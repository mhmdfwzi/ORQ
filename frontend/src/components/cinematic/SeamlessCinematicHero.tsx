'use client';

import { useEffect, useRef } from 'react';
import { useCart } from '@/components/cart/CartContext';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { formatPrice } from '@/lib/format';

export default function SeamlessCinematicHero() {
  const { products, site } = useCatalog();
  const { addToCart } = useCart();
  const heroProduct = products.find((product) => product.isBestSeller) ?? products[0];
  const variant = heroProduct?.variants.find((item) => item.size === '100ml' && item.inStock)
    ?? heroProduct?.variants.find((item) => item.inStock)
    ?? heroProduct?.variants[0];
  const canvasRef = useRef<HTMLCanvasElement>(null);

  // Ethereal Golden Smoke & Ember Canvas
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    if (!window.matchMedia('(min-width: 64rem) and (prefers-reduced-motion: no-preference)').matches) return;

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

      const originX = width * 0.28;
      const originY = height * 0.72;

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

      ctx.shadowBlur = 0;
      animId = requestAnimationFrame(render);
    };

    render();

    return () => {
      window.removeEventListener('resize', handleResize);
      cancelAnimationFrame(animId);
    };
  }, []);

  if (!heroProduct) return null;

  return (
    <section className="relative isolate overflow-hidden border-b border-[#221F1C] bg-[#070707]">
      <canvas ref={canvasRef} aria-hidden="true" className="pointer-events-none absolute inset-0 -z-10 hidden size-full lg:block" />
      <div className="mx-auto grid max-w-7xl items-center gap-3 px-4 pt-5 pb-8 sm:px-6 lg:min-h-[600px] lg:grid-cols-2 lg:gap-12 lg:px-8 lg:py-10">
        {heroProduct.image && (
          <div className="relative mx-auto w-full max-w-[min(76vw,21rem)] lg:order-2 lg:max-w-[28rem]">
            <div aria-hidden="true" className="absolute inset-[12%] -z-10 rounded-full bg-[#C4A265]/25 blur-3xl" />
            {/* Admin-uploaded product images can use any host. */}
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src={heroProduct.image}
              alt={heroProduct.name}
              fetchPriority="high"
              className="aspect-square w-full object-contain [mask-image:radial-gradient(ellipse,black_56%,transparent_76%)]"
            />
          </div>
        )}

        <div className="space-y-3 text-center lg:order-1 lg:space-y-5 lg:text-start">
          <h1 className="text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-6xl">{heroProduct.name}</h1>
          {heroProduct.arabicName && heroProduct.arabicName !== heroProduct.name && (
            <p className="text-sm font-medium text-[#D6C7B1] sm:text-lg">{heroProduct.arabicName}</p>
          )}
          {variant && (
            <div className="flex flex-wrap items-baseline justify-center gap-2 lg:justify-start">
              <span className="text-xl font-bold text-[#ECC480] sm:text-2xl">{formatPrice(variant.price)} {site.commerce.currency}</span>
              <span className="text-sm text-[#B9B1A5]">{parseInt(variant.size, 10)} مل</span>
            </div>
          )}
          <div className="mx-auto grid max-w-xs grid-cols-2 gap-2 pt-2 lg:mx-0 lg:max-w-sm">
            <button
              type="button"
              disabled={!variant?.inStock}
              onClick={() => variant && addToCart(heroProduct, variant.size)}
              className="gold-button min-h-11 rounded-lg px-2 py-2 text-sm disabled:opacity-60"
            >
              {variant?.inStock ? 'أضف للسلة' : 'غير متوفر'}
            </button>
            <a href="#signature" className="flex min-h-11 items-center justify-center rounded-lg border border-[#44372A] px-2 py-2 text-sm font-semibold text-[#ECC480] hover:border-[#C4A265]">
              تصفّح العطور
            </a>
          </div>
        </div>
      </div>
    </section>
  );
}
