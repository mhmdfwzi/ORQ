'use client';

import React, { useState } from 'react';
import HeaderLight from '@/components/layout/HeaderLight';
import FooterLight from '@/components/layout/FooterLight';
import ProductCardLight from '@/components/catalog/ProductCardLight';
import MoodFilterBarLight from '@/components/cinematic/MoodFilterBarLight';
import SeamlessWhiteGoldHero from '@/components/cinematic/SeamlessWhiteGoldHero';
import ScrollReveal from '@/components/ui/ScrollReveal';
import { PRODUCTS, GIFT_BUNDLE, getProductsByMood } from '@/data/products';
import { ScentMood } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';

export default function ConceptFiveView() {
  const { addToCart } = useCart();
  const [selectedMood, setSelectedMood] = useState<ScentMood>('all');

  const curatedProducts = PRODUCTS.slice(0, 4);
  const filteredProducts = getProductsByMood(selectedMood);

  return (
    <div className="bg-[#FAF8F5] text-[#1C1917] min-h-screen font-sans selection:bg-[#ECC480] selection:text-black">
      <HeaderLight />

      {/* S1: White & Gold Seamless Atelier Hero */}
      <SeamlessWhiteGoldHero />

      {/* S2: Curated Masterpieces Grid */}
      <section id="signature" className="py-16 border-b border-[#E8E2D5] bg-[#FAF8F5] scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          <ScrollReveal direction="up" delay={50}>
            <div className="flex flex-col sm:flex-row sm:items-end justify-between border-b border-[#E2DBD0] pb-4 gap-2">
              <div>
                <span className="text-[11px] font-mono tracking-widest text-[#996515] uppercase block font-semibold">
                  CURATED LUXURY SELECTION • WHITE EDITION
                </span>
                <h2 className="text-xl sm:text-3xl font-extrabold text-[#1C1917] mt-1">
                  المجموعة العطرية المختارة — حضور راقٍ واستثنائي
                </h2>
                <p className="text-xs sm:text-sm text-[#6B645A] mt-1">
                  تركيبات نقية صُممت بأيدي كبار صانعي العطور لتمنحك كاريزما واثقة وهادئة.
                </p>
              </div>
              <span className="text-xs text-[#80581B] font-mono font-bold bg-[#EFE9DD] px-3 py-1 rounded-full border border-[#DDD5C5]">
                12 عطراً نيش معتقاً
              </span>
            </div>
          </ScrollReveal>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {curatedProducts.map((product, idx) => (
              <ScrollReveal key={product.slug} delay={idx * 100} direction="up">
                <ProductCardLight product={product} />
              </ScrollReveal>
            ))}
          </div>
        </div>
      </section>

      {/* S3: Interactive Scent Mood Discovery */}
      <section id="moods" className="py-16 bg-[#F3EFEA] border-b border-[#E8E2D5] scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          <ScrollReveal direction="up" delay={50}>
            <div className="space-y-2">
              <span className="text-[11px] font-mono tracking-widest text-[#996515] uppercase block font-semibold">
                PERSONALIZED DISCOVERY
              </span>
              <h2 className="text-xl sm:text-3xl font-extrabold text-[#1C1917]">
                تسوق حسب طابع حضورك ومناسباتك
              </h2>
              <p className="text-xs sm:text-sm text-[#6B645A]">
                حدد الطابع الذي يعبر عنك لتصفية المجموعة فوراً إلى العطور الأنسب لشخصيتك:
              </p>
            </div>
          </ScrollReveal>

          {/* Interactive Mood Filter Bar in White */}
          <ScrollReveal direction="up" delay={100}>
            <MoodFilterBarLight
              currentMood={selectedMood}
              onSelectMood={(mood) => setSelectedMood(mood)}
            />
          </ScrollReveal>

          {/* Filtered Products Dynamic Grid */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {filteredProducts.map((product, idx) => (
              <ScrollReveal
                key={product.slug}
                delay={(idx % 4) * 80}
                direction="up"
              >
                <ProductCardLight product={product} />
              </ScrollReveal>
            ))}
          </div>
        </div>
      </section>

      {/* S4: Gifting Box Bundle (The 750 EGP Box in Ivory & Gold) */}
      <section id="gifting" className="py-16 bg-[#FAF8F5] border-b border-[#E8E2D5] scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <ScrollReveal direction="zoom" delay={100}>
            <div className="bg-gradient-to-l from-[#FFFFFF] via-[#FAF7F2] to-[#F5EFE6] border-2 border-[#DCD5C7] rounded-3xl p-6 sm:p-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center shadow-[0_15px_50px_rgba(180,140,70,0.12)]">
              
              <div className="lg:col-span-5 aspect-square rounded-2xl bg-white border border-[#EAE4D8] overflow-hidden relative shadow-lg">
                <img
                  src="/media/orq/52adf269-3af1-47ab-9da9-af148cbd6349.jpg"
                  alt="صندوق باقة الهدايا والتجربة المتكاملة"
                  className="w-full h-full object-cover"
                />
                <span className="absolute top-3 right-3 bg-red-600 text-white font-extrabold text-xs px-2.5 py-1 rounded shadow">
                  توفير 45%
                </span>
              </div>

              <div className="lg:col-span-7 space-y-4">
                <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EFE9DD] text-[#80581B] text-xs font-bold border border-[#DDD5C5]">
                  <span>🎁</span>
                  <span>باقة الهدايا والتجربة الفاخرة</span>
                </div>

                <h2 className="text-2xl sm:text-3xl font-extrabold text-[#1C1917]">
                  صندوق ORQ الأبيض الملكي: زجاجة 50 مل + معطر سيارة + 4 عينات نادرة
                </h2>

                <p className="text-xs sm:text-sm text-[#575147] leading-relaxed">
                  تجربة متكاملة في صندوق إهداء ملكي: اختر عطرك المفضل بحجم 50 مل، بالإضافة لمعطر سيارة فاخر بنفس الهوية، و 4 عينات نادرة لاستكشاف باقي تشكيلة الدار براحة تامة.
                </p>

                <div className="grid grid-cols-3 gap-3 py-2 text-center text-xs">
                  <div className="p-3 rounded-xl bg-white border border-[#EAE4D8] shadow-xs">
                    <span className="text-[#80581B] font-bold block">زجاجة 50 مل</span>
                    <span className="text-[10px] text-[#7A7369]">العطر من اختيارك</span>
                  </div>
                  <div className="p-3 rounded-xl bg-white border border-[#EAE4D8] shadow-xs">
                    <span className="text-[#80581B] font-bold block">معطر سيارة</span>
                    <span className="text-[10px] text-[#7A7369]">فوحان نيش فندقي</span>
                  </div>
                  <div className="p-3 rounded-xl bg-white border border-[#EAE4D8] shadow-xs">
                    <span className="text-[#80581B] font-bold block">4 عينات</span>
                    <span className="text-[10px] text-[#7A7369]">لتجربة المجموعة</span>
                  </div>
                </div>

                <div className="pt-3 flex flex-wrap items-center gap-4">
                  <button
                    onClick={() => addToCart(GIFT_BUNDLE, '50ml')}
                    className="bg-gradient-to-r from-[#C4A265] via-[#B38734] to-[#996515] text-white text-xs sm:text-sm px-8 py-3.5 rounded-full font-bold shadow-xl shadow-[#A37B34]/25 hover:brightness-105 hover:scale-103 transition-transform flex items-center gap-2 min-h-[44px]"
                  >
                    <span>اطلب الصندوق الملكي الآن بـ 750 ج.م</span>
                    <span>🛒</span>
                  </button>
                  <div className="text-xs text-[#6B645A]">
                    <span className="line-through ml-1.5 text-[#8C8478] font-mono">1,350 ج.م</span>
                    <span className="text-emerald-700 font-bold font-mono">وفر 600 ج.م اليوم</span>
                  </div>
                </div>
              </div>

            </div>
          </ScrollReveal>
        </div>
      </section>

      {/* S5: Brand Creed & Philosophy in Light Ivory */}
      <section className="py-20 bg-gradient-to-b from-[#F7F4EE] to-[#FAF8F5] text-center border-b border-[#E8E2D5]">
        <div className="max-w-3xl mx-auto px-4 space-y-4">
          <span className="font-serif tracking-[0.25em] text-xs text-[#80581B] uppercase block font-bold">
            ORQ HAUTE PARFUMERIE • WHITE ATELIER
          </span>
          <h3 className="text-2xl sm:text-3xl font-serif text-[#1C1917] leading-relaxed">
            &ldquo;حضورك له أثر لا يمحى من الذاكرة&rdquo;
          </h3>
          <p className="text-xs sm:text-sm text-[#575147] max-w-xl mx-auto leading-relaxed">
            نقاء المكونات، وتركيزات مكثفة تدوم لأكثر من 12 ساعة لتترك انطباعاً ساحراً يليق بذوقك الرفيع.
          </p>
        </div>
      </section>

      <FooterLight />
    </div>
  );
}

