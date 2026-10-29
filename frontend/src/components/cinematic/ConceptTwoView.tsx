'use client';

import React, { useState } from 'react';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import ProductCard from '@/components/catalog/ProductCard';
import MoodFilterBar from '@/components/cinematic/MoodFilterBar';
import CinematicAtomizerHero from '@/components/cinematic/CinematicAtomizerHero';
import ScrollReveal from '@/components/ui/ScrollReveal';
import { PRODUCTS, GIFT_BUNDLE, getProductsByMood } from '@/data/products';
import { ScentMood } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';

export default function ConceptTwoView() {
  const { addToCart } = useCart();
  const [selectedMood, setSelectedMood] = useState<ScentMood>('all');

  const filteredProducts = getProductsByMood(selectedMood);
  const featuredHero = PRODUCTS.find((p) => p.slug === 'alpha') || PRODUCTS[1];

  return (
    <div className="bg-[#070707] text-[#F2EBDD] min-h-screen">
      <Header />

      {/* S1: Clean Cinematic Atomizer Hero */}
      <CinematicAtomizerHero />

      {/* Scent Mood Selector & Fast Shop Grid */}
      <section id="signature" className="py-14 border-b border-[#221F1C]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          
          <ScrollReveal direction="up" delay={50}>
            <div className="space-y-2">
              <h2 className="text-xl sm:text-2xl font-bold text-white">
                تشكيلة العطور (تسوق سريع حسب شخصيتك)
              </h2>
              <p className="text-xs text-[#8E867B]">
                حدد الطابع العطري الذي تبحث عنه لعرض العطور المتوافقة معه فوراً:
              </p>
            </div>
          </ScrollReveal>

          {/* Interactive Mood Filter */}
          <ScrollReveal direction="up" delay={120}>
            <MoodFilterBar
              currentMood={selectedMood}
              onSelectMood={(mood) => setSelectedMood(mood)}
            />
          </ScrollReveal>

          {/* Filtered Products Grid with Staggered Entrance */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {filteredProducts.map((product, idx) => (
              <ScrollReveal
                key={product.slug}
                delay={(idx % 4) * 110}
                direction="up"
              >
                <ProductCard product={product} />
              </ScrollReveal>
            ))}
          </div>

        </div>
      </section>

      {/* Gifting & Bundle Offer (The 750 EGP Box) */}
      <section id="gifting" className="py-16 bg-[#0E0D0C] border-b border-[#221F1C]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <ScrollReveal direction="zoom" delay={100}>
            <div className="bg-gradient-to-l from-[#1C1712] via-[#161310] to-[#120F0D] border-2 border-[#C4A265]/40 rounded-2xl p-6 sm:p-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center shadow-2xl">
              
              <div className="lg:col-span-5 aspect-square rounded-xl bg-black border border-[#2B2723] overflow-hidden relative shadow-2xl">
                <img
                  src="/media/orq/52adf269-3af1-47ab-9da9-af148cbd6349.jpg"
                  alt="ORQ Gift Box Bundle"
                  className="w-full h-full object-cover"
                />
                <span className="absolute top-3 right-3 bg-red-600 text-white font-bold text-xs px-2.5 py-1 rounded shadow">
                  توفير 45%
                </span>
              </div>

              <div className="lg:col-span-7 space-y-4">
                <div className="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-[#C4A265]/20 text-[#ECC480] text-xs font-semibold border border-[#C4A265]/30">
                  <span>🎁</span>
                  <span>باقة الهدايا والتجربة المتكاملة</span>
                </div>

                <h2 className="text-2xl sm:text-3xl font-extrabold text-white">
                  باقة ORQ الفاخرة: عطر 50 مل + معطر سيارة + 4 عينات
                </h2>

                <p className="text-xs sm:text-sm text-[#B9B1A5] leading-relaxed">
                  كل ما تحتاجه في صندوق فاخر واحد: اختر أي عطر مفضل لديك بحجم 50 مل، بالإضافة إلى معطر سيارة فاخر من نفس الهوية، و 4 عينات عطرية لاكتشاف باقي المجموعة.
                </p>

                <div className="grid grid-cols-3 gap-3 py-2 text-center text-xs">
                  <div className="p-2.5 rounded bg-[#1A1815] border border-[#2C2723]">
                    <span className="text-[#ECC480] font-bold block">زجاجة 50 مل</span>
                    <span className="text-[10px] text-zinc-400">عطرك المفضل</span>
                  </div>
                  <div className="p-2.5 rounded bg-[#1A1815] border border-[#2C2723]">
                    <span className="text-[#ECC480] font-bold block">معطر سيارة</span>
                    <span className="text-[10px] text-zinc-400">ثبات يدوم طويلاً</span>
                  </div>
                  <div className="p-2.5 rounded bg-[#1A1815] border border-[#2C2723]">
                    <span className="text-[#ECC480] font-bold block">4 عينات مجانية</span>
                    <span className="text-[10px] text-zinc-400">لاكتشاف التشكيلة</span>
                  </div>
                </div>

                <div className="flex items-center gap-4 pt-3 border-t border-[#26221D]">
                  <div>
                    <div className="text-[10px] text-zinc-400 line-through">1,350 ج.م</div>
                    <div className="text-2xl font-bold text-[#ECC480] font-mono">750 ج.م</div>
                  </div>

                  <button
                    onClick={() => addToCart(GIFT_BUNDLE, '50ml')}
                    className="gold-button py-3 px-6 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 min-h-[44px]"
                  >
                    <span>طلب الباقة الآن بـ 750 ج.م</span>
                    <span>←</span>
                  </button>
                </div>

              </div>

            </div>
          </ScrollReveal>
        </div>
      </section>

      <Footer />
    </div>
  );
}
