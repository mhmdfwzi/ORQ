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
- Guest checkout uses `POST /api/v1/orders`. The server recalculates prices and stock from the submitted product slugs, sizes and quantities. Cash on delivery is the default. Paymob orders also require a billing email and a unique checkout key.
- Set the shipping fee under Filament → إعدادات الموقع → إعدادات الشحن والدفع.

## Paymob Egypt

1. In `backend/.env`, set `PAYMOB_SECRET_KEY`, `PAYMOB_PUBLIC_KEY`, `PAYMOB_INTEGRATION_ID` (Egypt card integration), and `PAYMOB_HMAC_SECRET` from the same Paymob account. Start with test keys and the test integration ID.
2. Set `APP_URL` and `FRONTEND_URL` to the public HTTPS origins for Laravel and the storefront. The Intention request supplies the Laravel webhook at `/api/v1/payments/paymob/webhook` and the per-order return URL. The server verifies the transaction HMAC and matches the Paymob order ID, amount, currency and integration.
3. Run the database migrations and set `PAYMOB_ENABLED=true`. Refresh Laravel config and rebuild or revalidate the storefront so it can pick up the new option. Test an approved and a declined card through Paymob before switching to live credentials.
4. Run Laravel's scheduler every minute in production (`php artisan schedule:run`). The store reserves stock while Paymob Checkout is open for 15 minutes, with another 30 minutes for callback delivery. The scheduled task releases expired reservations. A verified payment arriving after release is flagged for admin review.

Use the Paymob transaction reference on the order to reconcile or refund a payment in the Paymob dashboard. Update the order's payment status in Filament once the refund is confirmed.

Provider documentation: [Create an intention](https://developers.paymob.com/paymob-docs/intention-apis/create-intention), [transaction HMAC](https://developers.paymob.com/paymob-docs/developers/webhook-callbacks-and-hmac/hmac/hmac-transaction-callback).

## Contact and store pages

Contact messages appear in the Filament notification bell for admin users, with the sender's email. Set the store's published telephone, WhatsApp, email, address and hours in Filament → Contact Settings. The form is available regardless of which contact details are published.

The Arabic store pages are under `/pages/` and linked from the footer. Review the published delivery estimates and fill in the store's business details before launch.


## Deploying / updating the server

One command, run on the server from the repo root:

```bash
bash scripts/deploy.sh                # git pull + backend + frontend + health checks
bash scripts/deploy.sh --backend-only # or --frontend-only
```

What it does: preflight (required env keys) ? `git pull --ff-only` ? backend: maintenance mode, `composer install --no-dev`,
`migrate --force`, cache config/routes/views/events (+ `filament:optimize`) ? frontend: `npm ci`, `npm run build`,
`pm2 reload orq` ? health checks on `/api/v1/site` and the deployment ID on the public storefront.

First-time server setup:
1. `backend/.env` from `.env.example` (`APP_KEY`, DB, `APP_URL`, `FRONTEND_URL`, `REVALIDATE_SECRET`, mail/WhatsApp keys if used).
2. `frontend/.env.production` from `frontend/.env.example` (same `REVALIDATE_SECRET`, real API/site URLs).
3. Serve `backend/public` with nginx/Apache + PHP-FPM. Choose a free port for Next.js, point the ORQ reverse proxy to it, and disable proxy caching for storefront HTML. Install `pm2` (`npm i -g pm2`).
4. Set `FRONTEND_PORT` to that port and run `bash scripts/deploy.sh --seed` once. Create an admin user (`role = admin`).

Notes: the frontend build prerenders from the API, so the backend must be reachable first (the script checks this).
Never re-run `--seed` on a live shop: it resets seeded prices/images. Settings (site, brand story) and products are edited in Filament.
