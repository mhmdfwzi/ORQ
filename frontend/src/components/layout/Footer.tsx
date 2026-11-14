'use client';

import Link from 'next/link';
import TrustIcon from '@/components/layout/TrustIcon';
import { useCatalog } from '@/components/catalog/CatalogContext';

export default function Footer() {
  const { site } = useCatalog();
  const { footer } = site;

  return (
    <footer id="trust" className="scroll-mt-20 border-t border-[#221F1C] bg-[#0A0908] py-7 text-[#B9B1A5] sm:py-10">
      <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-2 gap-2 md:grid-cols-4">
          {footer.trust_features.map((feature) => (
            <details key={feature.title} className="rounded-xl border border-[#2B2723] bg-[#14110E]">
              <summary className="flex min-h-12 cursor-pointer list-none items-center gap-2 px-2 py-2 text-xs font-semibold text-[#E2D3BC] [&::-webkit-details-marker]:hidden sm:px-3 sm:text-sm">
                <TrustIcon icon={feature.icon} className="size-5 shrink-0 text-[#ECC480]" />
                <span className="flex-1">{feature.title}</span>
                <span aria-hidden="true" className="text-[#C4A265]">⌄</span>
              </summary>
              <p className="px-3 pb-3 text-xs leading-relaxed">{feature.desc}</p>
            </details>
          ))}
        </div>
        <div className="flex flex-wrap items-center justify-between gap-x-5 border-t border-[#221F1C] pt-3">
          <Link href="/" className="flex min-h-11 items-center font-serif text-xl font-bold tracking-widest gold-gradient-text">{site.name}</Link>
          <p className="text-xs">{footer.copyright}</p>
        </div>
      </div>
    </footer>
  );
}
