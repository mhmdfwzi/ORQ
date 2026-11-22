import products from '@/data/product-editorial.json';
import type { Product } from '@/types/product';

export type ProductEditorial = (typeof products)[number];

export function getProductEditorial(slug: string) {
  return products.find((item) => item.slug === slug);
}

export function withProductEditorial(product: Product): Product {
  const editorial = getProductEditorial(product.slug);
  const sourcePhotos = editorial?.photos ?? (product.slug === 'bundle-gift-box' ? ['/media/orq/catalog/bundle-gift-box-1.webp'] : []);
  const isLegacyImage = (photo: string) => photo === `/media/orq/${product.slug}.webp`;
  const useSourcePhotos = !product.image || isLegacyImage(product.image);
  const image = useSourcePhotos ? sourcePhotos[0] ?? product.image : product.image;
  const gallery = [...new Set([
    image,
    ...(product.gallery ?? []).map((photo) => isLegacyImage(photo) ? sourcePhotos[0] ?? photo : photo),
    ...(useSourcePhotos ? sourcePhotos : []),
  ].filter(Boolean))];

  return {
    slug: product.slug,
    sku: product.sku,
    name: editorial?.name ?? product.name,
    arabicName: product.arabicName,
    subtitle: editorial?.tagline ?? product.subtitle,
    category: product.category,
    scentType: editorial?.family ?? product.scentType,
    description: product.description,
    mood: product.mood,
    notes: {
      top: editorial?.notes.top ?? product.notes.top,
      heart: editorial?.notes.heart ?? product.notes.heart,
      base: editorial?.notes.base ?? product.notes.base,
    },
    variants: product.variants.map(({ size, price, compareAtPrice, inStock }) => ({ size, price, compareAtPrice, inStock })),
    image,
    gallery,
    isBestSeller: product.isBestSeller,
    isNew: product.isNew,
  };
}
