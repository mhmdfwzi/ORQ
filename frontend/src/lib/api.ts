import { cacheLife, cacheTag } from 'next/cache';
import type { Product } from '@/types/product';
import type { BrandStory } from '@/types/brand';
import type { SiteSettings } from '@/types/site';
import { getProductEditorial, withProductEditorial } from '@/lib/product-editorial';

/**
 * Data layer for the ORQ Laravel API (server-side only).
 *
 * Every function uses the Next 16 `'use cache'` directive and the shared
 * `products` tag. Filament triggers `POST /api/revalidate` after any product
 * change, which expires the `products` tag so edits go live
 * without a rebuild.
 */
const API_URL = process.env.API_URL ?? 'http://127.0.0.1:8001/api/v1';

async function fetchJson<T>(path: string): Promise<T | null> {
  // The enclosing tagged function caches the result. Avoid reusing fetch data across builds.
  const res = await fetch(`${API_URL}${path}`, { headers: { Accept: 'application/json' }, cache: 'no-store' });
  if (res.status === 404) return null;
  if (!res.ok) throw new Error(`ORQ API ${path} failed with status ${res.status}`);
  const body = (await res.json()) as { data: T };
  return body.data;
}

export async function getProducts(): Promise<Product[]> {
  'use cache';
  cacheLife('hours');
  cacheTag('products');
  return ((await fetchJson<Product[]>('/products')) ?? []).map(withProductEditorial);
}

export async function getProductBySlug(slug: string): Promise<Product | null> {
  'use cache';
  cacheLife('hours');
  cacheTag('products');
  const path = '/products/' + encodeURIComponent(slug);
  const alternate = getProductEditorial(slug)?.old_slug;
  const product = (await fetchJson<Product>(path))
    ?? (alternate && alternate !== slug ? await fetchJson<Product>('/products/' + encodeURIComponent(alternate)) : null);
  return product ? withProductEditorial(product) : null;
}

export async function getProductsByMood(mood: string): Promise<Product[]> {
  'use cache';
  cacheLife('hours');
  cacheTag('products');
  return ((await fetchJson<Product[]>('/products/mood/' + encodeURIComponent(mood))) ?? []).map(withProductEditorial);
}

export async function getGiftBundle(): Promise<Product | null> {
  'use cache';
  cacheLife('hours');
  cacheTag('products');
  const product = await fetchJson<Product>('/products/bundle');
  return product ? withProductEditorial(product) : null;
}

export async function getBestSellers(): Promise<Product[]> {
  'use cache';
  cacheLife('hours');
  cacheTag('products');
  return ((await fetchJson<Product[]>('/products/best-sellers')) ?? []).map(withProductEditorial);
}

export async function getBrandStory(): Promise<BrandStory | null> {
  'use cache';
  cacheLife('hours');
  cacheTag('brand');
  return fetchJson<BrandStory>('/brand');
}

export async function getSiteSettings(): Promise<SiteSettings | null> {
  'use cache';
  cacheLife('hours');
  cacheTag('site');
  return fetchJson<SiteSettings>('/site');
}
