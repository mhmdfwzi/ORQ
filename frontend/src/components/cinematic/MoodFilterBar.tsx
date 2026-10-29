'use client';

import React from 'react';
import { ScentMood } from '@/types/product';

interface MoodFilterBarProps {
  currentMood: ScentMood;
  onSelectMood: (mood: ScentMood) => void;
}

export default function MoodFilterBar({ currentMood, onSelectMood }: MoodFilterBarProps) {
  const filters: { id: ScentMood; label: string; icon: string }[] = [
    { id: 'all', label: 'جميع العطور (12)', icon: '✦' },
    { id: 'royal_oud', label: 'عود وهيبة ملكية', icon: '👑' },
    { id: 'night_allure', label: 'جذاب وسهرات', icon: '⚡' },
    { id: 'sporty_fresh', label: 'منعش ورياضي', icon: '🌊' },
    { id: 'warm_formal', label: 'رسمي ودافئ', icon: '💼' },
  ];

  return (
    <div className="space-y-2">
      <div className="text-xs text-[#A0988C] font-semibold flex items-center justify-between">
        <span>اختر طابع حضورك المفضل للتصفية الفورية:</span>
        <span className="text-[10px] text-[#C4A265]">انقر لتحديث التشكيلة مباشرة</span>
      </div>

      <div className="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-1 text-xs">
        {filters.map((f) => (
          <button
            key={f.id}
            onClick={() => onSelectMood(f.id)}
            className={`px-3.5 py-2 rounded-full transition-all whitespace-nowrap flex items-center gap-1.5 ${
              currentMood === f.id
                ? 'bg-[#C4A265] text-black font-bold shadow-md shadow-[#C4A265]/20'
                : 'bg-[#181614] border border-[#2B2723] text-zinc-300 hover:border-[#C4A265] hover:text-white'
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

