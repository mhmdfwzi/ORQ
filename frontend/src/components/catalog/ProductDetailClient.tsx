'use client';

import { useState } from 'react';
import Link from 'next/link';
import type { Product, ProductVariant } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';
import ProductCard from '@/components/catalog/ProductCard';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { formatPrice } from '@/lib/format';

export default function ProductDetailClient({ product }: { product: Product }) {
  const { products, site } = useCatalog();
  const { addToCart } = useCart();
  const defaultVariant = product.variants.find((variant) => variant.size === '100ml' && variant.inStock)
    ?? product.variants.find((variant) => variant.inStock)
    ?? product.variants[0];
  const [selectedSize, setSelectedSize] = useState<ProductVariant['size'] | undefined>(defaultVariant?.size);
  const [quantity, setQuantity] = useState(1);
  const selectedVariant = product.variants.find((variant) => variant.size === selectedSize) ?? defaultVariant;
  const relatedProducts = products.filter((item) => item.slug !== product.slug).slice(0, 4);
  const { currency } = site.commerce;

  return (
    <div className="mx-auto max-w-7xl space-y-7 px-4 pb-9 sm:px-6 lg:px-8">
      <Link href="/#signature" className="inline-flex min-h-11 items-center gap-2 text-sm text-[#ECC480] hover:underline">
        <span aria-hidden="true">→</span> العطور
      </Link>

      <div className="grid items-start gap-5 lg:grid-cols-2 lg:gap-12">
        {product.image && (
          // Admin-uploaded product images can use any host.
          // eslint-disable-next-line @next/next/no-img-element
          <img src={product.image} alt={product.name} fetchPriority="high" className="aspect-square w-full max-h-[26rem] rounded-2xl border border-[#2B2723] bg-[#14110E] object-contain lg:max-h-none" />
        )}

        <div className="min-w-0 space-y-5">
          <div className="space-y-1">
            <h1 className="text-2xl font-bold leading-tight text-white sm:text-4xl">{product.name}</h1>
            {product.arabicName && product.arabicName !== product.name && <p className="text-sm text-[#E2D3BC]">{product.arabicName}</p>}
          </div>

          {product.variants.length > 0 && (
            <fieldset className="min-w-0 space-y-2">
              <legend className="mb-2 text-sm font-bold text-white">الحجم</legend>
              <div className="grid grid-cols-2 gap-2">
                {product.variants.map((variant) => (
                  <button
                    key={variant.size}
                    type="button"
                    onClick={() => setSelectedSize(variant.size)}
                    aria-pressed={selectedVariant?.size === variant.size}
                    className={`flex min-h-11 flex-wrap items-center justify-between gap-x-2 rounded-lg border px-3 py-2 text-sm ${selectedVariant?.size === variant.size
                      ? 'border-[#C4A265] bg-[#211B14] text-[#ECC480]'
                      : 'border-[#332E28] bg-[#141210] text-[#E2D3BC]'}`}
                  >
                    <span>{parseInt(variant.size, 10)} مل</span>
                    <span className="font-bold">{variant.inStock ? `${formatPrice(variant.price)} ${currency}` : 'نفد'}</span>
                  </button>
                ))}
              </div>
            </fieldset>
          )}

          <div className="space-y-3">
            {selectedVariant && (
              <div className="flex flex-wrap items-baseline gap-x-3">
                <span className="text-2xl font-bold text-[#ECC480]">{formatPrice(selectedVariant.price * quantity)} {currency}</span>
                {selectedVariant.compareAtPrice && selectedVariant.compareAtPrice > selectedVariant.price && (
                  <del className="text-sm text-[#A0988C]">{formatPrice(selectedVariant.compareAtPrice * quantity)} {currency}</del>
                )}
              </div>
            )}
            <div className="flex gap-2">
              <div className="flex shrink-0 items-center rounded-lg border border-[#332E28] bg-[#141210]" role="group" aria-label="الكمية">
                <button type="button" disabled={quantity <= 1} onClick={() => setQuantity((count) => count - 1)} className="size-11 disabled:opacity-50" aria-label="تقليل الكمية">−</button>
                <span className="min-w-5 text-center text-sm font-bold">{quantity}</span>
                <button type="button" onClick={() => setQuantity((count) => count + 1)} className="size-11" aria-label="زيادة الكمية">+</button>
              </div>
              <button
                type="button"
                disabled={!selectedVariant?.inStock}
                onClick={() => selectedVariant && addToCart(product, selectedVariant.size, quantity)}
                className="gold-button min-h-11 flex-1 rounded-lg px-2 text-sm disabled:opacity-60"
              >
                {selectedVariant?.inStock ? 'أضف للسلة' : 'غير متوفر'}
              </button>
            </div>
          </div>

          <div className="divide-y divide-[#302820] rounded-xl border border-[#302820] bg-[#141210] px-3">
            <details>
              <summary className="min-h-11 cursor-pointer py-3 text-sm font-semibold text-white">عن العطر</summary>
              <div className="space-y-2 pb-3 text-sm leading-relaxed text-[#B9B1A5]">
                {product.subtitle && <p className="font-semibold text-[#E2D3BC]">{product.subtitle}</p>}
                <p>{product.description}</p>
                {product.originalInspiration && <p>{product.originalInspiration}</p>}
              </div>
            </details>
            <details>
              <summary className="min-h-11 cursor-pointer py-3 text-sm font-semibold text-white">النفحات العطرية</summary>
              <dl className="space-y-2 pb-3 text-sm text-[#B9B1A5]">
                {([{ key: 'top', label: 'القمة' }, { key: 'heart', label: 'القلب' }, { key: 'base', label: 'القاعدة' }] as const).map(({ key, label }) => (
                  <div key={key} className="grid grid-cols-[3.5rem_1fr] gap-2">
                    <dt className="font-bold text-[#ECC480]">{label}</dt>
                    <dd>{product.notes[key].join('، ') || '—'}</dd>
                  </div>
                ))}
              </dl>
            </details>
          </div>
        </div>
      </div>

      {relatedProducts.length > 0 && (
        <section className="space-y-4 border-t border-[#221F1C] pt-6">
          <h2 className="text-lg font-bold text-white sm:text-2xl">عطور أخرى</h2>
          <div className="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
            {relatedProducts.map((item) => <ProductCard key={item.slug} product={item} />)}
          </div>
        </section>
      )}
    </div>
  );
}
