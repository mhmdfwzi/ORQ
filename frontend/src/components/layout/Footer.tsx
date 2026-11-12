'use client';

import React from 'react';
import Link from 'next/link';
import ScrollReveal from '@/components/ui/ScrollReveal';
import TrustIcon from '@/components/layout/TrustIcon';
import { useCatalog } from '@/components/catalog/CatalogContext';

export default function Footer() {
  const { site, products } = useCatalog();
  const { footer } = site;
  // Footer shortcuts follow the catalogue: best sellers first, then the rest.
  const featured = [...products].sort((a, b) => Number(!!b.isBestSeller) - Number(!!a.isBestSeller)).slice(0, 4);

  return (
    <footer id="trust" className="border-t border-[#221F1C] bg-[#0A0908] text-[#8E867B] text-xs pt-12 pb-8 mt-16 scroll-mt-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        {/* Trust Badges Bar */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 pb-10 border-b border-[#1E1B18] text-center">
          {footer.trust_features.map((f, idx) => (
            <ScrollReveal key={f.title} delay={idx * 100} direction="up">
              <div className="space-y-2.5 p-5 rounded-xl bg-[#110F0E] border border-[#221F1C] hover:border-[#C4A265]/40 transition h-full flex flex-col items-center">
                <div className="p-2.5 rounded-full bg-[#181614] border border-[#2B2723]">
                  <TrustIcon icon={f.icon} className="w-6 h-6 text-[#ECC480]" />
                </div>
                <div className="font-bold text-white text-xs">{f.title}</div>
                <p className="text-[11px] text-[#A0988C] leading-relaxed">{f.desc}</p>
              </div>
            </ScrollReveal>
          ))}
        </div>

        {/* Footer Main Columns */}
        <ScrollReveal delay={120} direction="up">
          <div className="grid grid-cols-1 md:grid-cols-12 gap-8 text-[#A0988C]">
            <div className="md:col-span-5 space-y-3">
              <div className="flex items-center gap-3">
                <img src="/logo-mark.png" alt="" aria-hidden="true" width={30} height={35} className="h-9 w-auto" />
                <span className="font-serif tracking-[0.2em] text-xl font-bold gold-gradient-text block">{site.name}</span>
              </div>
              <p className="text-xs text-[#8E867B] leading-relaxed max-w-sm">{footer.description}</p>
              <div className="text-[11px] text-[#C4A265]/80 font-mono">{site.motto}</div>
            </div>

            <div className="md:col-span-3 space-y-2 text-xs">
              <div className="font-bold text-white mb-2">{footer.links_title}</div>
              <ul className="space-y-2">
                {featured.map((p) => (
                  <li key={p.slug}>
                    <Link href={`/preview/products/${p.slug}`} className="hover:text-[#ECC480] transition">
                      {p.name} ({p.arabicName})
                    </Link>
                  </li>
                ))}
              </ul>
            </div>

            <div className="md:col-span-4 space-y-2 text-xs">
              <div className="font-bold text-white mb-2">{footer.support_title}</div>
              <p className="text-[11px] text-[#8E867B] leading-relaxed">{footer.support_text}</p>
              <div className="pt-2">
                <span className="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-[#161412] border border-[#2B2723] text-[#ECC480] text-xs font-medium">
                  <span>✦</span>
                  <span>{footer.support_badge}</span>
                </span>
              </div>
            </div>
          </div>
        </ScrollReveal>

        {/* Copyright */}
        <div className="pt-8 border-t border-[#181614] flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-zinc-500">
          <div>{footer.copyright}</div>
          <div className="flex gap-4">
            {footer.legal_links.map((label) => (
              <span key={label} className="hover:underline cursor-pointer">{label}</span>
            ))}
          </div>
        </div>

      </div>
    </footer>
  );
}
