'use client';

import { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useCart } from '@/components/cart/CartContext';
import { useCatalog } from '@/components/catalog/CatalogContext';

export default function Header() {
  const { openCart, totalItems } = useCart();
  const { site } = useCatalog();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const pathname = usePathname();
  const home = pathname?.match(/^\/preview\/concept-[12356]$/) ? pathname : '/';
  const collectionAnchor = home.endsWith('-6') ? 'collection' : home.endsWith('-3') ? 'signature-line' : 'signature';
  const links = [
    { href: `${home}#${collectionAnchor}`, label: 'العطور' },
    ...(!home.endsWith('-1') && !home.endsWith('-3') ? [{ href: `${home}#gifting`, label: 'الهدايا' }] : []),
    ...(home === '/' || home.endsWith('-1') || home.endsWith('-6') ? [{ href: `${home}#notes`, label: 'النفحات' }] : []),
    { href: `${home}#trust`, label: 'التوصيل' },
  ];

  return (
    <header className="sticky top-0 z-30 border-b border-[#221F1C] bg-[#0A0908]/95 backdrop-blur-md">
      <div className="mx-auto flex h-15 max-w-7xl items-center justify-between gap-3 px-4 sm:h-18 sm:px-6 lg:px-8">
        <Link href={home} className="flex min-h-11 min-w-0 items-center gap-2.5">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img src="/logo-mark.png" alt="" width={32} height={37} className="h-8 w-auto shrink-0 sm:h-9" />
          <span className="font-serif text-2xl font-bold tracking-widest gold-gradient-text">{site.name}</span>
        </Link>

        <nav aria-label="التنقل الرئيسي" className="hidden items-center gap-8 text-sm text-[#B9B1A5] md:flex">
          {links.map(({ href, label }) => (
            <Link key={href} href={href} className="flex min-h-11 items-center hover:text-[#ECC480]">{label}</Link>
          ))}
        </nav>

        <div className="flex shrink-0 items-center gap-2">
          <button
            type="button"
            onClick={openCart}
            className="flex min-h-11 items-center gap-2 rounded-lg border border-[#38332D] bg-[#161412] px-3 text-xs font-semibold hover:border-[#C4A265]"
            aria-label={`فتح سلة المشتريات، ${totalItems} منتجات`}
          >
            <svg aria-hidden="true" className="size-4 text-[#ECC480]" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M5 8h14l2 13H3L5 8Zm4 2V6a3 3 0 0 1 6 0v4" />
            </svg>
            <span className="hidden sm:inline">السلة</span>
            <span className="min-w-5 rounded-full bg-[#C4A265] px-1.5 text-center font-bold text-black">{totalItems}</span>
          </button>

          <button
            type="button"
            onClick={() => setMobileMenuOpen((open) => !open)}
            className="flex size-11 items-center justify-center rounded-lg border border-[#2B2723] text-[#E2D3BC] hover:text-white md:hidden"
            aria-label="القائمة"
            aria-expanded={mobileMenuOpen}
            aria-controls="store-mobile-nav"
          >
            <svg aria-hidden="true" className="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeWidth="2" d={mobileMenuOpen ? 'M6 18 18 6M6 6l12 12' : 'M4 6h16M4 12h16m-7 6h7'} />
            </svg>
          </button>
        </div>
      </div>

      <nav
        id="store-mobile-nav"
        aria-label="التنقل الرئيسي"
        className={`absolute inset-x-0 top-full grid-cols-2 gap-2 border-y border-[#2B2723] bg-[#0E0D0C] p-3 shadow-xl md:hidden ${mobileMenuOpen ? 'grid' : 'hidden'}`}
      >
        {links.map(({ href, label }) => (
          <Link key={href} href={href} onClick={() => setMobileMenuOpen(false)} className="flex min-h-11 items-center rounded-lg bg-[#171411] px-3 text-sm hover:text-[#ECC480]">
            {label}
          </Link>
        ))}
      </nav>
    </header>
  );
}
