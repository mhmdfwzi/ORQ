'use client';

import React, { useState } from 'react';
import { PRODUCTS } from '@/data/products';
import ScrollReveal from '@/components/ui/ScrollReveal';

export default function ScentPyramid() {
  const [selectedSlug, setSelectedSlug] = useState('royal-oud');
  const [activeTier, setActiveTier] = useState<'all' | 'top' | 'heart' | 'base'>('all');

  const currentProduct = PRODUCTS.find((p) => p.slug === selectedSlug) || PRODUCTS[0];

  return (
    <div className="bg-[#141210] border border-[#2B2723] rounded-2xl p-6 sm:p-8 space-y-6">
      
      {/* Top Controls */}
      <ScrollReveal direction="up" delay={50}>
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-[#221F1C] pb-5">
          <div>
            <div className="text-[11px] font-bold text-[#C4A265] tracking-widest uppercase">
              THE OLFACTORY ARCHITECTURE
            </div>
            <h2 className="text-lg sm:text-xl font-bold text-white mt-1">
              تشريح الهرم العطري — كيف يتنفس عطر ORQ على بشرتك؟
            </h2>
            <p className="text-xs text-[#8E867B] mt-1">
              كل عطر مصمم ليتدرج عبر 3 مراحل حسية تدوم لساعات طويلة.
            </p>
          </div>

          {/* Perfume Selector */}
          <div className="flex items-center gap-2">
            <span className="text-xs text-[#A0988C]">اختر العطر:</span>
            <select
              value={selectedSlug}
              onChange={(e) => setSelectedSlug(e.target.value)}
              className="bg-[#1A1815] border border-[#38332D] text-[#ECC480] text-xs rounded-lg px-3 py-2 outline-none focus:border-[#C4A265]"
            >
              <option value="royal-oud">عطر متمرد (Royal Oud)</option>
              <option value="oud-ambassador">عطر غرور (Oud Ambassador)</option>
              <option value="chaos">عطر CHAOS (توابل منعشة وعنبر)</option>
              <option value="alpha">عطر ALPHA (أروماتيك وبحري منعش)</option>
              <option value="darknight">عطر DARKNIGHT (أخشاب وبخور مدخن)</option>
              <option value="gigantic">عطر GIGANTIC (فانيلا وتبغ دافئ)</option>
            </select>
          </div>
        </div>
      </ScrollReveal>

      {/* Tier Filter Buttons */}
      <ScrollReveal direction="up" delay={100}>
        <div className="flex items-center gap-2 overflow-x-auto hide-scrollbar text-xs">
          <button
            onClick={() => setActiveTier('all')}
            className={`px-3 py-1.5 rounded-lg font-medium transition ${
              activeTier === 'all'
                ? 'bg-[#C4A265] text-black font-bold'
                : 'bg-[#1C1A18] text-[#A0988C] hover:text-white'
            }`}
          >
            الهرم بالكامل
          </button>
          <button
            onClick={() => setActiveTier('top')}
            className={`px-3 py-1.5 rounded-lg font-medium transition ${
              activeTier === 'top'
                ? 'bg-[#C4A265] text-black font-bold'
                : 'bg-[#1C1A18] text-[#A0988C] hover:text-white'
            }`}
          >
            1. الافتتاحية (أول 15 دقيقة)
          </button>
          <button
            onClick={() => setActiveTier('heart')}
            className={`px-3 py-1.5 rounded-lg font-medium transition ${
              activeTier === 'heart'
                ? 'bg-[#C4A265] text-black font-bold'
                : 'bg-[#1C1A18] text-[#A0988C] hover:text-white'
            }`}
          >
            2. قلب العطر (2 - 5 ساعات)
          </button>
          <button
            onClick={() => setActiveTier('base')}
            className={`px-3 py-1.5 rounded-lg font-medium transition ${
              activeTier === 'base'
                ? 'bg-[#C4A265] text-black font-bold'
                : 'bg-[#1C1A18] text-[#A0988C] hover:text-white'
            }`}
          >
            3. القاعدة والثبات (حتى 14 ساعة)
          </button>
        </div>
      </ScrollReveal>

      {/* Interactive Pyramid Display */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        {/* Tier 1: Top */}
        <ScrollReveal direction="up" delay={150}>
          <div
            className={`p-5 rounded-xl border transition-all duration-500 h-full ${
              activeTier === 'all' || activeTier === 'top'
                ? 'bg-[#181614] border-[#C4A265]/50 shadow-[0_8px_25px_rgba(196,162,101,0.12)]'
                : 'bg-[#100F0E] border-[#221F1C] opacity-40'
            }`}
          >
            <div className="flex items-center justify-between mb-3">
              <span className="text-[10px] font-mono text-[#C4A265] tracking-widest uppercase">
                TIER 01 • TOP NOTES
              </span>
              <span className="text-[10px] text-zinc-500">اللحظة الأولى</span>
            </div>
            <h4 className="text-sm font-bold text-white mb-2">قمة العطر (الانطباع الفوري)</h4>
            <p className="text-[11px] text-[#A0988C] mb-4">
              الشرارة الأولى التي تنطلق فور الرش، مصممة لجذب الانتباه وإنعاش الحواس.
            </p>
            <div className="flex flex-wrap gap-1.5">
              {currentProduct.notes.top.map((note) => (
                <span
                  key={note}
                  className="px-2.5 py-1 rounded-md bg-[#24201C] border border-[#3A332B] text-xs text-[#ECC480] font-medium"
                >
                  ✦ {note}
                </span>
              ))}
            </div>
          </div>
        </ScrollReveal>

        {/* Tier 2: Heart */}
        <ScrollReveal direction="up" delay={250}>
          <div
            className={`p-5 rounded-xl border transition-all duration-500 h-full ${
              activeTier === 'all' || activeTier === 'heart'
                ? 'bg-[#181614] border-[#C4A265]/50 shadow-[0_8px_25px_rgba(196,162,101,0.12)]'
                : 'bg-[#100F0E] border-[#221F1C] opacity-40'
            }`}
          >
            <div className="flex items-center justify-between mb-3">
              <span className="text-[10px] font-mono text-[#C4A265] tracking-widest uppercase">
                TIER 02 • HEART NOTES
              </span>
              <span className="text-[10px] text-zinc-500">شخصية العطر</span>
            </div>
            <h4 className="text-sm font-bold text-white mb-2">قلب العطر (جوهر الرائحة)</h4>
            <p className="text-[11px] text-[#A0988C] mb-4">
              تظهر بعد هدوء الحمضيات لتعبر عن الهوية العميقة للعطر وسر كاريزمته الخاصة.
            </p>
            <div className="flex flex-wrap gap-1.5">
              {currentProduct.notes.heart.map((note) => (
                <span
                  key={note}
                  className="px-2.5 py-1 rounded-md bg-[#24201C] border border-[#3A332B] text-xs text-[#ECC480] font-medium"
                >
                  ✦ {note}
                </span>
              ))}
            </div>
          </div>
        </ScrollReveal>

        {/* Tier 3: Base */}
        <ScrollReveal direction="up" delay={350}>
          <div
            className={`p-5 rounded-xl border transition-all duration-500 h-full ${
              activeTier === 'all' || activeTier === 'base'
                ? 'bg-[#181614] border-[#C4A265]/50 shadow-[0_8px_25px_rgba(196,162,101,0.12)]'
                : 'bg-[#100F0E] border-[#221F1C] opacity-40'
            }`}
          >
            <div className="flex items-center justify-between mb-3">
              <span className="text-[10px] font-mono text-[#C4A265] tracking-widest uppercase">
                TIER 03 • BASE NOTES
              </span>
              <span className="text-[10px] text-zinc-500">الأثر الباقي</span>
            </div>
            <h4 className="text-sm font-bold text-white mb-2">قاعدة العطر (الثبات والفوحان)</h4>
            <p className="text-[11px] text-[#A0988C] mb-4">
              الزيوت العطرية الثقيلة التي تستقر على الملابس والجلد لساعات طويلة وتصنع بصمتك.
            </p>
            <div className="flex flex-wrap gap-1.5">
              {currentProduct.notes.base.map((note) => (
                <span
                  key={note}
                  className="px-2.5 py-1 rounded-md bg-[#24201C] border border-[#3A332B] text-xs text-[#ECC480] font-medium"
                >
                  ✦ {note}
                </span>
              ))}
            </div>
          </div>
        </ScrollReveal>

      </div>

    </div>
  );
}

