'use client';

import { useState } from 'react';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import BrandStory from '@/components/cinematic/BrandStory';
import ProductCard from '@/components/catalog/ProductCard';
import MoodFilterBar from '@/components/cinematic/MoodFilterBar';
import ScentPyramid from '@/components/cinematic/ScentPyramid';
import SeamlessCinematicHero from '@/components/cinematic/SeamlessCinematicHero';
import { useCatalog } from '@/components/catalog/CatalogContext';
import type { ScentMood } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';
import { formatPrice } from '@/lib/format';

export default function ConceptFourView() {
  const { giftBundle, getProductsByMood, site } = useCatalog();
  const { addToCart } = useCart();
  const [selectedMood, setSelectedMood] = useState<ScentMood>('all');

  const products = [...getProductsByMood(selectedMood)]
    .sort((a, b) => Number(b.isBestSeller ?? false) - Number(a.isBestSeller ?? false));
  const bundleVariant = giftBundle.variants[0];
  const bundleListPrice = bundleVariant?.compareAtPrice ?? 0;
  const bundleDiscounted = bundleVariant && bundleListPrice > bundleVariant.price;
  const currency = site.commerce.currency;

  return (
    <div className="min-h-screen bg-[#070707] text-[#F2EBDD]">
      <Header />
      <SeamlessCinematicHero />

      <section id="signature" aria-labelledby="collection-title" className="scroll-mt-20 border-b border-[#221F1C] py-8 sm:py-14">
        <div className="mx-auto max-w-7xl space-y-4 px-4 sm:space-y-6 sm:px-6 lg:px-8">
          <h2 id="collection-title" className="text-2xl font-bold text-white sm:text-3xl">العطور</h2>
          <div id="moods" className="scroll-mt-20">
            <MoodFilterBar currentMood={selectedMood} onSelectMood={setSelectedMood} />
          </div>
          <div id="lines" className="scroll-mt-20">
            {products.length ? (
              <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4">
                {products.map((product) => <ProductCard key={product.slug} product={product} />)}
              </div>
            ) : (
              <p role="status" className="py-4 text-sm text-[#B9B1A5]">لا توجد عطور بهذا الطابع حالياً.</p>
            )}
          </div>
        </div>
      </section>

      <section id="gifting" aria-labelledby="gift-title" className="scroll-mt-20 border-b border-[#221F1C] py-8 sm:py-14">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid gap-5 rounded-2xl border border-[#C4A265]/25 bg-[#14110E] p-3 sm:grid-cols-2 sm:items-center sm:gap-8 sm:p-6">
            {giftBundle.image && (
              // Admin-uploaded product images can use any host.
              // eslint-disable-next-line @next/next/no-img-element
              <img src={giftBundle.image} alt={giftBundle.name} loading="lazy" className="aspect-square w-full max-h-96 rounded-xl bg-[#0A0908] object-cover" />
            )}
            <div className="min-w-0 space-y-3 px-2 pb-2 sm:p-2">
              <h2 id="gift-title" className="text-xl font-bold text-white sm:text-3xl">{giftBundle.name}</h2>
              <ul className="flex flex-wrap gap-2 text-xs text-[#E2D3BC] sm:text-sm">
                {site.bundle.highlights.map(({ title }) => (
                  <li key={title} className="rounded-md border border-[#3A3025] px-2 py-1">{title}</li>
                ))}
              </ul>
              {bundleVariant && (
                <div className="flex flex-wrap items-baseline gap-2">
                  <span className="text-xl font-bold text-[#ECC480]">{formatPrice(bundleVariant.price)} {currency}</span>
                  {bundleDiscounted && <del className="text-sm text-[#A0988C]">{formatPrice(bundleListPrice)} {currency}</del>}
                </div>
              )}
              <button
                type="button"
                disabled={!bundleVariant?.inStock}
                onClick={() => bundleVariant && addToCart(giftBundle, bundleVariant.size)}
                className="gold-button min-h-11 w-full rounded-lg px-5 py-2 text-sm disabled:opacity-60 sm:w-auto"
              >
                {bundleVariant?.inStock ? site.bundle.cta : 'غير متوفرة حالياً'}
              </button>
            </div>
          </div>
        </div>
      </section>

      <section id="notes" className="scroll-mt-20 border-b border-[#221F1C] py-8 sm:py-14">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <ScentPyramid />
        </div>
      </section>

      <BrandStory />
      <Footer />
    </div>
  );
}
