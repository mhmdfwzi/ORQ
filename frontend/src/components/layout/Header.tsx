'use client';

import { Suspense, useEffect, useRef, useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useCart } from '@/components/cart/CartContext';
import { useCatalog } from '@/components/catalog/CatalogContext';
import TrustIcon from '@/components/layout/TrustIcon';
import { collections, home as homeEditorial } from '@/data/store-editorial.json';
import './StoreMobileMenu.css';

const menuIcons = {
  home: 'm3 10 9-7 9 7v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9Zm6 11v-7h6v7',
  perfume: 'M9 3h6v4H9zM10 7v3m4-3v3M8 10h8a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2Zm2 6h4',
  gift: 'M3 8h18v4H3zM5 12v9h14v-9M12 8v13m0-13H8.5A2.5 2.5 0 1 1 11 5.5L12 8Zm0 0h3.5A2.5 2.5 0 1 0 13 5.5L12 8Z',
  notes: 'M12 3c2.5 3.5 6 7.2 6 11a6 6 0 1 1-12 0c0-3.8 3.5-7.5 6-11Zm2.5 13a2.5 2.5 0 0 1-2.5 2.5',
  brand: 'M12 3.5 19.5 12 12 20.5 4.5 12 12 3.5Zm0 5v7m-3.5-3.5h7',
  contact: 'M21 11.5a8 8 0 0 1-8.5 8H5l1.2-3A7.4 7.4 0 0 1 4 11.5a8 8 0 0 1 8.5-8 8 8 0 0 1 8.5 8ZM8.5 11.5h8m-8 3h5',
  blog: 'M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm2 5h8m-8 4h8m-8 4h5',
  night: 'M20.9 13.2A8.8 8.8 0 0 1 10.8 3.1 9 9 0 1 0 20.9 13.2Z',
  quiz: 'M12 2 9.5 9.5 2 12l7.5 2.5L12 22l2.5-7.5L22 12l-7.5-2.5L12 2Z',
  account: 'M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM4 21v-2a5 5 0 0 1 5-5h6a5 5 0 0 1 5 5v2',
};

type NavigationLink = { href: string; label: string; icon: keyof typeof menuIcons | 'delivery' };

const collectionIcons: Record<string, NavigationLink['icon']> = {
  'signature-power-perfumes': 'perfume',
  'fresh-clean-perfumes': 'notes',
  'night-warm-perfumes': 'night',
  'oud-perfumes': 'brand',
};
const blogLink: NavigationLink = { href: '/blog', label: 'المدونة', icon: 'blog' };
const accountLink: NavigationLink = { href: '/account', label: 'حسابي', icon: 'account' };
const supportLinks: NavigationLink[] = [
  { href: '/pages/shipping-policy', label: 'التوصيل', icon: 'delivery' },
  { href: '/pages/contact', label: 'تواصل معنا', icon: 'contact' },
];
const storeLinks: NavigationLink[] = [
  ...collections.map(({ slug, name }) => ({ href: `/collections/${slug}`, label: name, icon: collectionIcons[slug] ?? 'perfume' })),
  { href: '/find-your-scent', label: 'اختار شفرتك', icon: 'quiz' },
  blogLink,
];

export default function Header() {
  return (
    <Suspense fallback={<StoreHeader pathname={null} />}>
      <HeaderForCurrentPath />
    </Suspense>
  );
}

function HeaderForCurrentPath() {
  return <StoreHeader pathname={usePathname()} />;
}

function StoreHeader({ pathname }: { pathname: string | null }) {
  const { openCart, totalItems } = useCart();
  const { site } = useCatalog();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const mobileMenu = useRef<HTMLDialogElement>(null);
  const home = pathname?.match(/^\/preview\/concept-[12356]$/) ? pathname : '/';
  const collectionAnchor = home.endsWith('-6') ? 'collection' : home.endsWith('-3') ? 'signature-line' : 'signature';
  const links: NavigationLink[] = home === '/' ? storeLinks : [
    { href: `${home}#${collectionAnchor}`, label: 'العطور', icon: 'perfume' },
    ...(!home.endsWith('-1') && !home.endsWith('-3') ? [{ href: `${home}#gifting`, label: 'الهدايا', icon: 'gift' as const }] : []),
    ...(home === '/' || home.endsWith('-1') || home.endsWith('-6') ? [{ href: `${home}#notes`, label: 'النفحات', icon: 'notes' as const }] : []),
    blogLink,
  ];
  const mobileLinks: NavigationLink[] = [
    { href: home, label: 'الرئيسية', icon: 'home' },
    accountLink,
    ...links,
    ...supportLinks,
    { href: '/pages/our-story', label: 'من نحن', icon: 'brand' },
  ];

  function closeMobileMenu() {
    mobileMenu.current?.close();
  }

  useEffect(() => {
    const desktop = window.matchMedia('(min-width: 64rem)');
    const closeOnDesktop = (event: MediaQueryListEvent) => {
      if (event.matches) mobileMenu.current?.close();
    };
    desktop.addEventListener('change', closeOnDesktop);
    return () => desktop.removeEventListener('change', closeOnDesktop);
  }, []);

  return (
    <header className="sticky top-0 z-30 border-b border-[#221F1C] bg-[#0A0908]/95 backdrop-blur-md">
      <div className="mx-auto flex h-15 max-w-7xl items-center justify-between gap-3 px-4 sm:h-18 sm:px-6 lg:px-8">
        <Link href={home} aria-label={home === '/' ? homeEditorial.logo_alt : undefined} className="flex min-h-11 min-w-0 items-center gap-2.5">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img src="/logo-mark.png" alt="" width={640} height={631} className="h-8 w-auto shrink-0 mix-blend-screen sm:h-9" />
          <span className="font-serif text-2xl font-bold tracking-widest gold-gradient-text">{site.name}</span>
        </Link>

        <nav aria-label="التنقل الرئيسي" className="hidden items-center gap-4 text-sm text-[#B9B1A5] lg:flex xl:gap-7">
          {links.map(({ href, label }) => (
            <Link key={href} href={href} aria-current={pathname === href ? 'page' : undefined} className="flex min-h-11 items-center whitespace-nowrap hover:text-[#ECC480] aria-[current=page]:text-[#ECC480]">{label}</Link>
          ))}
        </nav>

        <div className="flex shrink-0 items-center gap-2">
          <Link href={accountLink.href} aria-label={accountLink.label} aria-current={pathname === accountLink.href ? 'page' : undefined} className="hidden size-11 place-items-center rounded-lg border border-[#38332D] bg-[#161412] text-[#ECC480] hover:border-[#C4A265] lg:grid">
            <svg aria-hidden="true" className="size-5" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" viewBox="0 0 24 24">
              <path d={menuIcons.account} />
            </svg>
          </Link>
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
            onClick={() => {
              mobileMenu.current?.showModal();
              setMobileMenuOpen(true);
            }}
            className="flex size-11 items-center justify-center rounded-lg border border-[#2B2723] text-[#E2D3BC] hover:text-white lg:hidden"
            aria-label="فتح القائمة"
            aria-expanded={mobileMenuOpen}
            aria-haspopup="dialog"
            aria-controls="store-mobile-menu"
          >
            <svg aria-hidden="true" className="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeWidth="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
          </button>
        </div>
      </div>

      <dialog
        ref={mobileMenu}
        id="store-mobile-menu"
        aria-labelledby="store-mobile-menu-title"
        onClose={() => setMobileMenuOpen(false)}
        onClick={(event) => { if (event.target === event.currentTarget) closeMobileMenu(); }}
        className="store-mobile-menu text-[#F2EBDD]"
      >
        <div className="flex h-full flex-col">
          <div className="flex shrink-0 items-center justify-between gap-3 border-b border-[#463021] px-4 pt-[max(0.75rem,env(safe-area-inset-top))] pb-3 sm:px-6">
            <h2 id="store-mobile-menu-title" className="sr-only">قائمة المتجر</h2>
            <Link href={home} onClick={closeMobileMenu} className="flex min-h-11 items-center gap-2.5">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src="/logo-mark.png" alt="" width={640} height={631} className="h-8 w-auto shrink-0 mix-blend-screen" />
              <span className="font-serif text-2xl font-bold tracking-widest gold-gradient-text">{site.name}</span>
            </Link>
            <button
              type="button"
              autoFocus
              onClick={closeMobileMenu}
              className="grid size-11 shrink-0 place-items-center rounded-lg border border-[#59412C] text-[#E2D3BC] hover:border-[#C4A265] hover:text-[#ECC480]"
              aria-label="إغلاق القائمة"
            >
              <svg aria-hidden="true" className="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeWidth="1.75" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <nav aria-label="التنقل الرئيسي" className="min-h-0 flex-1 overflow-y-auto overscroll-contain px-3 pt-3 pb-[max(1rem,env(safe-area-inset-bottom))] sm:px-5">
            <ul className="space-y-1">
              {mobileLinks.map(({ href, label, icon }) => (
                <li key={href}>
                  <Link
                    href={href}
                    onClick={closeMobileMenu}
                    aria-current={pathname === href ? 'page' : undefined}
                    className="flex min-h-13 items-center gap-3 rounded-xl border border-transparent px-2 py-1.5 text-base font-semibold text-[#F2EBDD] transition-colors hover:bg-[#2C1D13] focus-visible:bg-[#2C1D13] aria-[current=page]:border-[#5A402C] aria-[current=page]:bg-[#2C1D13]"
                  >
                    <span aria-hidden="true" className="grid size-9 shrink-0 place-items-center rounded-lg border border-[#55402D] bg-[#271A13] text-[#ECC480]">
                      {icon === 'delivery' ? <TrustIcon icon="delivery" className="size-5" /> : (
                        <svg className="size-5" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" viewBox="0 0 24 24">
                          <path d={menuIcons[icon]} />
                        </svg>
                      )}
                    </span>
                    {label}
                  </Link>
                </li>
              ))}
            </ul>
          </nav>
        </div>
      </dialog>
    </header>
  );
}
