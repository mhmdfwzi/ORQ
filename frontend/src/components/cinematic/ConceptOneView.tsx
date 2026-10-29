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

      {/* S3: Scent Pyramid Exploration */}
      <section id="notes" className="py-14 border-b border-[#1E1B18] relative">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <ScrollReveal direction="up" threshold={0.1}>
            <ScentPyramid />
          </ScrollReveal>
        </div>
      </section>

      {/* S2: Curated Collection Grid */}
      <section id="signature" className="py-14 border-b border-[#1E1B18]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          <ScrollReveal direction="up" delay={50}>
            <div className="flex items-center justify-between border-b border-[#1E1B18] pb-4">
              <div>
                <h2 className="text-xl sm:text-2xl font-bold text-white">المجموعة العطرية</h2>
                <p className="text-xs text-zinc-400">توقيعات عطرية استثنائية للرجل العصري</p>
              </div>
              <span className="text-xs text-[#ECC480] font-mono">12 عطراً نادراً</span>
            </div>
          </ScrollReveal>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {curatedProducts.map((product, idx) => (
              <ScrollReveal key={product.slug} delay={idx * 130} direction="up">
                <ProductCard product={product} />
              </ScrollReveal>
            ))}
          </div>
        </div>
      </section>

      <Footer />
    </div>
  );
}
