'use client';

import React from 'react';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import ProductCard from '@/components/catalog/ProductCard';
import ScentPyramid from '@/components/cinematic/ScentPyramid';
import SeamlessCinematicHero from '@/components/cinematic/SeamlessCinematicHero';
import ScrollReveal from '@/components/ui/ScrollReveal';
import { PRODUCTS } from '@/data/products';

export default function ConceptOneView() {
  const curatedProducts = PRODUCTS.slice(0, 4);

  return (
    <div className="bg-[#070707] text-[#F2EBDD] min-h-screen">
      <Header />

      {/* S1: Seamless Cinematic Entrance (Pure Sculpted Bottle & Zero Seams) */}
      <SeamlessCinematicHero />

      {/* S2: Curated Collection Grid (Immediate Discovery following Hero) */}
      <section id="signature" className="py-14 border-b border-[#1E1B18] scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          <ScrollReveal direction="up" delay={50}>
            <div className="flex items-center justify-between border-b border-[#1E1B18] pb-4">
              <div>
                <h2 className="text-xl sm:text-2xl font-bold text-white">المجموعة العطرية المختارة</h2>
                <p className="text-xs text-zinc-400">توقيعات عطرية استثنائية صُممت لتصنع حضورك</p>
              </div>
              <span className="text-xs text-[#ECC480] font-mono">12 عطراً نادراً</span>
            </div>
          </ScrollReveal>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {curatedProducts.map((product, idx) => (
              <ScrollReveal key={product.slug} delay={idx * 120} direction="up">
                <ProductCard product={product} />
              </ScrollReveal>
            ))}
          </div>
        </div>
      </section>

      {/* S3: Scent Pyramid Exploration */}
      <section id="notes" className="py-14 border-b border-[#1E1B18] relative scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <ScrollReveal direction="up" threshold={0.1}>
            <ScentPyramid />
          </ScrollReveal>
        </div>
      </section>

      {/* Brand Creed / Philosophy */}
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
