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

  if (collapsed) {
    return (
      <button
        onClick={() => setCollapsed(false)}
        className="fixed bottom-4 left-4 z-40 bg-[#161412]/90 backdrop-blur-md border border-[#C4A265]/40 text-[#ECC480] text-[11px] px-3 py-1.5 rounded-full shadow-2xl flex items-center gap-1.5 hover:bg-[#221F1C]"
      >
        <span>🎨 تبديل التصميم</span>
      </button>
    );
  }

  return (
    <div className="fixed bottom-4 left-1/2 -translate-x-1/2 z-40 bg-[#12100E]/95 backdrop-blur-md border border-[#332E27] rounded-full p-1 shadow-2xl flex items-center gap-1 text-[11px] max-w-[95vw]">
      <Link
        href="/preview/concept-1"
        className={`px-3 py-1.5 rounded-full transition whitespace-nowrap ${
          isConcept1
            ? 'bg-[#C4A265] text-black font-bold'
            : 'text-zinc-400 hover:text-white'
        }`}
      >
        النموذج 1: السينمائي
      </Link>

      <Link
        href="/preview/concept-2"
        className={`px-3 py-1.5 rounded-full transition whitespace-nowrap ${
          isConcept2
            ? 'bg-[#C4A265] text-black font-bold'
            : 'text-zinc-400 hover:text-white'
        }`}
      >
        النموذج 2: العصري
      </Link>

      <Link
        href="/preview/concept-3"
        className={`px-3 py-1.5 rounded-full transition whitespace-nowrap ${
          isConcept3
            ? 'bg-[#C4A265] text-black font-bold'
            : 'text-zinc-400 hover:text-white'
        }`}
      >
        النموذج 3: المعرض
      </Link>

      <button
        onClick={() => setCollapsed(true)}
        className="text-zinc-500 hover:text-zinc-300 px-1.5 text-xs"
        title="إخفاء"
      >
        ✕
      </button>
    </div>
  );
}
