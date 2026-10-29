'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useCart } from '@/components/cart/CartContext';

export default function Header() {
  const { openCart, totalItems } = useCart();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const pathname = usePathname() || '/';

  // Determine current active base route
  const currentBase = pathname.startsWith('/preview/concept-4')
    ? '/preview/concept-4'
    : pathname.startsWith('/preview/concept-5')
    ? '/preview/concept-5'
    : pathname.startsWith('/preview/concept-2')
    ? '/preview/concept-2'
    : pathname.startsWith('/preview/concept-3')
    ? '/preview/concept-3'
    : '/preview/concept-1';

  return (
    <header className="border-b border-[#221F1C] bg-[#0A0908]/95 backdrop-blur-md sticky top-0 z-30">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
        
        {/* Brand Logo */}
        <Link href={currentBase} className="flex items-center gap-3 group">
          <div className="flex flex-col">
            <span className="font-serif tracking-[0.25em] text-2xl font-bold gold-gradient-text transition group-hover:brightness-110">
              ORQ
            </span>
            <span className="text-[9px] tracking-[0.2em] text-[#C4A265]/80 font-mono -mt-1">
              HAUTE PARFUMERIE
            </span>
          </div>
        </Link>

        {/* Desktop Navigation Links (Always Point to Valid Targets) */}
        <nav className="hidden md:flex items-center gap-8 text-xs font-medium text-[#B9B1A5]">
          <Link href={currentBase} className="hover:text-[#ECC480] transition">
            الرئيسية
          </Link>
          <Link href={`${currentBase}#signature`} className="hover:text-[#ECC480] transition">
            المجموعة العطرية
          </Link>
          <Link href={`${currentBase}#notes`} className="hover:text-[#ECC480] transition">
            الهرم العطري
          </Link>
          <Link href={`${currentBase}#trust`} className="hover:text-[#ECC480] transition">
            الضمان والتوصيل
          </Link>
        </nav>

        {/* Actions: Cart & Mobile Menu Button */}
        <div className="flex items-center gap-3">
          <button
            onClick={openCart}
            className="flex items-center gap-2 px-3.5 py-2 rounded-xl border border-[#38332D] hover:border-[#C4A265] bg-[#161412] text-xs font-semibold transition group min-h-[44px]"
            aria-label="فتح سلة المشتريات"
          >
            <span className="text-[#ECC480] text-sm group-hover:scale-110 transition-transform">🛍️</span>
            <span className="text-zinc-200">السلة</span>
            <span className="bg-[#C4A265] text-black text-[11px] font-bold px-1.5 py-0.2 rounded-full min-w-5 text-center">
              {totalItems}
            </span>
          </button>

          {/* Mobile hamburger */}
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="md:hidden text-[#A0988C] hover:text-white p-2 rounded-lg border border-[#2B2723] min-w-[44px] min-h-[44px] flex items-center justify-center"
            aria-label="القائمة"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
          </button>
        </div>

      </div>

      {/* Mobile Drawer Menu */}
      {mobileMenuOpen && (
        <div className="md:hidden border-t border-[#1E1B18] bg-[#0E0D0C] p-4 space-y-3 text-xs">
          <Link
            href={currentBase}
            onClick={() => setMobileMenuOpen(false)}
            className="block py-2 text-white hover:text-[#ECC480]"
          >
            الرئيسية
          </Link>
          <Link
            href={`${currentBase}#signature`}
            onClick={() => setMobileMenuOpen(false)}
            className="block py-2 text-[#A0988C] hover:text-[#ECC480]"
          >
            المجموعة العطرية
          </Link>
          <Link
            href={`${currentBase}#notes`}
            onClick={() => setMobileMenuOpen(false)}
            className="block py-2 text-[#A0988C] hover:text-[#ECC480]"
          >
            الهرم العطري
          </Link>
          <Link
            href={`${currentBase}#trust`}
            onClick={() => setMobileMenuOpen(false)}
            className="block py-2 text-[#A0988C] hover:text-[#ECC480]"
          >
            الضمان والتوصيل
          </Link>
        </div>
      )}
    </header>
  );
}
