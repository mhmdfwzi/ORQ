'use client';

import React, { useEffect, useRef, useState } from 'react';
import { useCart } from '@/components/cart/CartContext';
import { PRODUCTS } from '@/data/products';
import { formatPrice } from '@/lib/format';

export default function SeamlessWhiteGoldHero() {
  const { addToCart } = useCart();
  const heroProduct = PRODUCTS.find((p) => p.slug === 'royal-oud') || PRODUCTS[0];
  const canvasRef = useRef<HTMLCanvasElement | null>(null);

  const [isRevealing, setIsRevealing] = useState(false);

  useEffect(() => {
    return runSequence();
  }, []);

  const runSequence = () => {
    setIsRevealing(false);

    const t1 = setTimeout(() => {
      setIsRevealing(true);
    }, 300);

    const t2 = setTimeout(() => {
      setIsRevealing(false);
    }, 2000);

    return () => {
      clearTimeout(t1);
      clearTimeout(t2);
    };
  };

  // Ethereal Champagne & Golden Dust Canvas
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    let animId: number;
    let width = (canvas.width = canvas.offsetWidth);
    let height = (canvas.height = canvas.offsetHeight);

    const handleResize = () => {
      if (!canvas) return;
      width = canvas.width = canvas.offsetWidth;
      height = canvas.height = canvas.offsetHeight;
    };
    window.addEventListener('resize', handleResize);

    // Floating Golden Dust Embers
    interface GoldEmber {
      x: number;
      y: number;
      size: number;
      vx: number;
      vy: number;
      alpha: number;
    }
    const embers: GoldEmber[] = [];
    for (let i = 0; i < 40; i++) {
      embers.push({
        x: Math.random() * width,
        y: Math.random() * height,
        size: Math.random() * 2.2 + 0.8,
        vx: (Math.random() - 0.5) * 0.4,
        vy: -(Math.random() * 0.5 + 0.2),
        alpha: Math.random() * 0.6 + 0.2,
      });
    }

    const render = () => {
      if (document.hidden) {
        animId = requestAnimationFrame(render);
        return;
      }

      ctx.clearRect(0, 0, width, height);

      // Render Floating Champagne Gold Dust
      for (const e of embers) {
        e.x += e.vx;
        e.y += e.vy;
        if (e.y < 0) {
          e.y = height + 10;
          e.x = Math.random() * width;
        }

        ctx.fillStyle = `rgba(180, 135, 55, ${e.alpha})`;
        ctx.beginPath();
        ctx.arc(e.x, e.y, e.size, 0, Math.PI * 2);
        ctx.fill();
      }

      animId = requestAnimationFrame(render);
    };

    render();

    return () => {
      window.removeEventListener('resize', handleResize);
      cancelAnimationFrame(animId);
    };
  }, []);

  return (
    <section className="relative min-h-[580px] sm:min-h-[660px] lg:min-h-[740px] bg-gradient-to-b from-[#FAF8F5] via-[#F4EFE7] to-[#FAF8F5] overflow-hidden flex items-center justify-center border-b border-[#E8E2D5] select-none py-8 sm:py-12">
      {/* Background Floating Champagne Dust Canvas */}
      <canvas
        ref={canvasRef}
        className="absolute inset-0 w-full h-full pointer-events-none z-0"
      />

      {/* Atmospheric Luminous Glow */}
      <div className="absolute inset-0 bg-radial-gradient pointer-events-none z-0 opacity-70" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
          
          {/* 1. Photorealistic Bottle on Ivory & Gold Stage: On Top on Mobile (order-1), Left Column on Desktop (lg:order-2) */}
          <div className="lg:col-span-7 flex justify-center items-center relative order-1 lg:order-2 py-2 sm:py-4">
            
            {/* Luminous Warm Sunlight Aura */}
            <div
              className="absolute w-72 h-72 sm:w-[480px] sm:h-[480px] rounded-full bg-gradient-to-tr from-[#E6C687]/45 via-[#C4A265]/20 to-transparent blur-3xl pointer-events-none"
            />

            {/* Bottle Pedestal Container */}
            <div
              className="relative w-64 sm:w-80 md:w-[380px] aspect-[3/4] transition-transform duration-700 hover:scale-102"
            >
              {/* Flacon with Soft Dissolve */}
              <img
                src="/media/orq/royal_oud_photoreal.jpg"
                alt="ORQ Royal Oud Luxury Flacon - White Edition"
                className="w-full h-full object-contain filter contrast-105 drop-shadow-[0_20px_45px_rgba(140,105,45,0.25)] rounded-2xl"
                style={{
                  maskImage: 'radial-gradient(ellipse 90% 90% at 50% 50%, black 80%, transparent 100%)',
                  WebkitMaskImage: 'radial-gradient(ellipse 90% 90% at 50% 50%, black 80%, transparent 100%)',
                }}
              />

              {/* Shimmer Light Sweep */}
              <div
                className={`absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent -skew-x-12 pointer-events-none transition-transform duration-1000 ${
                  isRevealing ? 'translate-x-full opacity-100' : '-translate-x-full opacity-0'
                }`}
              />

              {/* Soft Luxury Shadow */}
              <div className="w-3/4 h-5 mx-auto -mt-4 bg-[#8C692D]/20 rounded-full blur-lg pointer-events-none" />
            </div>

          </div>

          {/* 2. Crisp Obsidian & Gold Typography: Immediately Visible */}
          <div
            className="lg:col-span-5 space-y-6 text-center sm:text-right z-10 order-2 lg:order-1"
          >
            {/* Brand Tag & Title */}
            <div className="space-y-2">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EFE9DD] border border-[#DDD5C5] text-[#80581B] text-[11px] font-bold tracking-widest uppercase">
                <span>✦</span>
                <span>THE WHITE & GOLD ATELIER</span>
              </div>

              <h1 className="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#1C1917] leading-tight">
                {heroProduct.name}
                {heroProduct.arabicName && heroProduct.arabicName !== heroProduct.name && (
                  <span className="text-2xl sm:text-3xl text-[#996515] font-normal mr-2">
                    ({heroProduct.arabicName})
                  </span>
                )}
              </h1>
              <p className="text-xs sm:text-sm text-[#575147] font-medium leading-relaxed">
                حضورك له أثر لا يمحى من الذاكرة • خلاصة العود الكمبودي والجلود الإيطالية بتركيز نيش مكثف
              </p>
            </div>

            {/* Price & Stock Status */}
            <div className="pt-1 flex items-baseline justify-center sm:justify-start gap-3">
              <span className="text-2xl sm:text-4xl font-extrabold text-[#80581B] font-mono whitespace-nowrap">
                {formatPrice(heroProduct.variants[1].price)} ج.م
              </span>
              <span className="text-xs text-[#7A7369] font-mono font-semibold">100 ML • EDP</span>
              <span className="text-xs text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                ● متوفر بالمخزون
              </span>
            </div>

            {/* Action Buttons */}
            <div className="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-2">
              <button
                onClick={() => addToCart(heroProduct, '100ml')}
                className="bg-gradient-to-r from-[#C4A265] via-[#B38734] to-[#996515] text-white text-xs sm:text-sm px-8 py-3.5 rounded-full font-bold shadow-xl shadow-[#A37B34]/25 hover:brightness-105 hover:scale-103 transition-transform flex items-center gap-2 min-h-[44px]"
              >
                <span>أضف للسلة الآن</span>
                <span>🛒</span>
              </button>

              {/* Replay Light Sweep */}
              <button
                onClick={runSequence}
                className="px-4 py-3.5 rounded-full border border-[#DCD5C7] hover:border-[#C4A265] bg-white text-[#80581B] text-xs font-semibold transition hover:bg-[#FAF8F5] flex items-center gap-1.5 min-h-[44px] shadow-sm"
                title="إعادة وميض الضوء"
              >
                <span>↺</span>
                <span>وميض النور</span>
              </button>
            </div>

            {/* Fast Notes Badges */}
            <div className="pt-2 flex flex-wrap items-center justify-center sm:justify-start gap-2 text-[11px] text-[#6B645A]">
              <span className="px-3 py-1 rounded-lg bg-white border border-[#E8E2D5] shadow-xs">
                🌿 قمة العطر: البرغموت والزعفران
              </span>
              <span className="px-3 py-1 rounded-lg bg-white border border-[#E8E2D5] shadow-xs">
                🪵 القلب: خشب العود المدخن
              </span>
              <span className="px-3 py-1 rounded-lg bg-white border border-[#E8E2D5] shadow-xs">
                👑 القاعدة: الجلود والعنبر الملكي
              </span>
            </div>

          </div>

        </div>
      </div>
    </section>
  );
}

