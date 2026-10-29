'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { Product } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';
import ProductCard from '@/components/catalog/ProductCard';
import ScrollReveal from '@/components/ui/ScrollReveal';
import { PRODUCTS } from '@/data/products';
import { formatPrice } from '@/lib/format';

interface ProductDetailClientProps {
  product: Product;
}

export default function ProductDetailClient({ product }: ProductDetailClientProps) {
  const { addToCart } = useCart();
  const [selectedSize, setSelectedSize] = useState<'50ml' | '100ml'>('100ml');
  const [quantity, setQuantity] = useState(1);

  const selectedVariant = product.variants.find((v) => v.size === selectedSize) || product.variants[0];
  const relatedProducts = PRODUCTS.filter((p) => p.slug !== product.slug).slice(0, 4);

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12">
      
      {/* Breadcrumb with valid working routes */}
      <nav className="text-xs text-[#8E867B] flex items-center gap-2">
        <Link href="/preview/concept-1" className="hover:text-white transition">الرئيسية</Link>
        <span>/</span>
        <Link href="/preview/concept-1#signature" className="hover:text-white transition">المجموعة العطرية</Link>
        <span>/</span>
        <span className="text-[#ECC480] font-semibold">{product.name}</span>
      </nav>

      {/* Main PDP Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        {/* Left Side: Product Gallery */}
        <div className="lg:col-span-6 space-y-4">
          <ScrollReveal direction="right" delay={50}>
            <div className="aspect-[4/3] sm:aspect-square rounded-2xl bg-[#0A0A0A] border border-[#2B2723] overflow-hidden relative shadow-2xl group">
              <img
                src={product.image}
                alt={product.name}
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-95"
              />
              {product.isBestSeller && (
                <span className="absolute top-4 right-4 bg-[#C4A265] text-black font-extrabold text-xs px-3 py-1 rounded shadow-lg">
                  الأكثر طلباً
                </span>
              )}
            </div>

            <div className="p-4 rounded-xl bg-[#141210] border border-[#221F1C] flex items-center justify-between text-xs text-[#A0988C] mt-4">
              <span className="flex items-center gap-1.5 text-emerald-400 font-semibold">
                <span>●</span>
                <span>متاح للشحن الفوري لكافة المحافظات</span>
              </span>
              <span className="font-mono text-[11px] text-[#C4A265]">EDP • تركيز نيش فاخر</span>
            </div>
          </ScrollReveal>
        </div>

        {/* Right Side: Product Details & Buying Controls */}
        <div className="lg:col-span-6 space-y-6 text-right">
          <ScrollReveal direction="left" delay={100}>
            <div className="space-y-2 border-b border-[#221F1C] pb-5">
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold text-[#C4A265] tracking-widest uppercase">
                  {product.category}
                </span>
                <span className="text-xs text-zinc-400">
                  {product.brandFeel}
                </span>
              </div>

              <h1 className="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white">
                عطر {product.name}
                {product.arabicName && product.arabicName !== product.name && (
                  <span className="text-xl text-[#8E867B] font-normal mr-3">({product.arabicName})</span>
                )}
              </h1>

              <p className="text-xs sm:text-sm text-[#ECC480] font-medium">
                {product.subtitle}
              </p>

              <p className="text-xs sm:text-sm text-[#B9B1A5] leading-relaxed pt-2">
                {product.description}
              </p>

              <div className="pt-2 text-xs text-zinc-400">
                <span className="text-[#C4A265] font-semibold">الهوية العطرية: </span>
                <span>{product.originalInspiration}</span>
              </div>
            </div>

            {/* Size Variant Selector */}
            <div className="space-y-3 pt-4">
              <label className="text-xs font-bold text-white block">
                اختر حجم الزجاجة:
              </label>
              <div className="grid grid-cols-2 gap-3">
                {product.variants.map((v) => (
                  <button
                    key={v.size}
                    onClick={() => setSelectedSize(v.size)}
                    className={`p-3.5 rounded-xl border text-right transition flex items-center justify-between min-h-[44px] ${
                      selectedSize === v.size
                        ? 'bg-[#1C1814] border-[#C4A265] text-[#ECC480] shadow-md'
                        : 'bg-[#141210] border-[#2B2723] text-zinc-300 hover:border-[#3E3832]'
                    }`}
                  >
                    <div>
                      <div className="font-bold text-xs sm:text-sm">{v.size === '100ml' ? '100 مل (الحجم الكبير)' : '50 مل (حجم السفر)'}</div>
                      <div className="text-[10px] text-zinc-400">Eau de Parfum مكثف</div>
                    </div>
                    <div className="font-mono font-bold text-sm text-[#ECC480] whitespace-nowrap mr-2">
                      {formatPrice(v.price)} ج.م
                    </div>
                  </button>
                ))}
              </div>
            </div>

            {/* Price & Quantity & Add to Cart */}
            <div className="space-y-4 pt-4">
              <div className="flex items-baseline justify-between">
                <div>
                  <span className="text-xs text-zinc-400 ml-2">السعر الإجمالي:</span>
                  <span className="text-2xl sm:text-3xl font-extrabold text-[#ECC480] font-mono whitespace-nowrap">
                    {formatPrice(selectedVariant.price * quantity)} ج.م
                  </span>
                </div>
                <span className="text-xs text-emerald-400 font-semibold">شامل الضريبة + عينة مجانية</span>
              </div>

              <div className="flex gap-3">
                {/* Quantity selector */}
                <div className="flex items-center border border-[#332E28] rounded-xl bg-[#141210] px-1 shrink-0">
                  <button
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                    className="w-9 h-11 flex items-center justify-center text-zinc-400 hover:text-white text-base focus:outline-none"
                    aria-label="تقليل الكمية"
                  >
                    -
                  </button>
                  <span className="px-2 text-white font-mono text-sm font-bold">{quantity}</span>
                  <button
                    onClick={() => setQuantity(quantity + 1)}
                    className="w-9 h-11 flex items-center justify-center text-zinc-400 hover:text-white text-base focus:outline-none"
                    aria-label="زيادة الكمية"
                  >
                    +
                  </button>
                </div>

                {/* Add to Cart Button (Concise for 320px screens) */}
                <button
                  onClick={() => addToCart(product, selectedSize, quantity)}
                  className="flex-1 gold-button py-3.5 px-5 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 shadow-xl min-h-[44px]"
                >
                  <span>أضف للسلة</span>
                  <span>🛒</span>
                </button>
              </div>
            </div>

            {/* Scent Notes Breakdown */}
            <div className="p-4 rounded-xl bg-[#141210] border border-[#26221D] space-y-3 mt-6">
              <h3 className="text-xs font-bold text-[#ECC480] flex items-center gap-2">
                <span>✦</span>
                <span>الهرم العطري والمكونات النادرة:</span>
              </h3>

              <div className="space-y-2 text-xs">
                <div className="flex gap-2">
                  <span className="text-[#C4A265] font-bold min-w-16">القمة:</span>
                  <span className="text-zinc-300">{product.notes.top.join(' • ')}</span>
                </div>
                <div className="flex gap-2">
                  <span className="text-[#C4A265] font-bold min-w-16">القلب:</span>
                  <span className="text-zinc-300">{product.notes.heart.join(' • ')}</span>
                </div>
                <div className="flex gap-2">
                  <span className="text-[#C4A265] font-bold min-w-16">القاعدة:</span>
                  <span className="text-zinc-300">{product.notes.base.join(' • ')}</span>
                </div>
              </div>
            </div>

            {/* Guarantees Box */}
            <div className="grid grid-cols-2 gap-3 text-[11px] text-[#A0988C] pt-4">
              <div className="p-2.5 rounded-lg bg-[#110F0E] border border-[#201D1A] flex items-center gap-2">
                <span className="text-base">🛡️</span>
                <span>عينة تجربة مجانية مرفقة</span>
              </div>
              <div className="p-2.5 rounded-lg bg-[#110F0E] border border-[#201D1A] flex items-center gap-2">
                <span className="text-base">💵</span>
                <span>الدفع عند الاستلام نقداً</span>
              </div>
            </div>
          </ScrollReveal>
        </div>

      </div>

      {/* Related Perfumes */}
      <div className="pt-16 border-t border-[#221F1C] space-y-6">
        <ScrollReveal direction="up" delay={50}>
          <div className="flex items-center justify-between">
            <h2 className="text-lg sm:text-xl font-bold text-white">عطور أخرى قد تناسب ذوقك</h2>
            <Link href="/preview/concept-1#signature" className="text-xs text-[#C4A265] hover:underline">
              عرض كل المجموعة ←
            </Link>
          </div>
        </ScrollReveal>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {relatedProducts.map((p, idx) => (
            <ScrollReveal key={p.slug} delay={idx * 120} direction="up">
              <ProductCard product={p} />
            </ScrollReveal>
          ))}
        </div>
      </div>

    </div>
  );
}
