import { getBrandStory, getGiftBundle, getProducts, getSiteSettings } from '@/lib/api';
import type { Product } from '@/types/product';
import type { BrandStory } from '@/types/brand';
import type { SiteSettings } from '@/types/site';

export interface Catalog {
  products: Product[];
  giftBundle: Product;
  brand: BrandStory;
  site: SiteSettings;
}

/**
 * Server-side loader: every piece of storefront data comes from the Laravel
 * database through the API. There is intentionally no hardcoded fallback —
 * if the API is unreachable the cached copy is served, and a cold start fails
 * loudly instead of showing stale invented data.
 */
export async function getCatalog(): Promise<Catalog> {
  const [products, giftBundle, brand, site] = await Promise.all([
    getProducts(),
    getGiftBundle(),
    getBrandStory(),
    getSiteSettings(),
  ]);

  if (products.length === 0) throw new Error('[catalog] API returned no products');
  if (!giftBundle) throw new Error('[catalog] API returned no gift bundle');
  if (!brand) throw new Error('[catalog] API returned no brand story');
  if (!site) throw new Error('[catalog] API returned no site settings');

  return { products, giftBundle, brand, site };
}
