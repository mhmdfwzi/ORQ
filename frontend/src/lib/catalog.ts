import { getBrandStory, getGiftBundle, getProducts } from '@/lib/api';
import { GIFT_BUNDLE, PRODUCTS } from '@/data/products';
import { BRAND_STORY } from '@/data/brand';
import type { Product } from '@/types/product';
import type { BrandStory } from '@/types/brand';

export interface Catalog {
  products: Product[];
  giftBundle: Product;
  brand: BrandStory;
}

/**
 * Server-side catalogue loader: Laravel API first, legacy hardcoded data as a
 * safety net so the storefront never goes blank if the API is unreachable.
 * The fallback lives outside the cached functions, so it is never cached.
 */
export async function getCatalog(): Promise<Catalog> {
  const [products, giftBundle, brand] = await Promise.allSettled([
    getProducts(),
    getGiftBundle(),
    getBrandStory(),
  ]);

  const list = products.status === 'fulfilled' ? products.value : [];
  if (products.status === 'rejected') {
    console.error('[catalog] API unreachable, using legacy fallback', products.reason);
  } else if (list.length === 0) {
    console.error('[catalog] API returned no products, using legacy fallback');
  }

  return {
    products: list.length > 0 ? list : PRODUCTS,
    giftBundle: (giftBundle.status === 'fulfilled' && giftBundle.value) || GIFT_BUNDLE,
    brand: (brand.status === 'fulfilled' && brand.value) || BRAND_STORY,
  };
}
