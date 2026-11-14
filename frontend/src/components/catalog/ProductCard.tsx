'use client';

import Link from 'next/link';
import type { Product } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { formatPrice } from '@/lib/format';

interface ProductCardProps {
  product: Product;
}

export default function ProductCard({ product }: ProductCardProps) {
  const { addToCart } = useCart();
  const { commerce } = useCatalog().site;
  const variant = product.variants.find((v) => v.size === '100ml' && v.inStock)
    ?? product.variants.find((v) => v.inStock)
    ?? product.variants[0];
  const href = `/products/${product.slug}`;

  return (
    <article className="group flex h-full min-w-0 flex-col rounded-xl border border-[#2B2723] bg-[#12100E] p-2 transition hover:border-[#C4A265]/60 sm:p-3">
      <Link href={href} className="block flex-1 rounded-lg focus-visible:outline-[#ECC480]">
        <div className="relative aspect-square overflow-hidden rounded-lg bg-[#1A1714]">
          {product.image && (
            // Admin-uploaded product images can use any host.
            // eslint-disable-next-line @next/next/no-img-element
            <img src={product.image} alt="" loading="lazy" className="size-full object-cover transition duration-500 motion-safe:group-hover:scale-105" />
          )}
        </div>
        <h3 className="mt-2 text-sm font-bold leading-snug break-words text-white group-hover:text-[#ECC480] sm:text-base">
          <bdi>{product.name}</bdi>
        </h3>
        {product.arabicName && product.arabicName !== product.name && (
          <p className="mt-0.5 text-xs text-[#B9B1A5]">{product.arabicName}</p>
        )}
      </Link>

      {variant && (
        <div className="mt-2 flex flex-wrap items-baseline gap-x-2 text-xs">
          <span className="font-bold text-[#ECC480]">{formatPrice(variant.price)} {commerce.currency}</span>
          <span className="text-[#B9B1A5]">{parseInt(variant.size, 10)} مل</span>
        </div>
      )}
      <button
        type="button"
        onClick={() => variant && addToCart(product, variant.size)}
        disabled={!variant?.inStock}
        aria-label={variant?.inStock ? `أضف ${product.name} للسلة` : undefined}
        className="gold-button mt-2 min-h-11 w-full rounded-lg px-1 py-2 text-xs disabled:opacity-60 sm:text-sm"
      >
        {variant?.inStock ? 'أضف للسلة' : 'غير متوفر'}
      </button>
    </article>
  );
}
