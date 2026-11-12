'use client';

import React from 'react';
import ScrollReveal from '@/components/ui/ScrollReveal';
import TrustIcon from '@/components/layout/TrustIcon';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { formatPrice } from '@/lib/format';

export default function FooterLight() {
  const { site, giftBundle } = useCatalog();
  const { footer, commerce } = site;
  const bundlePrice = giftBundle.variants[0]?.price;

  return (
    <footer id="trust" className="border-t border-[#E8E2D5] bg-[#F2EEE7] text-[#6B645A] text-xs pt-14 pb-8 mt-16 scroll-mt-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        {/* Trust Badges Bar */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 pb-10 border-b border-[#E2DBD0] text-center">
          {footer.trust_features.map((f, idx) => (
            <ScrollReveal key={f.title} delay={idx * 100} direction="up">
              <div className="space-y-2.5 p-5 rounded-2xl bg-white border border-[#EAE4D8] hover:border-[#C4A265] transition h-full flex flex-col items-center shadow-sm">
                <div className="p-3 rounded-full bg-[#FAF8F5] border border-[#E8E2D5]">
                  <TrustIcon icon={f.icon} className="w-6 h-6 text-[#996515]" />
                </div>
                <div className="font-bold text-[#1C1917] text-xs">{f.title}</div>
                <p className="text-[11px] text-[#7A7369] leading-relaxed">{f.desc}</p>
              </div>
            </ScrollReveal>
          ))}
        </div>

        {/* Footer Navigation & Brand Section */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8 text-right">
          <div className="md:col-span-5 space-y-3">
            <div className="flex items-center gap-2">
              <img src="/logo-mark.png" alt="" aria-hidden="true" width={30} height={35} className="h-9 w-auto brightness-[.62] saturate-150" />
              <span className="font-serif tracking-[0.25em] text-2xl font-bold bg-gradient-to-r from-[#80581B] to-[#B38734] bg-clip-text text-transparent">
                {site.name}
              </span>
              <span className="text-[9px] tracking-widest text-[#996515] font-mono border-r border-[#DCD5C7] pr-2 mr-1">
                {site.tagline} • WHITE EDITION
              </span>
            </div>
            <p className="text-xs text-[#6B645A] leading-relaxed max-w-sm">{footer.description}</p>
            {footer.trust_features[0] && (
              <div className="flex items-center gap-2 text-[11px] text-[#80581B]">
                <span>🛡️</span>
                <span>{footer.trust_features[0].title}</span>
              </div>
            )}
          </div>

          <div className="md:col-span-3 space-y-2.5">
            <h4 className="font-bold text-[#1C1917] text-xs tracking-wider">{footer.links_title}</h4>
            <ul className="space-y-2 text-xs">
              <li>
                <a href="#signature" className="hover:text-[#A67C37] transition">المجموعة المختارة</a>
              </li>
              <li>
                <a href="#moods" className="hover:text-[#A67C37] transition">التسوق حسب الطابع والشخصية</a>
              </li>
              <li>
                <a href="#gifting" className="hover:text-[#A67C37] transition">
                  {giftBundle.name}
                  {bundlePrice !== undefined && ` (${formatPrice(bundlePrice)} ${commerce.currency})`}
                </a>
              </li>
              <li>
                <a href="#trust" className="hover:text-[#A67C37] transition">{footer.legal_links[0]}</a>
              </li>
            </ul>
          </div>

          <div className="md:col-span-4 space-y-3">
            <h4 className="font-bold text-[#1C1917] text-xs tracking-wider">{footer.support_title}</h4>
            <p className="text-xs text-[#6B645A] leading-relaxed">{footer.support_text}</p>
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-[#E2DBD0] text-[11px] text-[#1C1917] font-medium shadow-sm">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              <span>{footer.support_badge}</span>
            </div>
          </div>
        </div>

        {/* Bottom Copyright */}
        <div className="pt-6 border-t border-[#E2DBD0] flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#8C8478] gap-3">
          <div>{footer.copyright}</div>
          <div className="font-mono text-[#996515]">{site.motto}</div>
        </div>

      </div>
    </footer>
  );
}
