#!/usr/bin/env bash
# ORQ one-command server update.
#
#   bash scripts/deploy.sh                 # pull + update backend + frontend
#   bash scripts/deploy.sh --seed          # first deploy only: also seed the catalogue
#   bash scripts/deploy.sh --backend-only
#   bash scripts/deploy.sh --frontend-only
#   bash scripts/deploy.sh --branch main
#
# Run it from the repository root on the server (or from anywhere: it locates the root itself).
# Config via environment variables:
#   FRONTEND_PORT  (required when creating a PM2 process)    PM2_APP (default orq)
#   PHP_BIN        (default php)       COMPOSER_BIN (default composer)
#   API_HEALTH_URL (default http://127.0.0.1:8001/api/v1/site)
#   WEB_HEALTH_URL (default homepage on NEXT_PUBLIC_SITE_URL or FRONTEND_URL)
set -Eeuo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKEND="$ROOT/backend"
FRONTEND="$ROOT/frontend"

BRANCH="main"
DO_BACKEND=1
DO_FRONTEND=1
DO_SEED=0

PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
FRONTEND_PORT="${FRONTEND_PORT:-}"
PM2_APP="${PM2_APP:-orq}"
# Web server user that must own storage/ and bootstrap/cache (aaPanel uses "www").
WEB_USER="${WEB_USER:-$(id -u www >/dev/null 2>&1 && echo www || true)}"
WEB_HEALTH_URL="${WEB_HEALTH_URL:-}"
# API_HEALTH_URL defaults to "<API_URL from the frontend env file>/site" (resolved after preflight).
API_HEALTH_URL="${API_HEALTH_URL:-}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --seed) DO_SEED=1 ;;
    --backend-only) DO_FRONTEND=0 ;;
    --frontend-only) DO_BACKEND=0 ;;
    --branch) BRANCH="${2:?--branch needs a value}"; shift ;;
    -h|--help) sed -n '2,15p' "${BASH_SOURCE[0]}"; exit 0 ;;
    *) echo "Unknown option: $1" >&2; exit 2 ;;
  esac
  shift
done

log()  { printf '\n\033[1;33m==> %s\033[0m\n' "$*"; }
ok()   { printf '\033[1;32m✔ %s\033[0m\n' "$*"; }
die()  { printf '\033[1;31m✘ %s\033[0m\n' "$*" >&2; exit 1; }
need() { command -v "$1" >/dev/null 2>&1 || die "Missing required command: $1"; }

env_value() {
  sed -n "s/^$1=//p" "$2" | sed -n '1p' | tr -d '\r"'
}

check_frontend_target() {
  local target
  target=$(pm2 jlist | node -e '
    const fs = require("node:fs");
    const [name, directory] = process.argv.slice(1);
    const matches = JSON.parse(fs.readFileSync(0, "utf8")).filter((process) => process.name === name);
    if (matches.length === 0) {
      console.log("missing");
    } else {
      const expected = fs.realpathSync(directory);
      const matchesDirectory = matches.every(({ pm2_env }) =>
        pm2_env?.pm_cwd && fs.realpathSync(pm2_env.pm_cwd) === expected);
      if (!matchesDirectory) process.exit(1);
      console.log("existing");
    }
  ' "$PM2_APP" "$FRONTEND") || die "Could not verify that PM2 app '$PM2_APP' runs from $FRONTEND."

  if [[ $target == missing ]]; then
    [[ $FRONTEND_PORT =~ ^[1-9][0-9]{0,4}$ ]] && (( FRONTEND_PORT <= 65535 )) \
      || die "PM2 app '$PM2_APP' is missing. For a first deployment, set FRONTEND_PORT to the free port configured for the ORQ proxy."
    need ss
    local listeners
    listeners=$(ss -H -ltn "sport = :$FRONTEND_PORT") || die "Could not check port $FRONTEND_PORT."
    [[ -z $listeners ]] || die "Port $FRONTEND_PORT is already in use."
  fi
  FRONTEND_TARGET=$target
}

serves_deployment() {
  local html
  html=$(curl -fsS --max-time 6 "$1") && [[ $html == *"data-dpl-id=\"$NEXT_DEPLOYMENT_ID\""* ]]
}

MAINTENANCE=0
cleanup() {
  local code=$?
  if [[ $MAINTENANCE -eq 1 ]]; then
    ( cd "$BACKEND" && "$PHP_BIN" artisan up >/dev/null 2>&1 ) && echo "Maintenance mode lifted."
  fi
  if [[ $code -ne 0 ]]; then
    printf '\n\033[1;31mDeploy FAILED (exit %s). The previous frontend build/process was left running where possible.\033[0m\n' "$code" >&2
  fi
}
trap cleanup EXIT

# ---------------------------------------------------------------- preflight
log "Preflight"
need git; need curl
[[ $DO_BACKEND -eq 1 ]] && { need "$PHP_BIN"; need "$COMPOSER_BIN"; }
[[ $DO_FRONTEND -eq 1 ]] && { need node; need npm; need pm2; }

[[ -f "$BACKEND/.env" ]] || die "backend/.env is missing (copy backend/.env.example and fill it in)."
grep -q '^APP_KEY=.\+' "$BACKEND/.env" || die "APP_KEY is empty in backend/.env (run: php artisan key:generate)."
for key in DB_DATABASE FRONTEND_URL REVALIDATE_SECRET; do
  grep -q "^${key}=.\+" "$BACKEND/.env" || die "${key} is missing or empty in backend/.env"
done
if [[ $DO_FRONTEND -eq 1 ]]; then
  FENV="$FRONTEND/.env.production"; [[ -f "$FENV" ]] || FENV="$FRONTEND/.env.local"
  [[ -f "$FENV" ]] || die "frontend env file missing (.env.production or .env.local) — see frontend/.env.example."
  for key in API_URL NEXT_PUBLIC_API_URL REVALIDATE_SECRET; do
    grep -q "^${key}=.\+" "$FENV" || die "${key} is missing in $(basename "$FENV")"
  done
  if [[ -z "$API_HEALTH_URL" ]]; then
    API_HEALTH_URL="$(env_value API_URL "$FENV" | sed 's#/*$##')/site"
  fi
  if [[ -z "$WEB_HEALTH_URL" ]]; then
    SITE_URL=$(env_value NEXT_PUBLIC_SITE_URL "$FENV")
    [[ -n "$SITE_URL" ]] || SITE_URL=$(env_value FRONTEND_URL "$BACKEND/.env")
    WEB_HEALTH_URL="${SITE_URL%/}/"
  fi
  [[ $WEB_HEALTH_URL =~ ^https?://[^/[:space:]]+(/[^[:space:]]*)?$ && $WEB_HEALTH_URL != *[\?\#]* ]] \
    || die "Set WEB_HEALTH_URL to the public ORQ homepage without query parameters."
  check_frontend_target
fi
[[ -n "$API_HEALTH_URL" ]] || API_HEALTH_URL="http://127.0.0.1:8001/api/v1/site"
ok "environment looks sane (API health: $API_HEALTH_URL)"

# ---------------------------------------------------------------- code
log "Pulling latest code ($BRANCH)"
cd "$ROOT"
git config core.fileMode false || true
git checkout "$BRANCH"
git pull --ff-only origin "$BRANCH"
ok "now at $(git log --oneline -1)"

# ---------------------------------------------------------------- backend
if [[ $DO_BACKEND -eq 1 ]]; then
  log "Backend (Laravel)"
  cd "$BACKEND"
  mkdir -p storage/framework/{sessions,views,cache/data} storage/logs bootstrap/cache
  "$PHP_BIN" artisan down --retry=60 >/dev/null 2>&1 && MAINTENANCE=1 || true

  "$COMPOSER_BIN" install --no-dev --prefer-dist --optimize-autoloader --no-interaction
  "$PHP_BIN" artisan migrate --force

  if [[ $DO_SEED -eq 1 ]]; then
    log "Seeding catalogue (first deploy only — re-running resets seeded prices/images)"
    "$PHP_BIN" artisan db:seed --class=ORQProductSeeder --force
  fi

  "$PHP_BIN" artisan storage:link >/dev/null 2>&1 || true
  "$PHP_BIN" artisan optimize:clear
  "$PHP_BIN" artisan config:cache
  "$PHP_BIN" artisan route:cache
  "$PHP_BIN" artisan view:cache
  "$PHP_BIN" artisan event:cache
  if "$PHP_BIN" artisan list --raw | grep -q '^filament:optimize '; then
    "$PHP_BIN" artisan filament:optimize
  fi

  # Artisan ran as the deploy user (often root): hand writable dirs back to the web user.
  if [[ -n "$WEB_USER" && $(id -u) -eq 0 ]]; then
    chown -R "$WEB_USER:$WEB_USER" storage bootstrap/cache
    chmod -R ug+rwX storage bootstrap/cache
  fi

  "$PHP_BIN" artisan up >/dev/null 2>&1 && MAINTENANCE=0
  ok "backend updated"
fi

# ---------------------------------------------------------------- frontend
if [[ $DO_FRONTEND -eq 1 ]]; then
  log "Frontend (Next.js)"
  # The build prerenders pages from the Laravel API, so the API must already be reachable.
  curl -fsS --max-time 20 "$API_HEALTH_URL" >/dev/null \
    || die "API not reachable at $API_HEALTH_URL — the frontend build needs it. Start/fix the backend first."

  cd "$FRONTEND"
  npm ci --no-audit --no-fund
  release_suffix=$(node -e 'console.log(require("node:crypto").randomBytes(6).toString("hex"))')
  export NEXT_DEPLOYMENT_ID="orq-$(git rev-parse --short HEAD)-$release_suffix"
  npm run build

  check_frontend_target
  if [[ $FRONTEND_TARGET == existing ]]; then
    pm2 reload "$PM2_APP" --update-env
  else
    pm2 start npm --name "$PM2_APP" -- start -- -p "$FRONTEND_PORT"
  fi
  ok "frontend restarted via pm2 ($PM2_APP)"
fi

# ---------------------------------------------------------------- health
log "Health checks"
if [[ $DO_BACKEND -eq 1 ]]; then
  curl -fsS --max-time 20 "$API_HEALTH_URL" >/dev/null && ok "API   $API_HEALTH_URL" || die "API health check failed"
fi
if [[ $DO_FRONTEND -eq 1 ]]; then
  ready=0
  for attempt in {1..8}; do
    if serves_deployment "$WEB_HEALTH_URL"; then
      ready=1
      break
    fi
    sleep 2
  done
  if [[ $ready -eq 0 ]]; then
    if serves_deployment "${WEB_HEALTH_URL}?orq_deployment=$NEXT_DEPLOYMENT_ID"; then
      die "The new version responds with a query parameter, but $WEB_HEALTH_URL is stale. Check the ORQ reverse proxy HTML cache."
    fi
    die "$WEB_HEALTH_URL did not serve $NEXT_DEPLOYMENT_ID. Check pm2 logs $PM2_APP and the ORQ proxy target."
  fi
  pm2 save >/dev/null 2>&1 || true
  ok "Web   $WEB_HEALTH_URL ($NEXT_DEPLOYMENT_ID)"
fi

log "Deploy finished: $(cd "$ROOT" && git log --oneline -1)"

