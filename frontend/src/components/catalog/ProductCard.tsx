'use client';

import Link from 'next/link';
import type { Product } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { formatPrice } from '@/lib/format';

interface ProductCardProps {
  product: Product;
  showScentType?: boolean;
}

export default function ProductCard({ product, showScentType = false }: ProductCardProps) {
  const { addToCart } = useCart();
  const { commerce } = useCatalog().site;
  const variant = product.variants.find((item) => item.size === '50ml');
  const href = '/products/' + (product.pageSlug ?? product.slug);

  return (
    <article className={`group flex h-full min-w-0 flex-col rounded-xl border border-[#35291E] bg-[#16110E] p-2 transition hover:border-[#C4A265]/60 motion-safe:hover:-translate-y-1 sm:p-3 ${showScentType ? 'storefront-card-enter' : ''}`}>
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
        {showScentType && product.scentType ? (
          <p className="mt-1 text-xs leading-relaxed text-[#CDBA9F]">{product.scentType}</p>
        ) : product.arabicName && product.arabicName !== product.name && (
          <p className="mt-0.5 text-xs text-[#B9B1A5]">{product.arabicName}</p>
        )}
      </Link>

      <div className="mt-2 flex min-h-11 items-center justify-between gap-1.5">
        {variant && (
          <div className="flex min-w-0 flex-wrap items-baseline gap-x-2 gap-y-0.5 text-xs">
            <span className="font-bold text-[#ECC480]">{formatPrice(variant.price)} {commerce.currency}</span>
            <span className="whitespace-nowrap text-[#B9B1A5]">{parseInt(variant.size, 10)} مل</span>
          </div>
        )}
        {variant?.inStock ? (
          <button
            type="button"
            onClick={() => addToCart(product, variant.size)}
            aria-label={`أضف ${product.name} للسلة`}
            className="grid size-11 shrink-0 cursor-pointer place-items-center rounded-full border border-[#C4A265]/45 bg-[#C4A265]/10 text-[#ECC480] transition-colors hover:border-[#ECC480] hover:bg-[#ECC480] hover:text-[#16110E] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ECC480] active:bg-[#C4A265] active:text-[#16110E]"
          >
            <svg aria-hidden="true" className="size-5" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M5 8h14l2 13H3L5 8Zm4 2V6a3 3 0 0 1 6 0v4m-5 5h4m-2-2v4" />
            </svg>
          </button>
        ) : (
          <span className="ms-auto text-end text-[11px] text-[#B9B1A5]">غير متوفر</span>
        )}
      </div>
    </article>
  );
}
