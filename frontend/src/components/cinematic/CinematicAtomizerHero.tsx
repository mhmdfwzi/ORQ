'use client';

import React, { useEffect, useRef, useState } from 'react';
import { useCart } from '@/components/cart/CartContext';
import { PRODUCTS } from '@/data/products';
import { formatPrice } from '@/lib/format';

export default function CinematicAtomizerHero() {
  const { addToCart } = useCart();
  const heroProduct = PRODUCTS.find((p) => p.slug === 'chaos') || PRODUCTS[1];
  const canvasRef = useRef<HTMLCanvasElement | null>(null);

  // 3-Stage Entrance Sequence (Seamless, Zero Harsh Cuts)
  // Stage 0: Golden Volumetric Light & Atmospheric Rays
  // Stage 1: Bottle Manifestation & High-Pressure Atomizer Burst
  // Stage 2: Smooth Glide into Store Elements
  const [stage, setStage] = useState<number>(0);
  const [sprayActive, setSprayActive] = useState(false);
  const [sprayCount, setSprayCount] = useState(0);

  // Spray particle system
  const particlesRef = useRef<
    {
      x: number;
      y: number;
      vx: number;
      vy: number;
      size: number;
      alpha: number;
      color: string;
      life: number;
      maxLife: number;
    }[]
  >([]);

  // Sequence playback
  const runSequence = () => {
    setStage(0);
    setSprayActive(false);

    const t1 = setTimeout(() => {
      setStage(1);
      triggerSpray();
    }, 600);

    const t2 = setTimeout(() => {
      setStage(2);
    }, 2400);

    return () => {
      clearTimeout(t1);
      clearTimeout(t2);
    };
  };

  useEffect(() => {
    runSequence();
  }, []);

  // Function to trigger atomizer mist explosion
  const triggerSpray = () => {
    setSprayActive(true);
    setSprayCount((prev) => prev + 1);

    const canvas = canvasRef.current;
    if (!canvas) return;
    const nozzleX = canvas.width < 1024 ? canvas.width * 0.5 : canvas.width * 0.65;
    const nozzleY = canvas.width < 1024 ? canvas.height * 0.20 : canvas.height * 0.42;

    for (let i = 0; i < 280; i++) {
      const angle = (Math.random() - 0.5) * 1.3 - Math.PI / 2;
      const speed = Math.random() * 12 + 4;
      const life = Math.random() * 55 + 35;

      particlesRef.current.push({
        x: nozzleX + (Math.random() - 0.5) * 12,
        y: nozzleY + (Math.random() - 0.5) * 8,
        vx: Math.cos(angle) * speed + (Math.random() - 0.5) * 2,
        vy: Math.sin(angle) * speed * 0.7 - Math.random() * 2,
        size: Math.random() * 2.8 + 0.8,
        alpha: Math.random() * 0.85 + 0.2,
        color: Math.random() > 0.3 ? '#ECC480' : '#D4AF37',
        life: 0,
        maxLife: life,
      });
    }

    setTimeout(() => {
      setSprayActive(false);
    }, 600);
  };

  // Canvas loop
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let animId: number;
    let width = (canvas.width = canvas.offsetWidth);
    let height = (canvas.height = canvas.offsetHeight);

    const handleResize = () => {
      if (!canvas) return;
      width = canvas.width = canvas.offsetWidth;
      height = canvas.height = canvas.offsetHeight;
    };
    window.addEventListener('resize', handleResize);

    let rayAngle = 0;

    const render = () => {
      ctx.clearRect(0, 0, width, height);
      rayAngle += 0.002;

      const nozzleX = width < 1024 ? width * 0.5 : width * 0.65;
      const nozzleY = width < 1024 ? height * 0.20 : height * 0.42;

      // Volumetric Light Rays
      ctx.save();
      ctx.translate(nozzleX, nozzleY);
      const rayCount = 6;
      for (let i = 0; i < rayCount; i++) {
        const currentRayAngle = rayAngle + (i * Math.PI * 2) / rayCount;
        ctx.save();
        ctx.rotate(currentRayAngle);

        const rayGrad = ctx.createRadialGradient(0, 0, 10, 0, 0, width * 0.55);
        rayGrad.addColorStop(0, 'rgba(196, 162, 101, 0.14)');
        rayGrad.addColorStop(0.4, 'rgba(196, 162, 101, 0.03)');
        rayGrad.addColorStop(1, 'rgba(0, 0, 0, 0)');

        ctx.fillStyle = rayGrad;
        ctx.beginPath();
        ctx.moveTo(0, 0);
        ctx.arc(0, 0, width * 0.55, -0.25, 0.25);
        ctx.closePath();
        ctx.fill();
        ctx.restore();
      }
      ctx.restore();

      // Render Atomizer Mist Particles
      const particles = particlesRef.current;
      for (let i = particles.length - 1; i >= 0; i--) {
        const p = particles[i];
        p.life++;
        p.x += p.vx;
        p.y += p.vy;
        p.vx *= 0.96;
        p.vy *= 0.96;
        p.vy += 0.03;

        const progress = p.life / p.maxLife;
        const currentAlpha = p.alpha * (1 - progress);

        if (progress >= 1 || currentAlpha <= 0) {
          particles.splice(i, 1);
          continue;
        }

        ctx.save();
        ctx.shadowBlur = 6;
        ctx.shadowColor = p.color;
        ctx.fillStyle = p.color;
        ctx.globalAlpha = Math.max(0, currentAlpha);
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
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
    <section className="relative min-h-[640px] sm:min-h-[740px] lg:min-h-[820px] bg-[#070707] overflow-hidden flex items-center justify-center border-b border-[#1E1B18] select-none py-10">
      {/* Background Volumetric Rays & Particle Canvas */}
      <canvas
        ref={canvasRef}
        className="absolute inset-0 w-full h-full pointer-events-none z-0"
      />

      {/* Atmospheric Vignette */}
      <div className="absolute inset-0 bg-radial-vignette pointer-events-none z-0" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-center">
          
          {/* 1. Photorealistic Perfume Bottle: STRICTLY ON TOP on Mobile (order-1), Left Column on Desktop (lg:order-2) */}
          <div className="lg:col-span-7 flex justify-center items-center relative order-1 lg:order-2 py-2 sm:py-6">
            
            {/* Dynamic Scent Halo */}
            <div
              className={`absolute w-64 h-64 sm:w-[460px] sm:h-[460px] rounded-full bg-gradient-to-tr from-[#C4A265]/35 via-amber-600/15 to-transparent blur-3xl pointer-events-none transition-all duration-700 ${
                sprayActive
                  ? 'scale-125 opacity-100'
                  : stage >= 1
                  ? 'scale-100 opacity-60'
                  : 'scale-75 opacity-0'
              }`}
            />

            {/* Bottle Container with Smooth Manifestation Transition */}
            <div
              className={`relative w-64 sm:w-80 md:w-[390px] aspect-[3/4] transition-all duration-1000 ease-[cubic-bezier(0.16,1,0.3,1)] ${
                stage >= 1
                  ? 'scale-100 opacity-100 blur-0 translate-y-0'
                  : 'scale-90 opacity-0 blur-2xl translate-y-12'
              }`}
            >
              {/* Seamless Photorealistic Luxury Bottle */}
              <img
                src="/media/orq/royal_oud_photoreal.jpg"
                alt="ORQ Luxury Perfume Bottle"
                className="w-full h-full object-contain filter contrast-110 drop-shadow-[0_25px_60px_rgba(0,0,0,0.95)]"
                style={{
                  maskImage: 'radial-gradient(ellipse 90% 90% at 50% 50%, black 75%, transparent 100%)',
                  WebkitMaskImage: 'radial-gradient(ellipse 90% 90% at 50% 50%, black 75%, transparent 100%)',
                }}
              />

              {/* Nozzle Spray Flash Glow */}
              <div
                className={`absolute top-[18%] left-[50%] -translate-x-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-[#ECC480] blur-md pointer-events-none transition-opacity duration-300 ${
                  sprayActive ? 'opacity-100 scale-125' : 'opacity-0 scale-75'
                }`}
              />

              {/* Bottom Mist & Soft Shadow Pedestal */}
              <div className="w-3/4 h-5 mx-auto -mt-5 bg-black/90 rounded-full blur-md pointer-events-none" />
            </div>

          </div>

          {/* 2. Clean Luxury Typography & Purchasing: BELOW Bottle on Mobile (order-2), Right Column on Desktop (lg:order-1) */}
          <div
            className={`lg:col-span-5 space-y-5 text-center sm:text-right z-10 order-2 lg:order-1 transition-all duration-1000 ease-[cubic-bezier(0.16,1,0.3,1)] ${
              stage >= 2
                ? 'opacity-100 translate-y-0 filter-none'
                : 'opacity-0 translate-y-8 blur-sm pointer-events-none'
            }`}
          >
            {/* Minimal Brand Tag */}
            <div className="space-y-1.5">
              <span className="font-serif tracking-[0.3em] text-[#C4A265] text-xs font-bold uppercase block">
                ORQ HAUTE PARFUMERIE
              </span>
              <h1 className="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight">
                {heroProduct.name}
                {heroProduct.arabicName && (
                  <span className="text-xl sm:text-2xl text-[#ECC480] font-normal mr-2">
                    ({heroProduct.arabicName})
                  </span>
                )}
              </h1>
              <p className="font-serif tracking-widest text-[11px] sm:text-xs text-zinc-300 uppercase">
                THE ATOMIZER BURST • انتعاش عاصف وكاريزما متمردة
              </p>
            </div>

            {/* Price & Status */}
            <div className="pt-1 flex items-baseline justify-center sm:justify-start gap-3">
              <span className="text-2xl sm:text-4xl font-extrabold text-[#ECC480] font-mono">
                {formatPrice(heroProduct.variants[1].price)} ج.م
              </span>
              <span className="text-xs text-zinc-400 font-mono">100 ML • EDP</span>
              <span className="text-xs text-emerald-400 font-semibold">● متوفر بالمخزون</span>
            </div>

            {/* Action Buttons */}
            <div className="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-2">
              <button
                onClick={() => addToCart(heroProduct, '100ml')}
                className="gold-button text-xs sm:text-sm px-7 py-3.5 rounded-full font-bold shadow-2xl shadow-[#C4A265]/25 hover:scale-105 transition-transform flex items-center gap-2"
              >
                <span>أضف للسلة الآن</span>
                <span>🛒</span>
              </button>

              {/* Trigger Atomizer Spray */}
              <button
                onClick={triggerSpray}
                className={`px-4 py-3.5 rounded-full border text-xs font-bold transition flex items-center gap-2 ${
                  sprayActive
                    ? 'bg-[#C4A265] text-black border-white ring-2 ring-white/50 scale-95'
                    : 'bg-[#141210]/90 border-[#38332D] text-[#ECC480] hover:border-[#C4A265]'
                }`}
              >
                <span>💨 أنثر الرذاذ</span>
                <span className="text-[10px] px-2 py-0.5 rounded-full bg-black/40 text-[#ECC480]">
                  {sprayCount}
                </span>
              </button>

              {/* Replay Scene */}
              <button
                onClick={runSequence}
                className="px-4 py-3.5 rounded-full border border-[#38332D] hover:border-[#C4A265] bg-[#141210]/90 text-zinc-400 hover:text-[#ECC480] text-xs font-semibold transition"
                title="إعادة المشهد السينمائي"
              >
                <span>↺</span>
              </button>
            </div>

            {/* Scent Badges */}
            <div className="pt-4 border-t border-[#1E1B18] flex items-center justify-center sm:justify-start gap-4 sm:gap-6 text-xs text-zinc-400 flex-wrap">
              <span className="flex items-center gap-1.5">
                <span className="text-[#C4A265]">✦</span>
                <span>برغموت كالابريا</span>
              </span>
              <span className="flex items-center gap-1.5">
                <span className="text-[#C4A265]">✦</span>
                <span>فلفل سيتشوان واللافندر</span>
              </span>
              <span className="flex items-center gap-1.5">
                <span className="text-[#C4A265]">✦</span>
                <span>الأمبروكسان الفاخر</span>
              </span>
            </div>
          </div>

        </div>
      </div>

      {/* Seamless Bottom Gradient to Connect with Next Section */}
      <div className="absolute bottom-0 inset-x-0 h-28 bg-gradient-to-t from-[#070707] to-transparent pointer-events-none z-10" />

      {/* Seamless Scroll Connection Indicator */}
      <a
        href="#signature"
        className={`absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center gap-1 text-[11px] text-zinc-400 hover:text-[#ECC480] transition-all duration-700 cursor-pointer ${
          stage >= 2 ? 'opacity-90 translate-y-0' : 'opacity-0 translate-y-4 pointer-events-none'
        }`}
      >
        <span className="font-serif tracking-widest uppercase text-[10px] text-[#C4A265]/80">
          تصفح التشكيلة
        </span>
        <span className="animate-scroll-pulse text-base text-[#ECC480]">↓</span>
      </a>
    </section>
  );
}
