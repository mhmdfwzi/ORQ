'use client';

import { useEffect, useRef, useState, useSyncExternalStore, type TouchEvent } from 'react';
import { useCart } from '@/components/cart/CartContext';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { formatPrice } from '@/lib/format';
import { home } from '@/data/store-editorial.json';
import './StorefrontHero.css';

const [motto, headline] = home.h1.split(' — ');
const featuredSlugs = ['chaos', 'darknight', 'gigantic', 'motamared'];
const rotationInterval = 7200;
const reducedMotionQuery = '(prefers-reduced-motion: reduce)';

function subscribeToMotionPreference(onChange: () => void) {
  const query = window.matchMedia(reducedMotionQuery);
  query.addEventListener('change', onChange);

  return () => query.removeEventListener('change', onChange);
}

function prefersReducedMotion() {
  return window.matchMedia(reducedMotionQuery).matches;
}

export default function SeamlessCinematicHero() {
  const { products, site } = useCatalog();
  const { addToCart } = useCart();
  const featured = featuredSlugs.flatMap((slug) => {
    const product = products.find((item) => (item.pageSlug ?? item.slug) === slug);
    return product ? [product] : [];
  });
  const slides = featured.length ? featured : products.slice(0, 1);
  const slideCount = slides.length;

  const [selected, setSelected] = useState(0);
  const [showOpening, setShowOpening] = useState(false);
  const [rotation, setRotation] = useState<boolean | null>(null);
  const [hovered, setHovered] = useState(false);
  const motionReduced = useSyncExternalStore(subscribeToMotionPreference, prefersReducedMotion, () => true);
  const rotating = rotation ?? !motionReduced;
  const activeIndex = selected % Math.max(1, slideCount);
  const hero = useRef<HTMLElement>(null);
  const rotationButton = useRef<HTMLButtonElement>(null);
  const touchStart = useRef<{ x: number; y: number } | null>(null);

  useEffect(() => {
    if (!rotating || hovered || slideCount < 2) return;

    const timer = window.setInterval(() => {
      const bounds = hero.current?.getBoundingClientRect();
      if (!bounds || bounds.bottom <= 0 || bounds.top >= window.innerHeight || document.hidden) return;

      setSelected((index) => (index + 1) % slideCount);
      setShowOpening(false);
    }, rotationInterval);

    return () => window.clearInterval(timer);
  }, [rotating, hovered, slideCount]);

  const product = slides[activeIndex];
  if (!product) return null;

  const variant = product.variants.find((item) => item.size === '100ml' && item.inStock)
    ?? product.variants.find((item) => item.inStock)
    ?? product.variants[0];

  function selectSlide(index: number) {
    setRotation(false);
    setSelected((index + slideCount) % slideCount);
    setShowOpening(false);
  }

  function finishSwipe(event: TouchEvent<HTMLDivElement>) {
    const start = touchStart.current;
    touchStart.current = null;
    const end = event.changedTouches[0];
    if (!start || !end || slideCount < 2) return;

    const horizontal = end.clientX - start.x;
    if (Math.abs(horizontal) > 42 && Math.abs(horizontal) > Math.abs(end.clientY - start.y) * 1.5) {
      selectSlide(activeIndex + (horizontal > 0 ? 1 : -1));
    }
  }

  return (
    <section
      ref={hero}
      className="orq-hero"
      aria-labelledby="hero-title"
      onPointerEnter={(event) => { if (event.pointerType === 'mouse') setHovered(true); }}
      onPointerLeave={() => setHovered(false)}
      onFocusCapture={(event) => {
        if (event.target !== rotationButton.current) setRotation(false);
      }}
    >
      <div className="orq-hero-layout mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="orq-hero-headline">
          <p dir="ltr" className="orq-hero-tagline">{motto}</p>
          <h1 id="hero-title" className="orq-hero-title">{headline}</h1>
          <p className="orq-hero-intro">{home.subheadline}</p>
        </div>

        <div className="orq-hero-stage" role="group" aria-roledescription="عرض شرائح" aria-label="عطور ORQ المميزة">
          <div
            id="hero-slides"
            className="orq-hero-frame"
            onTouchStart={(event) => {
              const touch = event.touches[0];
              touchStart.current = event.touches.length === 1 ? { x: touch.clientX, y: touch.clientY } : null;
            }}
            onTouchEnd={finishSwipe}
            onTouchCancel={() => { touchStart.current = null; }}
          >
            {slides.map((item, index) => (
              <div key={item.slug} className="orq-hero-slide" data-active={index === activeIndex} aria-hidden={index !== activeIndex}>
                {item.image && (
                  // Admin-uploaded product images can use any host.
                  // eslint-disable-next-line @next/next/no-img-element
                  <img
                    src={item.image}
                    alt={item.name}
                    className="orq-hero-bottle"
                    loading={index === 0 ? 'eager' : 'lazy'}
                    fetchPriority={index === 0 ? 'high' : 'low'}
                  />
                )}
              </div>
            ))}
            {showOpening && <span key={product.slug} className="orq-hero-spray" aria-hidden="true" />}
            {product.notes.top.length > 0 && (
              <div className="orq-hero-spray-panel">
                <button
                  type="button"
                  aria-expanded={showOpening}
                  aria-controls="hero-opening-notes"
                  onClick={() => {
                    setRotation(false);
                    setShowOpening((open) => !open);
                  }}
                  className="orq-hero-control orq-hero-spray-button"
                >
                  <span aria-hidden="true">✳</span> جرّب رشّة
                </button>
                <p id="hero-opening-notes" hidden={!showOpening} className="orq-hero-opening">
                  {product.notes.top.join('، ')}
                </p>
              </div>
            )}
          </div>

          {slideCount > 1 && (
            <div
              className="orq-hero-selector"
              role="group"
              aria-label="التنقل بين العطور"
              onKeyDown={(event) => {
                if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') return;
                event.preventDefault();
                selectSlide(activeIndex + (event.key === 'ArrowLeft' ? 1 : -1));
              }}
            >
              <button type="button" className="orq-hero-control" onClick={() => selectSlide(activeIndex - 1)} aria-controls="hero-slides">السابق</button>
              <div className="orq-hero-indicators">
                {slides.map((item, index) => (
                  <button
                    type="button"
                    key={item.slug}
                    className="orq-hero-indicator"
                    aria-label={'عرض عطر ' + item.name}
                    title={item.name}
                    aria-pressed={index === activeIndex}
                    aria-controls="hero-slides"
                    onClick={() => selectSlide(index)}
                  />
                ))}
              </div>
              <button type="button" className="orq-hero-control" onClick={() => selectSlide(activeIndex + 1)} aria-controls="hero-slides">التالي</button>
              <button
                type="button"
                ref={rotationButton}
                className="orq-hero-control orq-hero-rotation"
                aria-controls="hero-slides"
                aria-label={rotating ? 'إيقاف العرض التلقائي' : 'تشغيل العرض التلقائي'}
                title={rotating ? 'إيقاف العرض التلقائي' : 'تشغيل العرض التلقائي'}
                onClick={() => setRotation(!rotating)}
              >
                <span className={rotating ? 'orq-hero-pause-icon' : 'orq-hero-play-icon'} aria-hidden="true" />
              </button>
            </div>
          )}
        </div>

        <div className="orq-hero-details">
          <div aria-live={rotating ? 'off' : 'polite'} aria-atomic="true">
            <div key={product.slug} className="orq-hero-product">
              <div className="flex flex-wrap items-baseline gap-x-3">
                <h2 className="orq-hero-name"><bdi>{product.name}</bdi></h2>
                {product.arabicName && product.arabicName !== product.name && <span className="text-sm text-[#D7AF76]">{product.arabicName}</span>}
              </div>
              {product.subtitle && <p className="mt-1.5 text-sm leading-6 text-[#D3C1AC] sm:text-base">{product.subtitle}</p>}
              {variant && (
                <p className="mt-2 flex flex-wrap items-baseline gap-x-2 text-sm text-[#CCBCA9]">
                  <strong className="text-lg text-[#ECC480] sm:text-xl">{formatPrice(variant.price)} {site.commerce.currency}</strong>
                  <span>{parseInt(variant.size, 10)} مل</span>
                </p>
              )}
            </div>
          </div>
          <div className="mt-4 flex flex-wrap items-center gap-2.5">
            <button
              type="button"
              disabled={!variant?.inStock}
              onClick={() => variant && addToCart(product, variant.size)}
              className="gold-button min-h-11 flex-1 rounded-lg px-3 py-2 text-sm active:brightness-90 disabled:opacity-60 sm:flex-none sm:px-6"
            >
              {variant?.inStock ? 'أضف للسلة' : 'غير متوفر'}
            </button>
            <a href="#signature" className="orq-hero-discover flex min-h-11 flex-1 items-center justify-center rounded-lg px-3 text-sm font-bold sm:flex-none sm:px-5">
              اكتشف العطور
            </a>
          </div>
        </div>
      </div>
    </section>
  );
}
