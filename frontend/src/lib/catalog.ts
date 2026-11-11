import { getGiftBundle, getProducts } from '@/lib/api';
import { GIFT_BUNDLE, PRODUCTS } from '@/data/products';
import type { Product } from '@/types/product';

export interface Catalog {
  products: Product[];
  giftBundle: Product;
}

/**
 * Server-side catalogue loader: Laravel API first, legacy hardcoded data as a
 * safety net so the storefront never goes blank if the API is unreachable.
 * The fallback lives outside the cached functions, so it is never cached.
 */
export async function getCatalog(): Promise<Catalog> {
  try {
    const [products, giftBundle] = await Promise.all([getProducts(), getGiftBundle()]);
    if (products.length > 0) {
      return { products, giftBundle: giftBundle ?? GIFT_BUNDLE };
    }
    console.error('[catalog] API returned no products, using legacy fallback');
  } catch (error) {
    console.error('[catalog] API unreachable, using legacy fallback', error);
  }
  return { products: PRODUCTS, giftBundle: GIFT_BUNDLE };
}

