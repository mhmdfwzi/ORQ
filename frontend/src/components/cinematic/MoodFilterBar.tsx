'use client';

import React from 'react';
import { ScentMood } from '@/types/product';
import { useCatalog } from '@/components/catalog/CatalogContext';

interface MoodFilterBarProps {
  currentMood: ScentMood;
  onSelectMood: (mood: ScentMood) => void;
}

export default function MoodFilterBar({ currentMood, onSelectMood }: MoodFilterBarProps) {
  const { site } = useCatalog();
  const filters: { id: ScentMood; label: string }[] = [
    { id: 'all', label: site.moods.all_label },
    ...site.moods.items.map(({ id, label }) => ({ id: id as ScentMood, label })),
  ];

  return (
    <div role="group" aria-label="تصفية العطور حسب الطابع" className="flex flex-wrap items-center gap-2">
      {filters.map((filter) => (
        <button
          key={filter.id}
          type="button"
          aria-pressed={currentMood === filter.id}
          onClick={() => onSelectMood(filter.id)}
          className={`min-h-11 rounded-full border px-3 text-xs font-medium transition sm:px-4 sm:text-sm ${
            currentMood === filter.id
              ? 'border-[#C4A265] bg-[#C4A265] text-black'
              : 'border-[#2B2723] bg-[#181614] text-zinc-300 hover:border-[#C4A265] hover:text-white'
          }`}
        >
          {filter.label}
        </button>
      ))}
    </div>
  );
}

