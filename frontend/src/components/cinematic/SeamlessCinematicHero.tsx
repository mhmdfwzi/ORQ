'use client';

import React, { useEffect, useRef, useState } from 'react';
import { useCart } from '@/components/cart/CartContext';
import { PRODUCTS } from '@/data/products';
import { formatPrice } from '@/lib/format';

export default function SeamlessCinematicHero() {
  const { addToCart } = useCart();
  const heroProduct = PRODUCTS.find((p) => p.slug === 'royal-oud') || PRODUCTS[0];
  const canvasRef = useRef<HTMLCanvasElement | null>(null);

  // Non-blocking visual shimmer sweep
  const [isRevealing, setIsRevealing] = useState(false);

  // Play light sweep on mount with proper cleanup
  useEffect(() => {
    return runSequence();
  }, []);

  const runSequence = () => {
    setIsRevealing(false);

    // Subtle light sweep across bottle
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

  // Ethereal Golden Smoke & Ember Canvas
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Respect reduced motion
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

    // Smoke particles
    interface Smoke {
      x: number;
      y: number;
      r: number;
      maxR: number;
      alpha: number;
      maxAlpha: number;
      grow: number;
      vx: number;
      vy: number;
    }
    const smokePuffs: Smoke[] = [];

    // Floating embers
    interface Ember {
      x: number;
      y: number;
      size: number;
      vx: number;
      vy: number;
      alpha: number;
    }
    const embers: Ember[] = [];

    let timer = 0;

    const render = () => {
      // Don't render if document is hidden
      if (document.hidden) {
        animId = requestAnimationFrame(render);
        return;
      }

      ctx.clearRect(0, 0, width, height);
      timer++;

      const originX = width < 1024 ? width * 0.5 : width * 0.65;
      const originY = width < 1024 ? height * 0.30 : height * 0.72;

      // Central Golden Ambient Glow
      const ambient = ctx.createRadialGradient(originX, originY - 100, 20, originX, originY - 100, width * 0.4);
      ambient.addColorStop(0, 'rgba(196, 162, 101, 0.18)');
      ambient.addColorStop(0.5, 'rgba(196, 162, 101, 0.04)');
      ambient.addColorStop(1, 'rgba(7, 7, 7, 0)');
      ctx.fillStyle = ambient;
      ctx.beginPath();
      ctx.arc(originX, originY - 100, width * 0.4, 0, Math.PI * 2);
      ctx.fill();

      // Spawn soft smoke around bottle base
      if (timer % 9 === 0 && smokePuffs.length < 18) {
        smokePuffs.push({
          x: originX + (Math.random() - 0.5) * 80,
          y: originY + (Math.random() - 0.5) * 20,
          r: 25,
          maxR: Math.random() * 100 + 80,
          alpha: 0.02,
          maxAlpha: Math.random() * 0.2 + 0.08,
          grow: Math.random() * 0.6 + 0.4,
          vx: (Math.random() - 0.5) * 0.8,
          vy: -(Math.random() * 1.1 + 0.4),
        });
      }

      // Spawn gold embers
      if (embers.length < 35 && Math.random() > 0.45) {
        embers.push({
          x: originX + (Math.random() - 0.5) * 200,
          y: originY + Math.random() * 30,
          size: Math.random() * 2.0 + 0.6,
          vx: (Math.random() - 0.5) * 0.7,
          vy: -(Math.random() * 1.6 + 0.7),
          alpha: Math.random() * 0.8 + 0.2,
        });
      }

      // Render smoke puffs
      for (let i = smokePuffs.length - 1; i >= 0; i--) {
        const p = smokePuffs[i];
        p.r += p.grow;
        p.x += p.vx;
        p.y += p.vy;

        if (p.r < p.maxR * 0.4) {
          p.alpha = Math.min(p.maxAlpha, p.alpha + 0.007);
        } else {
          p.alpha -= 0.0025;
        }

        if (p.alpha <= 0 || p.r >= p.maxR) {
          smokePuffs.splice(i, 1);
          continue;
        }

        const g = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r);
        g.addColorStop(0, `rgba(212, 175, 55, ${p.alpha})`);
        g.addColorStop(0.4, `rgba(45, 36, 22, ${p.alpha * 0.6})`);
        g.addColorStop(1, 'rgba(7, 7, 7, 0)');

        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fill();
      }

      // Render embers
      for (let i = embers.length - 1; i >= 0; i--) {
        const e = embers[i];
        e.x += e.vx;
        e.y += e.vy;
        e.alpha -= 0.006;

        if (e.alpha <= 0 || e.y < 0) {
          embers.splice(i, 1);
          continue;
        }

        ctx.fillStyle = `rgba(236, 196, 128, ${e.alpha})`;
        ctx.shadowBlur = 6;
        ctx.shadowColor = '#ECC480';
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
    <section className="relative min-h-[580px] sm:min-h-[680px] lg:min-h-[760px] bg-[#070707] overflow-hidden flex items-center justify-center border-b border-[#1E1B18] select-none py-6 sm:py-10">
      {/* Background Golden Smoke & Mist Canvas */}
      <canvas
        ref={canvasRef}
        className="absolute inset-0 w-full h-full pointer-events-none z-0"
      />

      {/* Atmospheric Vignette */}
      <div className="absolute inset-0 bg-radial-vignette pointer-events-none z-0" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-center">
          
          {/* 1. Photorealistic Luxury Bottle: Strictly ON TOP on Mobile (order-1), Left Column on Desktop (lg:order-2) */}
          <div className="lg:col-span-7 flex justify-center items-center relative order-1 lg:order-2 py-2 sm:py-4">
            
            {/* Dynamic Golden Scent Aura */}
            <div
              className="absolute w-64 h-64 sm:w-[460px] sm:h-[460px] rounded-full bg-gradient-to-tr from-[#C4A265]/30 via-amber-600/15 to-transparent blur-3xl pointer-events-none"
            />

            {/* Bottle Container */}
            <div
              className="relative w-64 sm:w-80 md:w-[380px] aspect-[3/4] transition-transform duration-700 hover:scale-102"
            >
              {/* Ultra-Photorealistic Luxury Flacon (Natural #000000 Black Background with Seamless Radial Dissolve) */}
              <img
                src="/media/orq/royal_oud_photoreal.jpg"
                alt="ORQ Royal Oud Luxury Flacon"
                className="w-full h-full object-contain filter contrast-110 drop-shadow-[0_25px_60px_rgba(0,0,0,0.95)]"
                style={{
                  maskImage: 'radial-gradient(ellipse 90% 90% at 50% 50%, black 75%, transparent 100%)',
                  WebkitMaskImage: 'radial-gradient(ellipse 90% 90% at 50% 50%, black 75%, transparent 100%)',
                }}
              />

              {/* Specular Light Reflection Sweep */}
              <div
                className={`absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -skew-x-12 pointer-events-none transition-transform duration-1000 ${
                  isRevealing ? 'translate-x-full opacity-100' : '-translate-x-full opacity-0'
                }`}
              />

              {/* Soft Shadow Pedestal */}
              <div className="w-3/4 h-5 mx-auto -mt-5 bg-black/90 rounded-full blur-md pointer-events-none" />
            </div>

          </div>

          {/* 2. Clean Luxury Typography & Purchasing: Immediately Visible (Zero Blank Wait) */}
          <div
            className="lg:col-span-5 space-y-5 text-center sm:text-right z-10 order-2 lg:order-1"
          >
            {/* Brand Tag & Title */}
            <div className="space-y-1.5">
              <span className="font-serif tracking-[0.3em] text-[#C4A265] text-xs font-bold uppercase block">
                ORQ HAUTE PARFUMERIE
              </span>
              <h1 className="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight">
                {heroProduct.name}
                {heroProduct.arabicName && heroProduct.arabicName !== heroProduct.name && (
                  <span className="text-xl sm:text-2xl text-[#ECC480] font-normal mr-2">
                    ({heroProduct.arabicName})
                  </span>
                )}
              </h1>
              <p className="text-xs sm:text-sm text-zinc-300">
                حضورك له أثر لا يمحى من الذاكرة • تركيز نيش فاخر
              </p>
            </div>

            {/* Price & Status */}
            <div className="pt-1 flex items-baseline justify-center sm:justify-start gap-3">
              <span className="text-2xl sm:text-4xl font-extrabold text-[#ECC480] font-mono whitespace-nowrap">
                {formatPrice(heroProduct.variants[1].price)} ج.م
              </span>
              <span className="text-xs text-zinc-400 font-mono">100 ML • EDP</span>
              <span className="text-xs text-emerald-400 font-semibold">● متوفر بالمخزون</span>
            </div>

            {/* Decisive Action Buttons */}
            <div className="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-2">
              <button
                onClick={() => addToCart(heroProduct, '100ml')}
                className="gold-button text-xs sm:text-sm px-8 py-3.5 rounded-full font-bold shadow-2xl shadow-[#C4A265]/25 hover:scale-105 transition-transform flex items-center gap-2 min-h-[44px]"
              >
                <span>أضف للسلة الآن</span>
                <span>🛒</span>
              </button>

              {/* Replay Light Sweep */}
              <button
                onClick={runSequence}
                className="px-4 py-3.5 rounded-full border border-[#38332D] hover:border-[#C4A265] bg-[#141210]/90 text-[#ECC480] text-xs font-semibold transition hover:bg-[#201D1A] flex items-center gap-1.5 min-h-[44px]"
                title="إعادة وميض الضوء على الزجاجة"
              >
                <span>↺</span>
                <span>وميض الضوء</span>
              </button>
            </div>

            {/* Fast Notes Badges */}
            <div className="pt-4 border-t border-[#1E1B18] flex items-center justify-center sm:justify-start gap-4 sm:gap-6 text-xs text-zinc-400 flex-wrap">
              <span className="flex items-center gap-1.5">
                <span className="text-[#C4A265]">✦</span>
                <span>حمضيات إيطالية</span>
              </span>
              <span className="flex items-center gap-1.5">
                <span className="text-[#C4A265]">✦</span>
                <span>فلفل أسود وخشب الأرز</span>
              </span>
              <span className="flex items-center gap-1.5">
                <span className="text-[#C4A265]">✦</span>
                <span>العود الملكي الفاخر</span>
              </span>
            </div>
          </div>

        </div>
      </div>

      {/* Seamless Bottom Gradient to Connect with Next Section */}
      <div className="absolute bottom-0 inset-x-0 h-24 bg-gradient-to-t from-[#070707] to-transparent pointer-events-none z-10" />

      {/* Seamless Scroll Connection Indicator (Pointing to #signature) */}
      <a
        href="#signature"
        className="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center gap-1 text-[11px] text-zinc-400 hover:text-[#ECC480] transition-colors cursor-pointer"
        aria-label="الانتقال إلى المجموعة العطرية"
      >
        <span className="font-serif tracking-widest uppercase text-[10px] text-[#C4A265]/90">
          اكتشف المجموعة
        </span>
        <span className="animate-scroll-pulse text-base text-[#ECC480]">↓</span>
      </a>
    </section>
  );
}
