'use client';

import React from 'react';
import Link from 'next/link';
import { Product } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';
import { formatPrice } from '@/lib/format';

interface ProductCardProps {
  product: Product;
}

export default function ProductCard({ product }: ProductCardProps) {
  const { addToCart } = useCart();
  const defaultVariant = product.variants.find((v) => v.size === '100ml') || product.variants[0];

  return (
    <div className="bg-[#12100E] border border-[#24211D] rounded-2xl p-3 sm:p-4 flex flex-col justify-between hover:border-[#C4A265]/70 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group hover:-translate-y-1.5 hover:shadow-[0_12px_40px_rgba(196,162,101,0.18)]">
      
      {/* Clickable Image & Badges */}
      <Link href={`/preview/products/${product.slug}`} className="block relative aspect-[4/3] sm:aspect-[3/4] bg-[#0A0A0A] rounded-lg overflow-hidden border border-[#1E1B18] mb-3">
        <img
          src={product.image}
          alt={product.name}
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-90 group-hover:opacity-100"
        />

        {product.isBestSeller && (
          <span className="absolute top-2 right-2 bg-[#C4A265] text-black font-extrabold text-[10px] px-2 py-0.5 rounded shadow">
            الأكثر طلباً
          </span>
        )}

        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2.5">
          <span className="text-[11px] text-[#ECC480] font-medium">عرض كافة التفاصيل والنفحات ←</span>
        </div>
      </Link>

      {/* Info */}
      <div className="space-y-1.5 flex-1">
        <div className="flex items-center justify-between text-[11px] text-[#C4A265]">
          <span className="font-semibold uppercase tracking-wider">{product.category}</span>
          <span className="text-zinc-400">{product.brandFeel}</span>
        </div>

        <Link href={`/preview/products/${product.slug}`} className="block">
          <h3 className="text-sm sm:text-base font-bold text-white group-hover:text-[#ECC480] transition">
            {product.name}
            {product.arabicName && product.arabicName !== product.name && (
              <span className="text-xs text-[#8E867B] font-normal mr-2">({product.arabicName})</span>
            )}
          </h3>
        </Link>

        <p className="text-[11px] text-[#8E867B] line-clamp-1">
          {product.scentType}
        </p>

        {/* Pricing */}
        <div className="pt-2 flex items-baseline justify-between border-t border-[#1F1C19] mt-2">
          <div>
            <span className="text-xs text-zinc-500 ml-1">100 مل:</span>
            <span className="text-sm sm:text-base font-bold text-[#ECC480] font-mono">
              {formatPrice(defaultVariant.price)} ج.م
            </span>
          </div>
          <span className="text-[10px] text-emerald-400">● متوفر</span>
        </div>
      </div>

      {/* Action Buttons */}
      <div className="pt-3 grid grid-cols-2 gap-2 mt-2">
        <Link
          href={`/preview/products/${product.slug}`}
          className="text-center py-2 px-2 rounded-lg border border-[#332E28] hover:border-[#C4A265] text-zinc-300 hover:text-white text-xs font-medium transition"
        >
          التفاصيل
        </Link>
        <button
          onClick={() => addToCart(product, '100ml')}
          className="py-2 px-2 rounded-lg gold-button text-xs font-bold transition flex items-center justify-center gap-1"
        >
          <span>شراء</span>
          <span className="text-[10px]">🛒</span>
        </button>
      </div>

    </div>
  );
}
