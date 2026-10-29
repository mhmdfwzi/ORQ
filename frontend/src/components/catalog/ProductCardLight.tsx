'use client';

import React from 'react';
import Link from 'next/link';
import { Product } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';
import { formatPrice } from '@/lib/format';

interface ProductCardProps {
  product: Product;
}

export default function ProductCardLight({ product }: ProductCardProps) {
  const { addToCart } = useCart();
  const defaultVariant = product.variants.find((v) => v.size === '100ml') || product.variants[0];

  return (
    <div className="bg-[#FFFFFF] border border-[#EAE4D8] rounded-2xl p-3 sm:p-4 flex flex-col justify-between hover:border-[#C4A265] transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group hover:-translate-y-1.5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_16px_45px_rgba(180,140,70,0.15)]">
      
      {/* Clickable Image & Badges */}
      <Link
        href={`/preview/products/${product.slug}`}
        className="block relative aspect-[4/3] sm:aspect-[3/4] bg-[#F7F4EE] rounded-xl overflow-hidden border border-[#EFE9DF] mb-3"
      >
        <img
          src={product.image}
          alt={product.name}
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-95 group-hover:opacity-100"
        />

        {product.isBestSeller && (
          <span className="absolute top-2 right-2 bg-gradient-to-r from-[#D4AF37] to-[#B38530] text-white font-extrabold text-[10px] px-2.5 py-0.5 rounded shadow-sm">
            الأكثر طلباً
          </span>
        )}

        <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2.5">
          <span className="text-[11px] text-white font-semibold">عرض تفاصيل العطر ←</span>
        </div>
      </Link>

      {/* Info */}
      <div className="space-y-1.5 flex-1">
        <div className="flex items-center justify-between text-[11px] text-[#A67C37]">
          <span className="font-semibold uppercase tracking-wider">{product.category}</span>
          <span className="text-[#7A7369]">{product.brandFeel}</span>
        </div>

        <Link href={`/preview/products/${product.slug}`} className="block">
          <h3 className="text-sm sm:text-base font-bold text-[#1C1917] group-hover:text-[#A67C37] transition">
            {product.name}
            {product.arabicName && product.arabicName !== product.name && (
              <span className="text-xs text-[#7A7369] font-normal mr-2">({product.arabicName})</span>
            )}
          </h3>
        </Link>

        <p className="text-[11px] text-[#7A7369] line-clamp-1">
          {product.scentType}
        </p>

        {/* Pricing */}
        <div className="pt-2 flex items-baseline justify-between border-t border-[#F0ECE2] mt-2">
          <div>
            <span className="text-xs text-[#8C8478] ml-1">100 مل:</span>
            <span className="text-sm sm:text-base font-extrabold text-[#996515] font-mono whitespace-nowrap">
              {formatPrice(defaultVariant.price)} ج.م
            </span>
          </div>
          <span className="text-[10px] text-emerald-600 font-semibold">● متوفر بالمخزون</span>
        </div>
      </div>

      {/* Action Buttons */}
      <div className="pt-3 grid grid-cols-2 gap-2 mt-2">
        <Link
          href={`/preview/products/${product.slug}`}
          className="text-center py-2.5 px-2 rounded-xl border border-[#DCD5C7] hover:border-[#A67C37] text-[#44403C] hover:text-[#1C1917] bg-[#FDFCFA] text-xs font-semibold transition flex items-center justify-center min-h-[40px]"
        >
          التفاصيل
        </Link>
        <button
          onClick={() => addToCart(product, '100ml')}
          className="py-2.5 px-2 rounded-xl bg-gradient-to-r from-[#C4A265] via-[#B38734] to-[#996515] text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-md shadow-[#B38734]/20 hover:brightness-105 min-h-[40px]"
        >
          <span>شراء</span>
          <span className="text-[10px]">🛒</span>
        </button>
      </div>

    </div>
  );
}

