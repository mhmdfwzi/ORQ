'use client';

import React, { useEffect, useState } from 'react';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import ProductCard from '@/components/catalog/ProductCard';
import ScrollReveal from '@/components/ui/ScrollReveal';
import { PRODUCTS } from '@/data/products';

export default function ConceptThreeView() {
  const oudLine = PRODUCTS.filter((p) => p.mood === 'royal_oud');
  const signatureLine = PRODUCTS.filter((p) => p.mood !== 'royal_oud');

  const [heroMounted, setHeroMounted] = useState(false);
  useEffect(() => {
    const t = setTimeout(() => setHeroMounted(true), 150);
    return () => clearTimeout(t);
  }, []);

  return (
    <div className="bg-[#0B0B0B] text-[#F2EBDD]">
      <Header />

      {/* Panoramic Hero Banner with Smooth Cinematic Entrance */}
      <section className="relative overflow-hidden border-b border-[#221F1C]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
          
          <div className="border border-[#282522] rounded-2xl overflow-hidden relative shadow-2xl bg-[#0E0D0C]">
            {/* On Mobile: Flex Column (Image STRICTLY on Top, Text Below). On Desktop: Expansive Panoramic Overlay */}
            <div className="flex flex-col md:block relative">
              
              {/* 1. The Panoramic Bottles Image: Strictly ON TOP on Mobile (order-1) */}
              <div className="w-full aspect-[16/10] sm:aspect-[16/8] md:aspect-[16/6] bg-black relative overflow-hidden order-1">
                <img
                  src="/media/orq/19558efb-3e5a-4c4e-9fc7-4e6a38d1425d.jpg"
                  alt="ORQ Complete Collection Panorama"
                  className={`w-full h-full object-cover transition-all duration-1000 ease-[cubic-bezier(0.16,1,0.3,1)] ${
                    heroMounted ? 'opacity-90 scale-100' : 'opacity-0 scale-105'
                  }`}
                />
                {/* Desktop Gradient Overlay */}
                <div className="hidden md:block absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent pointer-events-none" />
                {/* Mobile Bottom Dissolve into text container */}
                <div className="md:hidden absolute bottom-0 inset-x-0 h-16 bg-gradient-to-t from-[#0E0D0C] to-transparent pointer-events-none" />
              </div>
              
              {/* 2. Headline & Buying Actions: STRICTLY BELOW Image on Mobile (order-2) */}
              <div
                className={`order-2 p-5 sm:p-6 md:p-0 md:absolute md:bottom-8 md:right-8 md:left-8 flex flex-col md:flex-row md:items-end justify-between gap-4 transition-all duration-1000 delay-300 ease-[cubic-bezier(0.16,1,0.3,1)] ${
                  heroMounted ? 'opacity-100 translate-y-0 filter-none' : 'opacity-0 translate-y-6 blur-sm'
                }`}
              >
                <div className="space-y-2 max-w-xl text-right">
                  <span className="text-[10px] sm:text-xs font-mono text-[#C4A265] tracking-[0.2em] uppercase block">
                    THE COMPLETE GALLERY 2026
                  </span>
                  <h1 className="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-white leading-tight">
                    اثنا عشر حضوراً.. <br className="hidden sm:inline" />
                    <span className="gold-gradient-text">بصمة عطرية واحدة</span>
                  </h1>
                  <p className="text-xs sm:text-sm text-zinc-300 leading-relaxed">
                    من هيبة العود المعتق والجلود النادرة، إلى نضارة البحر الأبيض المتوسط ونقاء المسك البودري.
                  </p>
                </div>

                <div className="flex flex-wrap gap-2.5 pt-2 md:pt-0">
                  <a
                    href="#oud-dynasty"
                    className="gold-button text-xs sm:text-sm px-5 py-3 rounded-lg font-bold flex-1 sm:flex-none text-center"
                  >
                    خط العود الملكي
                  </a>
                  <a
                    href="#signature-line"
                    className="border border-[#38332D] hover:border-[#C4A265] bg-black/60 text-[#ECC480] text-xs sm:text-sm px-5 py-3 rounded-lg transition flex-1 sm:flex-none text-center"
                  >
                    خط الحضور العصري
                  </a>
                </div>
              </div>

            </div>
          </div>

          {/* Smooth Scroll Connection Indicator */}
          <div className="pt-4 flex justify-center">
            <a
              href="#oud-dynasty"
              className="flex items-center gap-1.5 text-xs text-zinc-500 hover:text-[#ECC480] transition"
            >
              <span>استكشف خطوط التشكيلة</span>
              <span className="animate-scroll-pulse text-sm text-[#ECC480]">↓</span>
            </a>
          </div>

        </div>
      </section>

      {/* Line 1: Royal Oud Dynasty */}
      <section id="oud-dynasty" className="py-16 border-b border-[#221F1C] bg-[#0E0C0A]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          
          <ScrollReveal direction="right" delay={50}>
            <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-[#2C2117] pb-4">
              <div>
                <div className="flex items-center gap-2">
                  <span className="w-2.5 h-2.5 bg-amber-600 rounded-sm"></span>
                  <span className="text-xs font-bold text-[#ECC480] uppercase tracking-widest">
                    COLLECTION LINE 01
                  </span>
                </div>
                <h2 className="text-xl sm:text-2xl font-bold text-white mt-1">
                  مجموعة العود والفخامة الشرقية (The Royal Oud Dynasty)
                </h2>
                <p className="text-xs text-[#A0988C] mt-1">
                  عطور كثيفة ومكثفة بثبات أسطوري ورائحة العود المصفى بلمسات عصرية متمردة.
                </p>
              </div>

              <div className="text-xs text-[#C4A265] font-mono">
                تركيز Eau de Parfum مكثف • 1,999 ج.م
              </div>
            </div>
          </ScrollReveal>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {oudLine.map((product, idx) => (
              <ScrollReveal key={product.slug} delay={idx * 160} direction="up">
                <ProductCard product={product} />
              </ScrollReveal>
            ))}
          </div>

        </div>
      </section>

      {/* Line 2: Signature & Modern Presence */}
      <section id="signature-line" className="py-16 border-b border-[#221F1C]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          
          <ScrollReveal direction="left" delay={50}>
            <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-[#1E2530] pb-4">
              <div>
                <div className="flex items-center gap-2">
                  <span className="w-2.5 h-2.5 bg-sky-500 rounded-sm"></span>
                  <span className="text-xs font-bold text-sky-200 uppercase tracking-widest">
                    COLLECTION LINE 02
                  </span>
                </div>
                <h2 className="text-xl sm:text-2xl font-bold text-white mt-1">
                  مجموعة الحضور العصري والجاذبية (Signature Presence Line)
                </h2>
                <p className="text-xs text-[#A0988C] mt-1">
                  عطور يومية ومسائية صممت لتبقيك واثقاً ومحاطاً بالإطراءات في كل مناسبة.
                </p>
              </div>

              <div className="text-xs text-sky-300 font-mono">
                أسعار تبدأ من 1,199 ج.م
              </div>
            </div>
          </ScrollReveal>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {signatureLine.map((product, idx) => (
              <ScrollReveal key={product.slug} delay={(idx % 4) * 110} direction="up">
                <ProductCard product={product} />
              </ScrollReveal>
            ))}
          </div>

        </div>
      </section>

      {/* The Creed of ORQ */}
      <section className="py-20 bg-gradient-to-b from-[#100E0D] to-[#0A0A0A] text-center border-b border-[#221F1C]">
        <div className="max-w-4xl mx-auto px-4 space-y-6">
          <ScrollReveal direction="zoom" delay={100}>
            <span className="font-serif tracking-[0.3em] text-sm text-[#C4A265] uppercase block">
              OUR PHILOSOPHY
            </span>
            <h2 className="text-2xl sm:text-3xl font-serif text-white leading-relaxed mt-2">
              &ldquo;العطر لا يوضع ليُشم فقط.. <br />
              بل ليُحفر في الوجدان كبصمة تسبق حضورك&rdquo;
            </h2>
            <p className="text-xs sm:text-sm text-[#A0988C] leading-relaxed max-w-xl mx-auto mt-4">
              في ORQ Haute Parfumerie نؤمن أن كاريزما الرجل تبدأ من رائحته. نصنع خلطاتنا من أنقى الخلاصات العطرية لتعكس التميز والرجولة الواثقة التي لا تحتاج إلى صخب لتثبت وجودها.
            </p>
            <div className="pt-4">
              <span className="font-serif text-lg text-[#ECC480] tracking-widest">ORQ PERFUME</span>
            </div>
          </ScrollReveal>
        </div>
      </section>

      <Footer />
    </div>
  );
}
