# ORQ integration notes

| Folder | Stack | Role |
|---|---|---|
| `frontend/` | Next.js 16 (cacheComponents) | Cinematic storefront |
| `backend/` | Laravel 12 + Filament 4 | Headless API + admin panel (`/admin`) |

## Run locally

**Backend** (port 8001; 8000 may be taken by other projects)
```bash
cd backend
cp .env.example .env && php artisan key:generate   # then set DB_DATABASE=orq
php artisan migrate
php artisan db:seed --class=ORQProductSeeder
cd public && php -S 127.0.0.1:8001 index.php
```
Admin panel: http://127.0.0.1:8001/admin (user needs `role = admin`).

**Frontend** (`frontend/.env.local`)
```
API_URL=http://127.0.0.1:8001/api/v1
NEXT_PUBLIC_API_URL=http://127.0.0.1:8001/api/v1
REVALIDATE_SECRET=<same value as backend REVALIDATE_SECRET>
```
```bash
cd frontend && npm install && npm run build && npm run start
```

## How the pieces talk
- Products: `GET /api/v1/products`, `/products/{slug}`, `/products/mood/{mood}`, `/products/best-sellers`, `/products/bundle`.
- Saving a product or variant in Filament calls `POST {FRONTEND_URL}/api/revalidate` (header `x-revalidate-token`), which revalidates the `products` cache tag. No rebuild needed.
- Guest checkout: `POST /api/v1/orders` (COD, no account). Only `slug/size/quantity` are trusted from the client; prices and stock are resolved server-side in a transaction. Throttled to 10/min per IP.
- Shipping fee: `ORQ_SHIPPING_COST` in backend `.env` (default 0 = free).


## Deploying / updating the server

One command, run on the server from the repo root:

```bash
./scripts/deploy.sh                # git pull + backend + frontend + health checks
./scripts/deploy.sh --seed         # FIRST deploy only: also seeds the catalogue
./scripts/deploy.sh --backend-only # or --frontend-only
```

What it does: preflight (required env keys) ? `git pull --ff-only` ? backend: maintenance mode, `composer install --no-dev`,
`migrate --force`, cache config/routes/views/events (+ `filament:optimize`) ? frontend: `npm ci`, `npm run build`,
`pm2 reload orq-frontend` ? health checks on `/api/v1/site` and the storefront.

First-time server setup:
1. `backend/.env` from `.env.example` (`APP_KEY`, DB, `APP_URL`, `FRONTEND_URL`, `REVALIDATE_SECRET`, mail/WhatsApp keys if used).
2. `frontend/.env.production` from `frontend/.env.example` (same `REVALIDATE_SECRET`, real API/site URLs).
3. Serve `backend/public` with nginx/Apache + PHP-FPM; keep Next.js on `FRONTEND_PORT` (default 3000) behind a reverse proxy. Needs `pm2` (`npm i -g pm2`).
4. Create an admin user (`role = admin`) and run `./scripts/deploy.sh --seed` once.

Notes: the frontend build prerenders from the API, so the backend must be reachable first (the script checks this).
Never re-run `--seed` on a live shop: it resets seeded prices/images. Settings (site, brand story) and products are edited in Filament.
