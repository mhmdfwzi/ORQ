import { cacheLife, cacheTag } from 'next/cache';
import type { Product } from '@/types/product';
import type { BrandStory } from '@/types/brand';

/**
 * Data layer for the ORQ Laravel API (server-side only).
 *
 * Every function uses the Next 16 `'use cache'` directive and the shared
 * `products` tag. Filament triggers `POST /api/revalidate` after any product
 * change, which calls `revalidateTag('products', 'max')` so edits go live
 * without a rebuild.
 */
const API_URL = process.env.API_URL ?? 'http://127.0.0.1:8001/api/v1';

async function fetchJson<T>(path: string): Promise<T | null> {
  const res = await fetch(`${API_URL}${path}`, { headers: { Accept: 'application/json' } });
  if (res.status === 404) return null;
  if (!res.ok) throw new Error(`ORQ API ${path} failed with status ${res.status}`);
  const body = (await res.json()) as { data: T };
  return body.data;
}

export async function getProducts(): Promise<Product[]> {
  'use cache';
  cacheLife('hours');
  cacheTag('products');
  return (await fetchJson<Product[]>('/products')) ?? [];
}

export async function getProductBySlug(slug: string): Promise<Product | null> {
  'use cache';
  cacheLife('hours');
  cacheTag('products');
  return fetchJson<Product>(`/products/${encodeURIComponent(slug)}`);
}

export async function getProductsByMood(mood: string): Promise<Product[]> {
  'use cache';
  cacheLife('hours');
  cacheTag('products');
  return (await fetchJson<Product[]>(`/products/mood/${encodeURIComponent(mood)}`)) ?? [];
}

export async function getGiftBundle(): Promise<Product | null> {
  'use cache';
  cacheLife('hours');
  cacheTag('products');
  return fetchJson<Product>('/products/bundle');
}

export async function getBestSellers(): Promise<Product[]> {
  'use cache';
  cacheLife('hours');
  cacheTag('products');
  return (await fetchJson<Product[]>('/products/best-sellers')) ?? [];
}

export async function getBrandStory(): Promise<BrandStory | null> {
  'use cache';
  cacheLife('hours');
  cacheTag('brand');
  return fetchJson<BrandStory>('/brand');
}
