'use client';

import React, { useEffect, useRef, useState } from 'react';
import Link from 'next/link';

export default function CinematicGenieHero() {
  const canvasRef = useRef<HTMLCanvasElement | null>(null);
  const [revealed, setRevealed] = useState(false);
  const [isHovered, setIsHovered] = useState(false);
  const [mousePos, setMousePos] = useState({ x: 0, y: 0 });

  // Trigger reveal on mount
  useEffect(() => {
    const timer = setTimeout(() => {
      setRevealed(true);
    }, 200);
    return () => clearTimeout(timer);
  }, []);

  const replayAnimation = () => {
    setRevealed(false);
    setTimeout(() => {
      setRevealed(true);
    }, 150);
  };

  // Ethereal golden smoke particles & ember canvas simulation
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let animationFrameId: number;
    let width = (canvas.width = canvas.offsetWidth);
    let height = (canvas.height = canvas.offsetHeight);

    const handleResize = () => {
      if (!canvas) return;
      width = canvas.width = canvas.offsetWidth;
      height = canvas.height = canvas.offsetHeight;
    };
    window.addEventListener('resize', handleResize);

    // Particle classes for golden embers and smoke clouds
    interface Particle {
      x: number;
      y: number;
      size: number;
      speedX: number;
      speedY: number;
      opacity: number;
      fadeSpeed: number;
      color: string;
      wobble: number;
      wobbleSpeed: number;
    }

    const particles: Particle[] = [];
    const particleCount = 45;

    for (let i = 0; i < particleCount; i++) {
      particles.push({
        x: width / 2 + (Math.random() - 0.5) * (width * 0.4),
        y: height * 0.7 + Math.random() * (height * 0.3),
        size: Math.random() * 3 + 1,
        speedX: (Math.random() - 0.5) * 0.8,
        speedY: -(Math.random() * 1.4 + 0.6),
        opacity: Math.random() * 0.7 + 0.2,
        fadeSpeed: Math.random() * 0.006 + 0.003,
        color: Math.random() > 0.3 ? '#ECC480' : '#C4A265',
        wobble: Math.random() * Math.PI * 2,
        wobbleSpeed: Math.random() * 0.03 + 0.01,
      });
    }

    // Smoke Puff particles
    interface SmokePuff {
      x: number;
      y: number;
      radius: number;
      maxRadius: number;
      opacity: number;
      growth: number;
      speedY: number;
      speedX: number;
    }

    const smokePuffs: SmokePuff[] = [];
    let smokeTimer = 0;

    const render = () => {
      ctx.clearRect(0, 0, width, height);

      // 1. Draw Central Golden Vortex Aura
      const centerX = width * 0.45;
      const centerY = height * 0.52;
      const auraGradient = ctx.createRadialGradient(
        centerX,
        centerY,
        30,
        centerX,
        centerY,
        width * 0.35
      );
      auraGradient.addColorStop(0, 'rgba(196, 162, 101, 0.22)');
      auraGradient.addColorStop(0.4, 'rgba(196, 162, 101, 0.08)');
      auraGradient.addColorStop(1, 'rgba(0, 0, 0, 0)');

      ctx.fillStyle = auraGradient;
      ctx.beginPath();
      ctx.arc(centerX, centerY, width * 0.35, 0, Math.PI * 2);
      ctx.fill();

      // 2. Spawn and render smoke puffs rising from the lamp/base
      smokeTimer++;
      if (smokeTimer % 12 === 0 && smokePuffs.length < 18) {
        smokePuffs.push({
          x: centerX + (Math.random() - 0.5) * 80,
          y: height * 0.78,
          radius: 20,
          maxRadius: Math.random() * 80 + 90,
          opacity: 0.18,
          growth: 0.4,
          speedY: -(Math.random() * 0.8 + 0.4),
          speedX: (Math.random() - 0.5) * 0.5,
        });
      }

      for (let i = smokePuffs.length - 1; i >= 0; i--) {
        const p = smokePuffs[i];
        p.y += p.speedY;
        p.x += p.speedX;
        p.radius += p.growth;
        p.opacity -= 0.0012;

        if (p.opacity <= 0 || p.radius >= p.maxRadius) {
          smokePuffs.splice(i, 1);
          continue;
        }

        const smokeGrad = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.radius);
        smokeGrad.addColorStop(0, `rgba(212, 175, 55, ${p.opacity})`);
        smokeGrad.addColorStop(0.5, `rgba(40, 34, 25, ${p.opacity * 0.6})`);
        smokeGrad.addColorStop(1, 'rgba(11, 11, 11, 0)');

        ctx.fillStyle = smokeGrad;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
        ctx.fill();
      }

      // 3. Render floating golden embers rising like vapor essence
      particles.forEach((p) => {
        p.wobble += p.wobbleSpeed;
        p.x += p.speedX + Math.sin(p.wobble) * 0.5;
        p.y += p.speedY;
        p.opacity -= p.fadeSpeed;

        if (p.opacity <= 0 || p.y < 0) {
          p.x = centerX + (Math.random() - 0.5) * (width * 0.35);
          p.y = height * 0.85 + Math.random() * 30;
          p.opacity = Math.random() * 0.8 + 0.3;
          p.size = Math.random() * 3 + 1;
        }

        ctx.save();
        ctx.shadowBlur = 10;
        ctx.shadowColor = p.color;
        ctx.fillStyle = p.color;
        ctx.globalAlpha = Math.max(0, p.opacity);
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
      });

      animationFrameId = requestAnimationFrame(render);
    };

    render();

    return () => {
      window.removeEventListener('resize', handleResize);
      cancelAnimationFrame(animationFrameId);
    };
  }, []);

  // Mouse Parallax Effect
  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    const rect = e.currentTarget.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5;
    const y = (e.clientY - rect.top) / rect.height - 0.5;
    setMousePos({ x, y });
  };

  return (
    <section
      onMouseMove={handleMouseMove}
      className="relative min-h-[620px] sm:min-h-[720px] lg:min-h-[820px] bg-[#070707] overflow-hidden flex items-center justify-center border-b border-[#24201C] select-none"
    >
      {/* Background Interactive Particle Canvas */}
      <canvas
        ref={canvasRef}
        className="absolute inset-0 w-full h-full pointer-events-none z-0"
      />

      {/* Atmospheric Vignette and Deep Shadows */}
      <div className="absolute inset-0 bg-radial-vignette pointer-events-none z-0"></div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full z-10 py-12">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
          
          {/* Right Column: Mystical Brand Text & Hook (RTL) */}
          <div
            className={`lg:col-span-5 space-y-6 text-right transition-all duration-1000 transform ${
              revealed
                ? 'opacity-100 translate-y-0 filter-none'
                : 'opacity-0 translate-y-12 blur-sm'
            }`}
          >
            {/* Supernatural Essence Pill */}
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-[#C4A265]/40 bg-[#161411]/80 backdrop-blur-md text-xs text-[#ECC480] shadow-lg shadow-[#C4A265]/10">
              <span className="w-2 h-2 rounded-full bg-[#C4A265] animate-ping"></span>
              <span className="font-mono tracking-widest text-[11px] uppercase">
                THE GENIE REVEAL • التجربة السينمائية
              </span>
            </div>

            {/* Main Headline */}
            <div className="space-y-2">
              <span className="font-serif tracking-[0.3em] text-[#C4A265] text-xs sm:text-sm font-bold uppercase block">
                SOME SCENTS ARE FELT, NOT SEEN
              </span>
              <h1 className="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight">
                حضورك لا يُرى.. <br />
                <span className="gold-gradient-text">بل يُستحضر كالسحر</span>
              </h1>
            </div>

            <p className="text-sm sm:text-base text-[#B9B1A5] leading-relaxed max-w-lg">
              كأنك فتحت مصباح الأساطير لتتحرر خلاصات العود النادر والزيوت الأرستقراطية. لا نبيع مجرد عطر.. بل نصنع هالة آسرة تلتف حولك وتملأ الأرجاء بهيبة لا تُقاوم.
            </p>

            {/* CTA & Controls */}
            <div className="flex flex-wrap items-center gap-4 pt-2">
              <a
                href="#signature"
                className="gold-button text-xs sm:text-sm px-7 py-4 rounded-xl font-bold flex items-center gap-2 shadow-2xl shadow-[#C4A265]/30 hover:scale-105 transition-transform"
              >
                <span>استكشف إكسير المجموعة</span>
                <span>←</span>
              </a>

              {/* Replay Magic Button */}
              <button
                onClick={replayAnimation}
                className="px-4 py-3.5 rounded-xl border border-[#38332D] hover:border-[#C4A265] bg-[#141210]/80 text-[#ECC480] text-xs font-semibold flex items-center gap-2 transition hover:bg-[#1E1B18]"
                title="إعادة حركة كشف العطر والضباب"
              >
                <span className="text-sm">↺</span>
                <span>إعادة المشهد</span>
              </button>
            </div>

            {/* Scent DNA highlights */}
            <div className="pt-6 border-t border-[#221F1C] flex items-center gap-6 text-xs text-zinc-400">
              <div className="flex items-center gap-2">
                <span className="text-[#C4A265]">✦</span>
                <span>عود ملكي مدخن</span>
              </div>
              <div className="flex items-center gap-2">
                <span className="text-[#C4A265]">✦</span>
                <span>أمبروكسان مغناطيسي</span>
              </div>
              <div className="flex items-center gap-2">
                <span className="text-[#C4A265]">✦</span>
                <span>فوحان أسطوري</span>
              </div>
            </div>
          </div>

          {/* Left Column: The "Genie out of the Lamp" Perfume Bottle Manifestation */}
          <div className="lg:col-span-7 flex justify-center items-center relative py-8">
            
            {/* Golden Shockwave Expanding Rings */}
            <div
              className={`absolute w-72 h-72 sm:w-96 sm:h-96 rounded-full border border-[#C4A265]/20 pointer-events-none transition-all duration-1000 ${
                revealed ? 'scale-125 opacity-40' : 'scale-50 opacity-0'
              }`}
            ></div>
            <div
              className={`absolute w-96 h-96 sm:w-[460px] sm:h-[460px] rounded-full border border-[#ECC480]/15 pointer-events-none transition-all duration-1000 delay-300 ${
                revealed ? 'scale-110 opacity-30' : 'scale-50 opacity-0'
              }`}
            ></div>

            {/* Bottle Pedestal / Altar with Mystical Smoke Base */}
            <div
              style={{
                transform: `perspective(1000px) rotateY(${mousePos.x * 12}deg) rotateX(${
                  -mousePos.y * 12
                }deg)`,
              }}
              className="relative w-full max-w-md sm:max-w-lg transition-transform duration-200 ease-out"
            >
              {/* Golden Lamp Glow Underneath */}
              <div className="absolute -bottom-8 left-1/2 -translate-x-1/2 w-3/4 h-24 bg-gradient-to-t from-[#C4A265]/40 via-[#C4A265]/15 to-transparent rounded-full blur-2xl pointer-events-none"></div>

              {/* The Bottle Frame - Condensing from Smoke into Reality */}
              <div
                className={`relative aspect-[4/3] sm:aspect-[4/3.2] rounded-3xl bg-[#090909] border-2 overflow-hidden shadow-2xl transition-all duration-1000 ease-out ${
                  revealed
                    ? 'border-[#C4A265]/60 shadow-[0_0_60px_rgba(196,162,101,0.25)] scale-100 blur-0 opacity-100'
                    : 'border-transparent scale-90 blur-xl opacity-0'
                }`}
              >
                {/* Image of the bottle emerging */}
                <img
                  src="/media/orq/11206483-815d-4610-a05d-b5d5fc2397db.jpg"
                  alt="ORQ Haute Parfumerie Genie Bottle"
                  className="w-full h-full object-cover object-center filter contrast-110 hover:scale-105 transition-transform duration-700"
                />

                {/* Shimmering Glass Light Beam passing across */}
                <div
                  className={`absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -skew-x-12 pointer-events-none transition-transform duration-1000 delay-500 ${
                    revealed ? 'translate-x-full' : '-translate-x-full'
                  }`}
                ></div>

                {/* Smoke Overlay Effect at the bottom */}
                <div className="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent pointer-events-none"></div>

                {/* Floating Essence Badge */}
                <div className="absolute bottom-4 right-4 left-4 flex items-center justify-between text-xs backdrop-blur-md bg-black/60 p-3 rounded-xl border border-zinc-800">
                  <div className="flex items-center gap-2">
                    <span className="text-base animate-bounce">✨</span>
                    <div>
                      <div className="text-white font-bold">عطر متمرد (Royal Oud)</div>
                      <div className="text-[10px] text-[#A0988C]">إصدار النيش الملكي • خلاصة معتقة</div>
                    </div>
                  </div>
                  <div className="text-right">
                    <div className="text-xs font-bold text-[#ECC480] font-mono">1,999 ج.م</div>
                    <div className="text-[9px] text-emerald-400">100 ML • EDP</div>
                  </div>
                </div>
              </div>

              {/* Mystical Genie Tendril Smoke Badge below bottle */}
              <div className="text-center mt-4">
                <span className="text-[11px] font-mono text-[#C4A265]/80 tracking-widest uppercase">
                  ✦ CONCENTRATED ESSENCE OF DOMINANCE ✦
                </span>
              </div>
            </div>

          </div>

        </div>
      </div>
    </section>
  );
}

