'use client';

import React, { useEffect, useRef, useState } from 'react';

interface CinematicIntroProps {
  onComplete?: () => void;
}

export default function CinematicIntro({ onComplete }: CinematicIntroProps) {
  const canvasRef = useRef<HTMLCanvasElement | null>(null);
  // Phase 0: Darkness (0 - 0.8s)
  // Phase 1: Golden Smoke Vortex (0.8s - 2.0s)
  // Phase 2: Bottle Emerges from Smoke (2.0s - 3.5s)
  // Phase 3: Logo & Tagline Reveal (3.5s - 5.0s)
  // Phase 4: Fade Out into Store
  const [phase, setPhase] = useState<number>(0);
  const [isDismissed, setIsDismissed] = useState<boolean>(false);

  // Sequence Timeline
  useEffect(() => {
    const t1 = setTimeout(() => setPhase(1), 400);   // Smoke vortex begins
    const t2 = setTimeout(() => setPhase(2), 1500);  // Bottle manifests from mist
    const t3 = setTimeout(() => setPhase(3), 2800);  // Logo & title illuminate
    const t4 = setTimeout(() => {
      // Auto complete sequence after 4.8s
      handleEnter();
    }, 5200);

    return () => {
      clearTimeout(t1);
      clearTimeout(t2);
      clearTimeout(t3);
      clearTimeout(t4);
    };
  }, []);

  const handleEnter = () => {
    setIsDismissed(true);
    setTimeout(() => {
      if (onComplete) onComplete();
    }, 700);
  };

  // High performance golden smoke canvas
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let animId: number;
    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    const handleResize = () => {
      if (!canvas) return;
      width = canvas.width = window.innerWidth;
      height = canvas.height = window.innerHeight;
    };
    window.addEventListener('resize', handleResize);

    // Smoke particles
    interface SmokeParticle {
      x: number;
      y: number;
      radius: number;
      maxRadius: number;
      alpha: number;
      maxAlpha: number;
      growth: number;
      vx: number;
      vy: number;
      angle: number;
      vAngle: number;
    }

    const particles: SmokeParticle[] = [];

    // Embers
    interface Ember {
      x: number;
      y: number;
      size: number;
      vy: number;
      vx: number;
      alpha: number;
    }
    const embers: Ember[] = [];

    const centerX = width / 2;
    const centerY = height * 0.52;

    const render = () => {
      ctx.clearRect(0, 0, width, height);

      // Spawn swirling golden smoke puffs
      if (particles.length < 24 && Math.random() > 0.4) {
        particles.push({
          x: centerX + (Math.random() - 0.5) * 80,
          y: centerY + 60 + Math.random() * 40,
          radius: 20,
          maxRadius: Math.random() * 120 + 90,
          alpha: 0.01,
          maxAlpha: Math.random() * 0.28 + 0.12,
          growth: Math.random() * 0.8 + 0.4,
          vx: (Math.random() - 0.5) * 1.2,
          vy: -(Math.random() * 1.4 + 0.6),
          angle: Math.random() * Math.PI * 2,
          vAngle: (Math.random() - 0.5) * 0.02,
        });
      }

      // Spawn floating golden sparks
      if (embers.length < 50 && Math.random() > 0.3) {
        embers.push({
          x: centerX + (Math.random() - 0.5) * 220,
          y: centerY + 100,
          size: Math.random() * 2.5 + 0.8,
          vy: -(Math.random() * 2 + 1),
          vx: (Math.random() - 0.5) * 1,
          alpha: Math.random() * 0.9 + 0.2,
        });
      }

      // Draw Smoke
      for (let i = particles.length - 1; i >= 0; i--) {
        const p = particles[i];
        p.radius += p.growth;
        p.x += p.vx;
        p.y += p.vy;
        p.angle += p.vAngle;

        if (p.radius < p.maxRadius * 0.5) {
          p.alpha = Math.min(p.maxAlpha, p.alpha + 0.008);
        } else {
          p.alpha -= 0.004;
        }

        if (p.alpha <= 0 || p.radius >= p.maxRadius) {
          particles.splice(i, 1);
          continue;
        }

        const grad = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.radius);
        grad.addColorStop(0, `rgba(212, 175, 55, ${p.alpha})`);
        grad.addColorStop(0.4, `rgba(180, 140, 60, ${p.alpha * 0.6})`);
        grad.addColorStop(1, 'rgba(0, 0, 0, 0)');

        ctx.fillStyle = grad;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
        ctx.fill();
      }

      // Draw Embers
      for (let i = embers.length - 1; i >= 0; i--) {
        const e = embers[i];
        e.y += e.vy;
        e.x += e.vx;
        e.alpha -= 0.008;

        if (e.alpha <= 0 || e.y < 0) {
          embers.splice(i, 1);
          continue;
        }

        ctx.fillStyle = `rgba(236, 196, 128, ${e.alpha})`;
        ctx.shadowBlur = 8;
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

  if (isDismissed) return null;

  return (
    <div
      className={`fixed inset-0 z-50 bg-[#000000] flex flex-col items-center justify-center overflow-hidden transition-opacity duration-1000 select-none ${
        isDismissed ? 'opacity-0 pointer-events-none' : 'opacity-100'
      }`}
    >
      {/* Background Smoke Canvas */}
      <canvas
        ref={canvasRef}
        className="absolute inset-0 w-full h-full pointer-events-none z-0"
      />

      {/* Skip Intro Button in corner */}
      <button
        onClick={handleEnter}
        className="absolute top-6 left-6 z-30 text-[11px] text-[#A0988C] hover:text-[#ECC480] px-3.5 py-1.5 rounded-full border border-zinc-800 bg-black/50 backdrop-blur-md transition flex items-center gap-1.5"
      >
        <span>تخطي الدخلة</span>
        <span>✕</span>
      </button>

      {/* Main Center Stage: Bottle Emerging from Mist */}
      <div className="relative z-10 flex flex-col items-center justify-center px-4 w-full max-w-lg text-center">
        
        {/* Glow Halo behind bottle */}
        <div
          className={`absolute w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-gradient-to-tr from-[#C4A265]/30 to-amber-600/10 blur-3xl pointer-events-none transition-all duration-1000 ${
            phase >= 2 ? 'scale-125 opacity-100' : 'scale-50 opacity-0'
          }`}
        ></div>

        {/* The Perfume Bottle Manifesting */}
        <div className="relative mb-6">
          <div
            className={`relative w-56 sm:w-68 aspect-[3/4] rounded-2xl overflow-hidden border border-[#C4A265]/40 shadow-[0_0_80px_rgba(196,162,101,0.35)] transition-all duration-1000 ease-out ${
              phase >= 2
                ? 'scale-100 opacity-100 blur-0 translate-y-0'
                : 'scale-90 opacity-0 blur-2xl translate-y-12'
            }`}
          >
            <img
              src="/media/orq/11206483-815d-4610-a05d-b5d5fc2397db.jpg"
              alt="ORQ Signature"
              className="w-full h-full object-cover object-center filter contrast-110"
            />

            {/* Sweep light sheen */}
            <div
              className={`absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -skew-x-12 transition-transform duration-1000 ease-out delay-500 pointer-events-none ${
                phase >= 2 ? 'translate-x-full' : '-translate-x-full'
              }`}
            ></div>

            {/* Vignette on image */}
            <div className="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
          </div>
        </div>

        {/* Brand & Punchy Cinematic Tagline (Zero Clutter) */}
        <div
          className={`space-y-3 transition-all duration-1000 transform ${
            phase >= 3
              ? 'opacity-100 translate-y-0 filter-none'
              : 'opacity-0 translate-y-8 blur-sm'
          }`}
        >
          {/* Shimmering Logo */}
          <div className="font-serif tracking-[0.35em] text-3xl sm:text-4xl font-bold gold-gradient-text">
            ORQ
          </div>

          {/* Minimal Punchy Tagline */}
          <div className="font-serif tracking-[0.25em] text-xs sm:text-sm text-zinc-300 uppercase">
            SOME SCENTS ARE FELT, NOT SEEN
          </div>

          <div className="text-sm font-semibold text-[#ECC480] pt-1">
            حضورك يسبق خطوتك
          </div>

          {/* Direct Clean Action */}
          <div className="pt-4">
            <button
              onClick={handleEnter}
              className="gold-button px-8 py-3.5 rounded-full text-xs font-bold shadow-xl shadow-[#C4A265]/30 hover:scale-105 transition-all"
            >
              دخول المتجر ←
            </button>
          </div>
        </div>

      </div>

      {/* Cinematic black bars at top and bottom */}
      <div className="absolute top-0 inset-x-0 h-10 bg-black/60 pointer-events-none z-20"></div>
      <div className="absolute bottom-0 inset-x-0 h-10 bg-black/60 pointer-events-none z-20"></div>
    </div>
  );
}

