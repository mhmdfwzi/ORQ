'use client';

import { useState } from 'react';
import { useCatalog } from '@/components/catalog/CatalogContext';
import type { PerfumeNotes } from '@/types/product';

const tiers: { key: keyof PerfumeNotes; label: string }[] = [
  { key: 'top', label: 'القمة' },
  { key: 'heart', label: 'القلب' },
  { key: 'base', label: 'القاعدة' },
];

export default function ScentPyramid() {
  const { products } = useCatalog();
  const [selectedSlug, setSelectedSlug] = useState(products[0]?.slug);
  const [activeTier, setActiveTier] = useState<keyof PerfumeNotes | 'all'>('all');
  const currentProduct = products.find((product) => product.slug === selectedSlug) ?? products[0];
  if (!currentProduct) return null;

  return (
    <div className="space-y-4 rounded-2xl border border-[#2B2723] bg-[#141210] p-4 sm:p-6">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 className="text-xl font-bold text-white sm:text-2xl">الهرم العطري</h2>
        <select
          aria-label="اختر العطر"
          value={currentProduct.slug}
          onChange={(event) => setSelectedSlug(event.target.value)}
          className="min-h-11 w-full min-w-0 rounded-lg border border-[#38332D] bg-[#1A1815] px-3 text-base text-[#ECC480] sm:w-auto sm:max-w-full"
        >
          {products.map((product) => <option key={product.slug} value={product.slug}>{product.name}</option>)}
        </select>
      </div>

      <div className="grid grid-cols-4 gap-1.5 sm:flex" role="group" aria-label="طبقات العطر">
        {[{ key: 'all' as const, label: 'الكل' }, ...tiers].map(({ key, label }) => (
          <button
            key={key}
            type="button"
            onClick={() => setActiveTier(key)}
            aria-pressed={activeTier === key}
            className={`min-h-11 rounded-lg px-1.5 text-xs font-semibold sm:px-4 sm:text-sm ${activeTier === key
              ? 'bg-[#C4A265] text-black'
              : 'bg-[#211D18] text-[#E2D3BC] hover:text-white'}`}
          >
            {label}
          </button>
        ))}
      </div>

      <dl className="grid gap-2 sm:grid-cols-3 sm:gap-3">
        {tiers.filter(({ key }) => activeTier === 'all' || activeTier === key).map(({ key, label }) => (
          <div key={key} className="rounded-lg border border-[#30281F] bg-[#191612] p-3">
            <dt className="text-sm font-bold text-[#ECC480]">{label}</dt>
            <dd className="mt-1 text-sm leading-relaxed text-[#E2D3BC]">{currentProduct.notes[key].join('، ') || '—'}</dd>
          </div>
        ))}
      </dl>
    </div>
  );
}
