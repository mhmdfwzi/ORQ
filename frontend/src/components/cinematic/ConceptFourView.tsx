'use client';

import React, { useState } from 'react';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import ProductCard from '@/components/catalog/ProductCard';
import MoodFilterBar from '@/components/cinematic/MoodFilterBar';
import ScentPyramid from '@/components/cinematic/ScentPyramid';
import SeamlessCinematicHero from '@/components/cinematic/SeamlessCinematicHero';
import ScrollReveal from '@/components/ui/ScrollReveal';
import { PRODUCTS, GIFT_BUNDLE, getProductsByMood } from '@/data/products';
import { ScentMood } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';
import { formatPrice } from '@/lib/format';

export default function ConceptFourView() {
  const { addToCart } = useCart();
  const [selectedMood, setSelectedMood] = useState<ScentMood>('all');

  const curatedProducts = PRODUCTS.slice(0, 4);
  const filteredProducts = getProductsByMood(selectedMood);
  const oudLine = PRODUCTS.filter((p) => p.mood === 'royal_oud');
  const signatureLine = PRODUCTS.filter((p) => p.mood !== 'royal_oud');

  return (
    <div className="bg-[#070707] text-[#F2EBDD] min-h-screen">
      <Header />

      {/* S1: The Cinematic Master Hero (Instant visibility, Royal Oud flacon, instant CTA) */}
      <SeamlessCinematicHero />

      {/* S2: Curated Bestsellers Grid (Immediate Discovery right after Hero) */}
      <section id="signature" className="py-14 border-b border-[#1E1B18] scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          <ScrollReveal direction="up" delay={50}>
            <div className="flex flex-col sm:flex-row sm:items-end justify-between border-b border-[#1E1B18] pb-4 gap-2">
              <div>
                <span className="text-[11px] font-mono tracking-widest text-[#C4A265] uppercase block">
                  TOP 4 SIGNATURE SELECTIONS
                </span>
                <h2 className="text-xl sm:text-2xl font-bold text-white mt-0.5">
                  أيقونات الحضور — العطور الأكثر طلباً
                </h2>
              </div>
              <span className="text-xs text-[#ECC480] font-mono">12 عطراً نيش معتقاً</span>
            </div>
          </ScrollReveal>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {curatedProducts.map((product, idx) => (
              <ScrollReveal key={product.slug} delay={idx * 100} direction="up">
                <ProductCard product={product} />
              </ScrollReveal>
            ))}
          </div>
        </div>
      </section>

      {/* S3: Interactive Scent Mood Discovery (From Concept 2 - Fast filtering) */}
      <section id="moods" className="py-16 bg-[#0B0A09] border-b border-[#1E1B18] scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          <ScrollReveal direction="up" delay={50}>
            <div className="space-y-2">
              <span className="text-[11px] font-mono tracking-widest text-[#C4A265] uppercase block">
                FIND YOUR SCENT PERSONA
              </span>
              <h2 className="text-xl sm:text-3xl font-extrabold text-white">
                استكشف مجموعتنا حسب شخصيتك وطابعك
              </h2>
              <p className="text-xs sm:text-sm text-[#A0988C]">
                اختر الطابع العطري لعرض التشكيلة المطابقة فوراً دون الحاجة للبحث الطويل:
              </p>
            </div>
          </ScrollReveal>

          {/* Interactive Mood Filter Bar */}
          <ScrollReveal direction="up" delay={100}>
            <MoodFilterBar
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
                <ProductCard product={product} />
              </ScrollReveal>
            ))}
          </div>
        </div>
      </section>

      {/* S4: Editorial Dual Lines (From Concept 3 - Royal Oud Dynasty vs Modern Signature) */}
      <section id="lines" className="py-16 border-b border-[#1E1B18] scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-14">
          
          {/* Line 1: Royal Oud Dynasty */}
          <div className="space-y-6">
            <ScrollReveal direction="up" delay={50}>
              <div className="flex flex-col sm:flex-row sm:items-end justify-between border-b border-[#282522] pb-4 gap-2">
                <div>
                  <span className="text-[10px] font-mono tracking-widest text-[#C4A265] uppercase block">
                    ROYAL OUD DYNASTY • سلالة العود الملكي
                  </span>
                  <h3 className="text-xl sm:text-2xl font-bold text-white mt-1">
                    أخشاب العود النادرة والجلود المعتقة
                  </h3>
                  <p className="text-xs text-zinc-400 mt-1">
                    خلطات شرقية مهيبة تمتاز بالعمق والفخامة للمناسبات الكبرى والسهرات.
                  </p>
                </div>
                <span className="text-xs text-[#ECC480] font-mono">{oudLine.length} عطور ملكية</span>
              </div>
            </ScrollReveal>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {oudLine.map((product, idx) => (
                <ScrollReveal key={product.slug} delay={idx * 90} direction="up">
                  <ProductCard product={product} />
                </ScrollReveal>
              ))}
            </div>
          </div>

          {/* Line 2: Modern Signature Line */}
          <div className="space-y-6 pt-4">
            <ScrollReveal direction="up" delay={50}>
              <div className="flex flex-col sm:flex-row sm:items-end justify-between border-b border-[#282522] pb-4 gap-2">
                <div>
                  <span className="text-[10px] font-mono tracking-widest text-[#C4A265] uppercase block">
                    MODERN SIGNATURE LINE • خط الحضور العصري
                  </span>
                  <h3 className="text-xl sm:text-2xl font-bold text-white mt-1">
                    الانتعاش الجذاب والجاذبية المعاصرة
                  </h3>
                  <p className="text-xs text-zinc-400 mt-1">
                    توقيعات يومية ذكية تمنحك حضوراً واثقاً في العمل والمواعيد الرسمية.
                  </p>
                </div>
                <span className="text-xs text-[#ECC480] font-mono">{signatureLine.length} عطور عصرية</span>
              </div>
            </ScrollReveal>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {signatureLine.slice(0, 4).map((product, idx) => (
                <ScrollReveal key={product.slug} delay={idx * 90} direction="up">
                  <ProductCard product={product} />
                </ScrollReveal>
              ))}
            </div>
          </div>

        </div>
      </section>

      {/* S5: Gifting Box Bundle (The 750 EGP Box - Verified Cart Integration) */}
      <section id="gifting" className="py-16 bg-[#0E0D0C] border-b border-[#221F1C] scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <ScrollReveal direction="zoom" delay={100}>
            <div className="bg-gradient-to-l from-[#1C1712] via-[#161310] to-[#120F0D] border-2 border-[#C4A265]/40 rounded-2xl p-6 sm:p-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center shadow-2xl">
              
              <div className="lg:col-span-5 aspect-square rounded-xl bg-black border border-[#2B2723] overflow-hidden relative shadow-2xl">
                <img
                  src="/media/orq/52adf269-3af1-47ab-9da9-af148cbd6349.jpg"
                  alt="صندوق باقة الهدايا الفاخرة من ORQ"
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
                  باقة ORQ الفاخرة: عطر 50 مل + معطر سيارة + 4 عينات نادرة
                </h2>

                <p className="text-xs sm:text-sm text-[#B9B1A5] leading-relaxed">
                  كل ما تحتاجه في صندوق هدايا فاخر واحد: اختر أي عطر مفضل لديك بحجم 50 مل، بالإضافة إلى معطر سيارة نيش فاخر من نفس الهوية، و 4 عينات عطرية لاكتشاف باقي تشكيلة ORQ.
                </p>

                <div className="grid grid-cols-3 gap-3 py-2 text-center text-xs">
                  <div className="p-2.5 rounded bg-[#1A1815] border border-[#2C2723]">
                    <span className="text-[#ECC480] font-bold block">زجاجة 50 مل</span>
                    <span className="text-[10px] text-zinc-400">أي عطر من اختيارك</span>
                  </div>
                  <div className="p-2.5 rounded bg-[#1A1815] border border-[#2C2723]">
                    <span className="text-[#ECC480] font-bold block">معطر سيارة</span>
                    <span className="text-[10px] text-zinc-400">نفس الهوية العطرية</span>
                  </div>
                  <div className="p-2.5 rounded bg-[#1A1815] border border-[#2C2723]">
                    <span className="text-[#ECC480] font-bold block">4 عينات</span>
                    <span className="text-[10px] text-zinc-400">لتجربة باقي المجموعة</span>
                  </div>
                </div>

                <div className="pt-3 flex flex-wrap items-center gap-4">
                  <button
                    onClick={() => addToCart(GIFT_BUNDLE, '50ml')}
                    className="gold-button text-xs sm:text-sm px-8 py-3.5 rounded-full font-bold shadow-xl shadow-[#C4A265]/25 hover:scale-105 transition-transform flex items-center gap-2 min-h-[44px]"
                  >
                    <span>طلب الباقة الآن بـ 750 ج.م فقط</span>
                    <span>🛒</span>
                  </button>
                  <div className="text-xs text-[#A0988C]">
                    <span className="line-through ml-1.5 text-zinc-500 font-mono">1,350 ج.م</span>
                    <span className="text-emerald-400 font-semibold font-mono">وفر 600 ج.م</span>
                  </div>
                </div>
              </div>

            </div>
          </ScrollReveal>
        </div>
      </section>

      {/* S6: The Olfactory Architecture (Scent Pyramid) */}
      <section id="notes" className="py-16 border-b border-[#1E1B18] scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <ScrollReveal direction="up" threshold={0.1}>
            <ScentPyramid />
          </ScrollReveal>
        </div>
      </section>

      {/* S7: Brand Creed / Philosophy */}
      <section className="py-16 bg-gradient-to-b from-[#0E0C0A] to-[#070707] text-center border-b border-[#1E1B18]">
        <div className="max-w-3xl mx-auto px-4 space-y-4">
          <span className="font-serif tracking-[0.25em] text-xs text-[#C4A265] uppercase block">
            ORQ HAUTE PARFUMERIE
          </span>
          <h3 className="text-xl sm:text-2xl font-serif text-white leading-relaxed">
            &ldquo;حضورك له أثر لا يمحى من الذاكرة&rdquo;
          </h3>
          <p className="text-xs text-zinc-400 max-w-xl mx-auto leading-relaxed">
            نصنع خلطاتنا من خلاصات الزيوت النادرة بتركيزات مكثفة تدوم لأكثر من 12 ساعة لتمنحك كاريزما واثقة وهادئة.
          </p>
        </div>
      </section>

      <Footer />
    </div>
  );
}
