'use client';

import React, { useState, useEffect, useRef } from 'react';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import ProductCard from '@/components/catalog/ProductCard';
import MoodFilterBar from '@/components/cinematic/MoodFilterBar';
import ScentPyramid from '@/components/cinematic/ScentPyramid';
import BrandStory from '@/components/cinematic/BrandStory';
import ScrollReveal from '@/components/ui/ScrollReveal';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { ScentMood } from '@/types/product';
import { useCart } from '@/components/cart/CartContext';
import { formatPrice } from '@/lib/format';

/* ────────────────────────────────────────────────────────────
 *  CONCEPT 6 — "THE EDITORIAL"
 *  Full-bleed photography hero with layered depth, dramatic
 *  scroll-linked reveals, and asymmetric editorial grid layouts.
 *  Dark obsidian + burnished gold + warm amber undertones.
 * ──────────────────────────────────────────────────────────── */

function HeroSection() {
  const { products: PRODUCTS } = useCatalog();
  const { addToCart } = useCart();
  const heroProduct = PRODUCTS.find((p) => p.isBestSeller) ?? PRODUCTS[0];
  const secondHero = PRODUCTS.filter((p) => p.isBestSeller)[1] ?? PRODUCTS[1];
  const [activeHero, setActiveHero] = useState(0);
  const heroes = [heroProduct, secondHero].filter(Boolean);
  const current = heroes[activeHero] ?? heroProduct;

  // Auto-rotate hero products
  useEffect(() => {
    if (heroes.length < 2) return;
    const interval = setInterval(() => {
      setActiveHero((prev) => (prev + 1) % heroes.length);
    }, 6000);
    return () => clearInterval(interval);
  }, [heroes.length]);

  return (
    <section className="relative min-h-[100dvh] bg-[#070707] overflow-hidden flex items-end">
      {/* Full-bleed product image that bleeds to top edge */}
      <div className="absolute inset-0 z-0">
        {heroes.map((hero, idx) => (
          <div
            key={hero.slug}
            className="absolute inset-0 flex items-center justify-center"
            style={{
              opacity: activeHero === idx ? 1 : 0,
              transitionProperty: 'opacity',
              transitionDuration: '1200ms',
              transitionTimingFunction: 'cubic-bezier(0.22, 1, 0.36, 1)',
            }}
          >
            <img
              src={hero.image}
              alt={hero.name}
              className="w-full h-full object-cover object-center scale-110"
              style={{
                maskImage: 'linear-gradient(to bottom, black 30%, transparent 95%)',
                WebkitMaskImage: 'linear-gradient(to bottom, black 30%, transparent 95%)',
              }}
            />
          </div>
        ))}

        {/* Gradient overlays for text readability */}
        <div className="absolute inset-0 bg-gradient-to-t from-[#070707] via-[#070707]/70 to-transparent" />
        <div className="absolute inset-0 bg-gradient-to-r from-[#070707]/80 via-transparent to-[#070707]/40" />
      </div>

      {/* Hero content anchored at lower third */}
      <div className="relative z-10 w-full pb-16 pt-40">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="max-w-2xl space-y-5">
            <h1
              className="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-white leading-[0.95] tracking-tight"
              style={{ letterSpacing: '-0.03em' }}
            >
              {current.name}
            </h1>
            {current.arabicName && current.arabicName !== current.name && (
              <p className="text-xl sm:text-2xl font-light text-[#ECC480]/90">
                {current.arabicName}
              </p>
            )}
            <p className="text-sm sm:text-base text-zinc-300 max-w-lg leading-relaxed">
              {current.description}
            </p>

            <div className="flex items-center gap-6 pt-2">
              <button
                onClick={() => addToCart(current, '100ml')}
                className="gold-button px-8 py-4 rounded-full text-sm font-bold flex items-center gap-2 min-h-[48px]"
              >
                <span>اطلب الآن</span>
                <span className="text-lg">←</span>
              </button>
              <div className="text-right">
                <span className="block text-2xl font-bold text-[#ECC480] font-mono">
                  {formatPrice(current.variants[1]?.price ?? current.variants[0]?.price ?? 0)} ج.م
                </span>
                <span className="text-xs text-zinc-500">100 ML • EDP</span>
              </div>
            </div>

            {/* Hero dots */}
            {heroes.length > 1 && (
              <div className="flex items-center gap-3 pt-4">
                {heroes.map((h, idx) => (
                  <button
                    key={h.slug}
                    onClick={() => setActiveHero(idx)}
                    className={`h-0.5 rounded-full transition-all duration-500 ${
                      activeHero === idx
                        ? 'w-10 bg-[#C4A265]'
                        : 'w-5 bg-zinc-600 hover:bg-zinc-400'
                    }`}
                    aria-label={h.name}
                  />
                ))}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Scroll hint */}
      <a
        href="#collection"
        className="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 text-zinc-400 hover:text-[#ECC480] transition-colors"
        aria-label="اكتشف المجموعة"
      >
        <span className="animate-scroll-pulse text-xl block">↓</span>
      </a>
    </section>
  );
}

/* ── Horizontal Scroll Product Showcase ── */
function ProductShowcase() {
  const { products: PRODUCTS } = useCatalog();
  const scrollRef = useRef<HTMLDivElement>(null);
  const [canScrollLeft, setCanScrollLeft] = useState(false);
  const [canScrollRight, setCanScrollRight] = useState(true);

  const updateScrollState = () => {
    const el = scrollRef.current;
    if (!el) return;
    // RTL: scrollLeft is negative in some browsers
    const sl = Math.abs(el.scrollLeft);
    setCanScrollLeft(sl > 10);
    setCanScrollRight(sl < el.scrollWidth - el.clientWidth - 10);
  };

  const scroll = (direction: 'left' | 'right') => {
    const el = scrollRef.current;
    if (!el) return;
    const amount = el.clientWidth * 0.7;
    // In RTL, "right" visually = negative scrollLeft
    el.scrollBy({
      left: direction === 'right' ? -amount : amount,
      behavior: 'smooth',
    });
  };

  return (
    <section id="collection" className="py-20 bg-[#070707] border-b border-[#1E1B18] scroll-mt-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ScrollReveal direction="up">
          <div className="flex items-end justify-between mb-10">
            <div>
              <h2 className="text-2xl sm:text-4xl font-extrabold text-white leading-tight">
                تشكيلة العطور الفاخرة
              </h2>
              <p className="text-sm text-zinc-400 mt-2 max-w-md">
                {PRODUCTS.length} عطراً مصنوعاً بعناية من أندر الخامات العطرية في العالم
              </p>
            </div>
            <div className="hidden sm:flex items-center gap-2">
              <button
                onClick={() => scroll('left')}
                disabled={!canScrollLeft}
                className="w-10 h-10 rounded-full border border-[#2B2723] flex items-center justify-center text-zinc-400 hover:text-white hover:border-[#C4A265] transition disabled:opacity-30 disabled:cursor-not-allowed"
              >
                →
              </button>
              <button
                onClick={() => scroll('right')}
                disabled={!canScrollRight}
                className="w-10 h-10 rounded-full border border-[#2B2723] flex items-center justify-center text-zinc-400 hover:text-white hover:border-[#C4A265] transition disabled:opacity-30 disabled:cursor-not-allowed"
              >
                ←
              </button>
            </div>
          </div>
        </ScrollReveal>

        <div
          ref={scrollRef}
          onScroll={updateScrollState}
          className="flex gap-5 overflow-x-auto hide-scrollbar pb-4 -mx-4 px-4 snap-x snap-mandatory"
        >
          {PRODUCTS.map((product, idx) => (
            <div
              key={product.slug}
              className="min-w-[260px] sm:min-w-[280px] lg:min-w-[300px] snap-start flex-shrink-0"
            >
              <ScrollReveal delay={idx * 60} direction="up">
                <ProductCard product={product} />
              </ScrollReveal>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

/* ── Split Editorial: Two staggered product columns ── */
function EditorialGrid() {
  const { products: PRODUCTS } = useCatalog();
  const leftCol = PRODUCTS.filter((_, i) => i % 2 === 0).slice(0, 3);
  const rightCol = PRODUCTS.filter((_, i) => i % 2 === 1).slice(0, 3);

  return (
    <section className="py-20 bg-[#0A0908] border-b border-[#1E1B18]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ScrollReveal direction="up">
          <h2 className="text-2xl sm:text-3xl font-extrabold text-white mb-12 text-center">
            صُنع لمن يترك أثراً
          </h2>
        </ScrollReveal>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
          {/* Left column — flush top */}
          <div className="space-y-8">
            {leftCol.map((product, idx) => (
              <ScrollReveal key={product.slug} delay={idx * 120} direction="up">
                <EditorialCard product={product} variant={idx === 0 ? 'large' : 'compact'} />
              </ScrollReveal>
            ))}
          </div>

          {/* Right column — offset down for stagger */}
          <div className="space-y-8 lg:pt-24">
            {rightCol.map((product, idx) => (
              <ScrollReveal key={product.slug} delay={idx * 120 + 80} direction="up">
                <EditorialCard product={product} variant={idx === 1 ? 'large' : 'compact'} />
              </ScrollReveal>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function EditorialCard({ product, variant }: { product: any; variant: 'large' | 'compact' }) {
  const { addToCart } = useCart();
  const defaultVariant = product.variants.find((v: any) => v.size === '100ml') || product.variants[0];

  if (variant === 'large') {
    return (
      <div className="group relative rounded-2xl overflow-hidden bg-[#12100E] border border-[#24211D] hover:border-[#C4A265]/50 transition-colors">
        <div className="aspect-[4/5] overflow-hidden">
          <img
            src={product.image}
            alt={product.name}
            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-[#12100E] via-transparent to-transparent" />
        </div>
        <div className="absolute bottom-0 inset-x-0 p-6 space-y-3">
          <h3 className="text-xl font-bold text-white">{product.name}</h3>
          <p className="text-xs text-zinc-400 line-clamp-2">{product.description}</p>
          <div className="flex items-center justify-between">
            <span className="text-lg font-bold text-[#ECC480] font-mono">
              {formatPrice(defaultVariant.price)} ج.م
            </span>
            <button
              onClick={() => addToCart(product, '100ml')}
              className="gold-button px-5 py-2.5 rounded-full text-xs font-bold flex items-center gap-1.5 min-h-[40px]"
            >
              <span>اطلب</span>
              <span>🛒</span>
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="group flex gap-4 items-center p-4 rounded-2xl bg-[#12100E] border border-[#24211D] hover:border-[#C4A265]/50 transition-colors">
      <div className="w-24 h-24 sm:w-28 sm:h-28 rounded-xl overflow-hidden flex-shrink-0 bg-[#0A0A0A]">
        <img
          src={product.image}
          alt={product.name}
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        />
      </div>
      <div className="flex-1 min-w-0 space-y-1">
        <h3 className="text-sm font-bold text-white truncate">{product.name}</h3>
        <p className="text-[11px] text-zinc-500">{product.scentType}</p>
        <div className="flex items-center justify-between pt-1">
          <span className="text-sm font-bold text-[#ECC480] font-mono">
            {formatPrice(defaultVariant.price)} ج.م
          </span>
          <button
            onClick={() => addToCart(product, '100ml')}
            className="text-xs text-[#C4A265] hover:text-white transition font-medium"
          >
            أضف للسلة ←
          </button>
        </div>
      </div>
    </div>
  );
}

/* ── Mood Discovery ── */
function MoodDiscoverySection() {
  const { products: PRODUCTS, getProductsByMood } = useCatalog();
  const [selectedMood, setSelectedMood] = useState<ScentMood>('all');
  const filteredProducts = getProductsByMood(selectedMood);

  return (
    <section id="moods" className="py-20 bg-[#0B0A09] border-b border-[#1E1B18] scroll-mt-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <ScrollReveal direction="up">
          <div className="max-w-xl">
            <h2 className="text-2xl sm:text-3xl font-extrabold text-white">
              اختر طابع حضورك
            </h2>
            <p className="text-sm text-zinc-400 mt-3">
              حدد شخصيتك العطرية لتصفية التشكيلة المناسبة لذوقك ومناسباتك
            </p>
          </div>
        </ScrollReveal>

        <ScrollReveal direction="up" delay={80}>
          <MoodFilterBar
            currentMood={selectedMood}
            onSelectMood={(mood) => setSelectedMood(mood)}
          />
        </ScrollReveal>

        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
          {filteredProducts.map((product, idx) => (
            <ScrollReveal key={product.slug} delay={(idx % 4) * 60} direction="up">
              <ProductCard product={product} />
            </ScrollReveal>
          ))}
        </div>
      </div>
    </section>
  );
}

/* ── Bundle Offer with dramatic split ── */
function BundleSection() {
  const { giftBundle: GIFT_BUNDLE, site } = useCatalog();
  const { addToCart } = useCart();
  const bundleVariant = GIFT_BUNDLE.variants[0];
  const bundlePrice = bundleVariant?.price ?? 0;
  const bundleList = bundleVariant?.compareAtPrice ?? bundlePrice;
  const bundleSaving = bundleList - bundlePrice;
  const discount = bundleList > 0 ? Math.round((bundleSaving / bundleList) * 100) : 0;
  const currency = site.commerce.currency;

  return (
    <section id="gifting" className="py-0 bg-[#070707] border-b border-[#1E1B18] scroll-mt-20">
      <div className="grid grid-cols-1 lg:grid-cols-2 min-h-[500px]">
        {/* Image half — full bleed */}
        <div className="relative aspect-square lg:aspect-auto overflow-hidden bg-black">
          <img
            src="/media/orq/bundle-gift-box.webp"
            alt="صندوق باقة الهدايا الفاخرة"
            className="w-full h-full object-cover"
          />
          {discount > 0 && (
            <div className="absolute top-6 right-6 bg-red-600 text-white font-bold text-sm px-4 py-2 rounded-lg shadow-lg">
              وفّر {discount}%
            </div>
          )}
        </div>

        {/* Content half */}
        <div className="flex items-center p-8 sm:p-12 lg:p-16">
          <ScrollReveal direction="up">
            <div className="space-y-6 max-w-lg">
              <h2 className="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                {GIFT_BUNDLE.name}
              </h2>
              <p className="text-base text-[#ECC480]">{GIFT_BUNDLE.subtitle}</p>
              <p className="text-sm text-zinc-400 leading-relaxed">
                {GIFT_BUNDLE.description}
              </p>

              {/* Highlights */}
              <div className="grid grid-cols-3 gap-3 py-2">
                {site.bundle.highlights.map((h) => (
                  <div key={h.title} className="text-center p-3 rounded-xl bg-[#12100E] border border-[#24211D]">
                    <span className="text-[#ECC480] font-bold block text-sm">{h.title}</span>
                    <span className="text-[10px] text-zinc-500">{h.desc}</span>
                  </div>
                ))}
              </div>

              {/* Price & CTA */}
              <div className="flex flex-wrap items-center gap-5 pt-2">
                <button
                  onClick={() => addToCart(GIFT_BUNDLE, '50ml')}
                  className="gold-button px-8 py-4 rounded-full text-sm font-bold flex items-center gap-2 min-h-[48px]"
                >
                  <span>{site.bundle.cta} بـ {formatPrice(bundlePrice)} {currency}</span>
                  <span>🛒</span>
                </button>
                {bundleSaving > 0 && (
                  <div className="text-sm space-x-2 space-x-reverse">
                    <span className="line-through text-zinc-500 font-mono">
                      {formatPrice(bundleList)} {currency}
                    </span>
                    <span className="text-emerald-400 font-bold font-mono">
                      وفر {formatPrice(bundleSaving)} {currency}
                    </span>
                  </div>
                )}
              </div>
            </div>
          </ScrollReveal>
        </div>
      </div>
    </section>
  );
}

/* ── Brand Creed ── */
function CreedSection() {
  const { site } = useCatalog();

  return (
    <section className="py-24 bg-[#070707] border-b border-[#1E1B18] relative overflow-hidden">
      {/* Ambient glow */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#C4A265]/5 rounded-full blur-3xl pointer-events-none" />

      <div className="relative max-w-3xl mx-auto px-4 text-center space-y-6">
        <ScrollReveal direction="up">
          <h2 className="text-3xl sm:text-4xl lg:text-5xl font-serif text-white leading-relaxed">
            &ldquo;{site.creed.quote}&rdquo;
          </h2>
        </ScrollReveal>
        <ScrollReveal direction="up" delay={100}>
          <p className="text-sm text-zinc-400 max-w-xl mx-auto leading-relaxed">
            {site.creed.text}
          </p>
        </ScrollReveal>
        <ScrollReveal direction="up" delay={200}>
          <div className="w-12 h-px bg-[#C4A265]/60 mx-auto" />
        </ScrollReveal>
      </div>
    </section>
  );
}

/* ── MAIN VIEW ── */
export default function ConceptSixView() {
  return (
    <div className="bg-[#070707] text-[#F2EBDD] min-h-screen">
      <Header />
      <HeroSection />
      <ProductShowcase />
      <EditorialGrid />
      <MoodDiscoverySection />
      <BundleSection />

      {/* Scent Pyramid */}
      <section id="notes" className="py-20 bg-[#0A0908] border-b border-[#1E1B18] scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <ScrollReveal direction="up" threshold={0.1}>
            <ScentPyramid />
          </ScrollReveal>
        </div>
      </section>

      <CreedSection />
      <BrandStory />
      <Footer />
    </div>
  );
}

