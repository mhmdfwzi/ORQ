#!/usr/bin/env bash
# ORQ one-command server update.
#
#   ./scripts/deploy.sh                 # pull + update backend + frontend
#   ./scripts/deploy.sh --seed          # first deploy only: also seed the catalogue
#   ./scripts/deploy.sh --backend-only
#   ./scripts/deploy.sh --frontend-only
#   ./scripts/deploy.sh --branch main
#
# Run it from the repository root on the server (or from anywhere: it locates the root itself).
# Config via environment variables:
#   FRONTEND_PORT  (default 3000)      PM2_APP (default orq-frontend)
#   PHP_BIN        (default php)       COMPOSER_BIN (default composer)
#   API_HEALTH_URL (default http://127.0.0.1:8001/api/v1/site)
#   WEB_HEALTH_URL (default http://127.0.0.1:${FRONTEND_PORT}/)
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
FRONTEND_PORT="${FRONTEND_PORT:-3000}"
PM2_APP="${PM2_APP:-orq-frontend}"
# Web server user that must own storage/ and bootstrap/cache (aaPanel uses "www").
WEB_USER="${WEB_USER:-$(id -u www >/dev/null 2>&1 && echo www || true)}"
WEB_HEALTH_URL="${WEB_HEALTH_URL:-http://127.0.0.1:${FRONTEND_PORT}/}"
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
need git; need "$PHP_BIN"; need curl
[[ $DO_BACKEND -eq 1 ]] && need "$COMPOSER_BIN"
[[ $DO_FRONTEND -eq 1 ]] && { need node; need npm; }

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
    API_HEALTH_URL="$(grep '^API_URL=' "$FENV" | head -1 | cut -d= -f2- | tr -d '"' | sed 's#/*$##')/site"
  fi
fi
[[ -n "$API_HEALTH_URL" ]] || API_HEALTH_URL="http://127.0.0.1:8001/api/v1/site"
ok "environment looks sane (API health: $API_HEALTH_URL)"

# ---------------------------------------------------------------- code
log "Pulling latest code ($BRANCH)"
cd "$ROOT"
git config core.fileMode false || true
git reset --hard HEAD >/dev/null 2>&1 || true
git fetch --prune origin
git checkout "$BRANCH"
git pull --ff-only origin "$BRANCH"
ok "now at $(git log --oneline -1)"

# ---------------------------------------------------------------- backend
if [[ $DO_BACKEND -eq 1 ]]; then
  log "Backend (Laravel)"
  cd "$BACKEND"
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
  npm run build

  if command -v pm2 >/dev/null 2>&1; then
    if pm2 describe "$PM2_APP" >/dev/null 2>&1; then
      pm2 reload "$PM2_APP" --update-env
    else
      pm2 start npm --name "$PM2_APP" -- start -- -p "$FRONTEND_PORT"
    fi
    pm2 save >/dev/null 2>&1 || true
    ok "frontend restarted via pm2 ($PM2_APP)"
  else
    echo "pm2 not found — restart the Next.js process yourself (npm run start -- -p $FRONTEND_PORT)."
  fi
fi

# ---------------------------------------------------------------- health
log "Health checks"
if [[ $DO_BACKEND -eq 1 ]]; then
  curl -fsS --max-time 20 "$API_HEALTH_URL" >/dev/null && ok "API   $API_HEALTH_URL" || die "API health check failed"
fi
if [[ $DO_FRONTEND -eq 1 ]]; then
  sleep 3
  curl -fsS --max-time 30 "$WEB_HEALTH_URL" >/dev/null && ok "Web   $WEB_HEALTH_URL" || die "Frontend health check failed"
fi

log "Deploy finished: $(cd "$ROOT" && git log --oneline -1)"

