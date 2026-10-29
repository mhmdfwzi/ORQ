'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';

export default function ConceptSwitcherBar() {
  const pathname = usePathname();
  const [collapsed, setCollapsed] = useState(false);

  const isConcept1 = pathname === '/preview/concept-1' || pathname === '/preview' || pathname === '/';
  const isConcept2 = pathname === '/preview/concept-2';
  const isConcept3 = pathname === '/preview/concept-3';
  const isConcept4 = pathname === '/preview/concept-4';
  const isConcept5 = pathname === '/preview/concept-5';

  if (collapsed) {
    return (
      <button
        onClick={() => setCollapsed(false)}
        className="fixed bottom-4 left-4 z-40 bg-[#161412]/95 backdrop-blur-md border border-[#C4A265]/40 text-[#ECC480] text-xs px-3.5 py-2.5 rounded-full shadow-2xl flex items-center gap-1.5 hover:bg-[#221F1C] min-h-[44px]"
        aria-label="عرض شريط تبديل النماذج"
      >
        <span>🎨</span>
        <span>النماذج (5)</span>
      </button>
    );
  }

  return (
    <div className="fixed bottom-3 left-1/2 -translate-x-1/2 z-40 bg-[#12100E]/95 backdrop-blur-md border border-[#332E27] rounded-2xl sm:rounded-full p-1.5 shadow-2xl flex flex-wrap items-center justify-center gap-1 text-xs max-w-[calc(100vw-16px)]">
      <Link
        href="/preview/concept-1"
        className={`px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl sm:rounded-full transition whitespace-nowrap min-h-[38px] flex items-center ${
          isConcept1
            ? 'bg-[#C4A265] text-black font-bold'
            : 'text-zinc-400 hover:text-white'
        }`}
      >
        1. السينمائي
      </Link>

      <Link
        href="/preview/concept-2"
        className={`px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl sm:rounded-full transition whitespace-nowrap min-h-[38px] flex items-center ${
          isConcept2
            ? 'bg-[#C4A265] text-black font-bold'
            : 'text-zinc-400 hover:text-white'
        }`}
      >
        2. العصري
      </Link>

      <Link
        href="/preview/concept-3"
        className={`px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl sm:rounded-full transition whitespace-nowrap min-h-[38px] flex items-center ${
          isConcept3
            ? 'bg-[#C4A265] text-black font-bold'
            : 'text-zinc-400 hover:text-white'
        }`}
      >
        3. المعرض
      </Link>

      <Link
        href="/preview/concept-4"
        className={`px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl sm:rounded-full transition whitespace-nowrap min-h-[38px] flex items-center gap-1 ${
          isConcept4
            ? 'bg-[#C4A265] text-black font-bold ring-1 ring-white/50'
            : 'text-[#ECC480] hover:text-white font-medium'
        }`}
      >
        <span>4. المتكامل</span>
        <span className="text-[10px]">✨</span>
      </Link>

      <Link
        href="/preview/concept-5"
        className={`px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl sm:rounded-full transition whitespace-nowrap min-h-[38px] flex items-center gap-1 ${
          isConcept5
            ? 'bg-[#FAF8F5] text-[#1C1917] font-extrabold ring-1 ring-[#C4A265]'
            : 'text-zinc-300 hover:text-white'
        }`}
      >
        <span>5. أبيض وذهبي</span>
        <span className="text-[10px]">⚪</span>
      </Link>

      <button
        onClick={() => setCollapsed(true)}
        className="text-zinc-500 hover:text-zinc-300 px-2 py-2 text-sm rounded-lg min-w-[36px] min-h-[38px] flex items-center justify-center"
        title="إخفاء شريط المقارنة"
        aria-label="إخفاء شريط المقارنة"
      >
        ✕
      </button>
    </div>
  );
}
