'use client';

import React from 'react';
import { ScentMood } from '@/types/product';

interface MoodFilterBarLightProps {
  currentMood: ScentMood;
  onSelectMood: (mood: ScentMood) => void;
}

export default function MoodFilterBarLight({ currentMood, onSelectMood }: MoodFilterBarLightProps) {
  const filters: { id: ScentMood; label: string; icon: string }[] = [
    { id: 'all', label: 'جميع العطور (12)', icon: '✦' },
    { id: 'royal_oud', label: 'عود وهيبة ملكية', icon: '👑' },
    { id: 'night_allure', label: 'جذاب وسهرات', icon: '⚡' },
    { id: 'sporty_fresh', label: 'منعش ورياضي', icon: '🌊' },
    { id: 'warm_formal', label: 'رسمي ودافئ', icon: '💼' },
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
