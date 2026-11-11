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
