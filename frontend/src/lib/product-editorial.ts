import products from '@/data/product-editorial.json';
import type { Product } from '@/types/product';

export type ProductEditorial = (typeof products)[number];

export function getProductEditorial(slug: string) {
  return products.find((item) => item.slug === slug || item.old_slug === slug);
}

export function withProductEditorial(product: Product): Product {
  const editorial = getProductEditorial(product.slug);
  const sourcePhotos = editorial?.photos ?? (product.slug === 'bundle-gift-box' ? ['/media/orq/catalog/bundle-gift-box-1.webp'] : []);
  const isLegacyImage = (photo: string) => photo === `/media/orq/${product.slug}.webp`
    || (editorial?.old_slug && photo === `/media/orq/${editorial.old_slug}.webp`);
  const useSourcePhotos = !product.image || isLegacyImage(product.image);
  const image = useSourcePhotos ? sourcePhotos[0] ?? product.image : product.image;
  const gallery = [...new Set([
    image,
    ...(product.gallery ?? []).map((photo) => isLegacyImage(photo) ? sourcePhotos[0] ?? photo : photo),
    ...(useSourcePhotos ? sourcePhotos : []),
  ].filter(Boolean))];

  if (!editorial) return { ...product, image, gallery };

  return {
    ...product,
    pageSlug: editorial.slug,
    name: editorial.name,
    arabicName: editorial.old_slug ? editorial.name : product.arabicName,
    subtitle: editorial.tagline,
    scentType: editorial.family,
    notes: editorial.notes,
    image,
    gallery,
  };
}
