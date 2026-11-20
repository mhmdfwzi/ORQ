'use client';

import Link from 'next/link';
import TrustIcon from '@/components/layout/TrustIcon';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { storePageGroups, storePages } from '@/lib/store-pages';

export default function Footer() {
  const { site } = useCatalog();
  const { footer } = site;

  return (
    <footer id="trust" className="scroll-mt-20 border-t border-[#84613A] bg-[radial-gradient(ellipse_at_top_right,#322217,#130e0b_54%,#0a0807)] text-[#C4B7A4]">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="grid gap-7 border-b border-[#62472D]/65 py-8 sm:grid-cols-[1.2fr_1fr] sm:items-end sm:gap-12 sm:py-12">
          <div>
            <Link href="/" className="inline-flex items-center gap-3 text-[#ECC480]">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src="/logo-mark.png" alt="" width={640} height={631} loading="lazy" className="h-10 w-auto mix-blend-screen" />
              <span className="font-serif text-5xl font-semibold leading-none tracking-[0.14em]">{site.name}</span>
            </Link>
            <p className="mt-2 font-serif text-xs tracking-[0.22em] text-[#D7AF76]">{site.tagline}</p>
            <p className="mt-4 max-w-xs text-sm leading-7 text-[#E1CFB8]">{site.creed.quote}</p>
          </div>

          <nav aria-label="روابط الفوتر" className="grid grid-cols-2 gap-x-4 border-t border-[#62472D]/65 pt-2 text-sm font-semibold sm:border-t-0 sm:pt-0">
            {[
              { href: '/#signature', label: 'تسوّق العطور' },
              { href: '/#gifting', label: 'باقات الهدايا' },
              { href: '/#notes', label: 'النفحات العطرية' },
              { href: '/blog', label: 'مدونة ORQ' },
            ].map(({ href, label }) => (
              <Link key={href} href={href} className="flex min-h-11 items-center gap-2 text-[#EDDDC6] transition hover:text-[#ECC480]">
                <span aria-hidden="true" className="text-[#C49C64]">↗</span> {label}
              </Link>
            ))}
          </nav>
        </div>

        <nav aria-label="الهوية وخدمة العملاء والسياسات" className="grid gap-5 border-b border-[#62472D]/65 py-6 sm:grid-cols-3">
          {storePageGroups.map(({ title, slugs }) => (
            <div key={title}>
              <h2 className="mb-1 text-sm font-bold text-[#D7AF76]">{title}</h2>
              <ul className="grid grid-cols-2 gap-x-3 sm:grid-cols-1">
                {slugs.map((slug) => (
                  <li key={slug}>
                    <Link href={'/pages/' + slug} className="inline-flex min-h-11 items-center text-sm text-[#EDDDC6] hover:text-[#ECC480] hover:underline">
                      {storePages[slug].label}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </nav>

        <div className="grid grid-cols-2 items-start gap-2.5 py-6 md:grid-cols-4">
          {footer.trust_features.map((feature) => (
            <details key={feature.title} className="group rounded-lg border border-[#493321] bg-[#110D0B]/80">
              <summary className="flex min-h-14 cursor-pointer list-none items-center gap-1.5 px-2 py-2 text-xs font-semibold text-[#E2D3BC] [&::-webkit-details-marker]:hidden sm:gap-2 sm:px-3 sm:text-sm">
                <TrustIcon icon={feature.icon} className="size-4 shrink-0 text-[#ECC480] sm:size-5" />
                <span className="flex-1">{feature.title}</span>
                <span aria-hidden="true" className="shrink-0 text-[#C4A265] motion-safe:transition-transform motion-safe:group-open:rotate-180">⌄</span>
              </summary>
              <p className="px-3 pb-3 text-xs leading-relaxed">{feature.desc}</p>
            </details>
          ))}
        </div>
        <div className="flex flex-wrap items-center justify-between gap-x-5 border-t border-[#62472D]/65 py-2.5">
          <p className="text-xs leading-relaxed">{footer.copyright}</p>
          <a href="#top" className="flex min-h-11 items-center gap-2 text-xs font-bold text-[#ECC480] hover:underline">
            <span aria-hidden="true">↑</span> للأعلى
          </a>
        </div>
      </div>
    </footer>
  );
}
