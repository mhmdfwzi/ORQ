'use client';

import React from 'react';
import { ScentMood } from '@/types/product';
import { useCatalog } from '@/components/catalog/CatalogContext';

interface MoodFilterBarLightProps {
  currentMood: ScentMood;
  onSelectMood: (mood: ScentMood) => void;
}

export default function MoodFilterBarLight({ currentMood, onSelectMood }: MoodFilterBarLightProps) {
  const { site, products } = useCatalog();
  // Filters (labels/icons) and the total count come from the database.
  const filters: { id: ScentMood; label: string; icon: string }[] = [
    { id: 'all', label: `${site.moods.all_label} (${products.length})`, icon: '✦' },
    ...site.moods.items.map((m) => ({ id: m.id as ScentMood, label: m.label, icon: m.icon })),
  ];

  return (
    <div className="space-y-2.5">
      <div className="text-xs text-[#6B645A] font-semibold flex items-center justify-between">
        <span>اختر طابع حضورك المفضل للتصفية الفورية:</span>
        <span className="text-[10px] text-[#A67C37]">انقر لتحديث التشكيلة مباشرة</span>
      </div>

      <div className="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-1 text-xs">
        {filters.map((f) => (
          <button
            key={f.id}
            onClick={() => onSelectMood(f.id)}
            className={`px-4 py-2.5 rounded-full transition-all whitespace-nowrap flex items-center gap-1.5 shadow-sm ${
              currentMood === f.id
                ? 'bg-gradient-to-r from-[#C4A265] to-[#A37B34] text-white font-bold shadow-md shadow-[#A37B34]/25 scale-102'
                : 'bg-white border border-[#E2DBD0] text-[#44403C] hover:border-[#C4A265] hover:text-[#1C1917]'
            }`}
          >
            <span>{f.icon}</span>
            <span>{f.label}</span>
          </button>
        ))}
      </div>
    </div>
  );
}

