'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useCart } from '@/components/cart/CartContext';

export default function HeaderLight() {
  const { openCart, totalItems } = useCart();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const pathname = usePathname() || '/preview/concept-5';

  const currentBase = pathname.startsWith('/preview/concept-5')
    ? '/preview/concept-5'
    : '/preview/concept-1';

  return (
    <header className="border-b border-[#E8E2D5] bg-[#FAF8F5]/90 backdrop-blur-md sticky top-0 z-30 transition-colors shadow-[0_2px_15px_rgba(0,0,0,0.02)]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
        
        {/* Brand Logo */}
        <Link href={currentBase} className="flex items-center gap-3 group">
          <div className="flex flex-col">
            <span className="font-serif tracking-[0.25em] text-2xl font-bold bg-gradient-to-r from-[#80581B] via-[#B38734] to-[#664614] bg-clip-text text-transparent transition group-hover:brightness-110">
              ORQ
            </span>
            <span className="text-[9px] tracking-[0.2em] text-[#996515] font-mono -mt-1 font-semibold">
              HAUTE PARFUMERIE • WHITE EDITION
            </span>
          </div>
        </Link>

        {/* Desktop Navigation Links */}
        <nav className="hidden md:flex items-center gap-8 text-xs font-semibold text-[#575147]">
          <Link href={currentBase} className="hover:text-[#A67C37] transition">
            الرئيسية
          </Link>
          <Link href={`${currentBase}#signature`} className="hover:text-[#A67C37] transition">
            المجموعة العطرية
          </Link>
          <Link href={`${currentBase}#moods`} className="hover:text-[#A67C37] transition">
            الطابع والشخصية
          </Link>
          <Link href={`${currentBase}#gifting`} className="hover:text-[#A67C37] transition">
            باقة الهدايا
          </Link>
          <Link href={`${currentBase}#trust`} className="hover:text-[#A67C37] transition">
            الضمان والتوصيل
          </Link>
        </nav>

        {/* Actions: Cart & Mobile Menu Button */}
        <div className="flex items-center gap-3">
          <button
            onClick={openCart}
            className="flex items-center gap-2 px-3.5 py-2 rounded-xl border border-[#DCD5C7] hover:border-[#C4A265] bg-white text-xs font-semibold transition group min-h-[44px] shadow-sm"
            aria-label="فتح سلة المشتريات"
          >
            <span className="text-sm group-hover:scale-110 transition-transform">🛍️</span>
            <span className="text-[#1C1917] font-bold">السلة</span>
            <span className="bg-gradient-to-r from-[#C4A265] to-[#A37B34] text-white text-[11px] font-extrabold px-1.5 py-0.2 rounded-full min-w-5 text-center shadow-sm">
              {totalItems}
            </span>
          </button>

          {/* Mobile hamburger */}
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="md:hidden text-[#44403C] hover:text-[#1C1917] p-2 rounded-lg border border-[#DCD5C7] bg-white min-w-[44px] min-h-[44px] flex items-center justify-center shadow-sm"
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
        <div className="md:hidden border-t border-[#E8E2D5] bg-[#FAF8F5] p-4 space-y-3 text-xs shadow-lg">
          <Link
            href={currentBase}
            onClick={() => setMobileMenuOpen(false)}
            className="block py-2 text-[#1C1917] font-bold hover:text-[#A67C37]"
          >
            الرئيسية
          </Link>
          <Link
            href={`${currentBase}#signature`}
            onClick={() => setMobileMenuOpen(false)}
            className="block py-2 text-[#575147] hover:text-[#A67C37]"
          >
            المجموعة العطرية
          </Link>
          <Link
            href={`${currentBase}#moods`}
            onClick={() => setMobileMenuOpen(false)}
            className="block py-2 text-[#575147] hover:text-[#A67C37]"
          >
            الطابع والشخصية
          </Link>
          <Link
            href={`${currentBase}#gifting`}
            onClick={() => setMobileMenuOpen(false)}
            className="block py-2 text-[#575147] hover:text-[#A67C37]"
          >
            باقة الهدايا
          </Link>
          <Link
            href={`${currentBase}#trust`}
            onClick={() => setMobileMenuOpen(false)}
            className="block py-2 text-[#575147] hover:text-[#A67C37]"
          >
            الضمان والتوصيل
          </Link>
        </div>
      )}
    </header>
  );
}

