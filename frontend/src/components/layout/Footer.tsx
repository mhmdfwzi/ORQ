'use client';

import React from 'react';
import Link from 'next/link';
import ScrollReveal from '@/components/ui/ScrollReveal';

export default function Footer() {
  const trustFeatures = [
    {
      icon: (
        <svg className="w-6 h-6 text-[#ECC480]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
      ),
      title: 'عينة تجربة مجانية',
      desc: 'اختبر العطر أولاً من العينة المرفقة قبل فتح الزجاجة الأصلية.',
    },
    {
      icon: (
        <svg className="w-6 h-6 text-[#ECC480]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
        </svg>
      ),
      title: 'توصيل سريع ومؤمّن',
      desc: 'تغليف فاخر ومحكم يصلك إلى باب بيتك خلال 24 إلى 48 ساعة.',
    },
    {
      icon: (
        <svg className="w-6 h-6 text-[#ECC480]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      ),
      title: 'الدفع عند الاستلام (COD)',
      desc: 'عاين الشحنة واستلم طلبك براحة تامة ثم ادفع نقداً.',
    },
    {
      icon: (
        <svg className="w-6 h-6 text-[#ECC480]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
      ),
      title: 'زيوت نيش أصلية نادرة',
      desc: 'تركيزات Eau de Parfum تدوم وتفوح لأكثر من 12 ساعة.',
    },
  ];

  return (
    <footer id="trust" className="border-t border-[#221F1C] bg-[#0A0908] text-[#8E867B] text-xs pt-12 pb-8 mt-16 scroll-mt-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        {/* Trust Badges Bar with Clean SVG Icons */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 pb-10 border-b border-[#1E1B18] text-center">
          {trustFeatures.map((f, idx) => (
            <ScrollReveal key={f.title} delay={idx * 100} direction="up">
              <div className="space-y-2.5 p-5 rounded-xl bg-[#110F0E] border border-[#221F1C] hover:border-[#C4A265]/40 transition h-full flex flex-col items-center">
                <div className="p-2.5 rounded-full bg-[#181614] border border-[#2B2723]">
                  {f.icon}
                </div>
                <div className="font-bold text-white text-xs">{f.title}</div>
                <p className="text-[11px] text-[#A0988C] leading-relaxed">{f.desc}</p>
              </div>
            </ScrollReveal>
          ))}
        </div>

        {/* Footer Main Columns */}
        <ScrollReveal delay={120} direction="up">
          <div className="grid grid-cols-1 md:grid-cols-12 gap-8 text-[#A0988C]">
            <div className="md:col-span-5 space-y-3">
              <span className="font-serif tracking-[0.2em] text-xl font-bold gold-gradient-text block">ORQ</span>
              <p className="text-xs text-[#8E867B] leading-relaxed max-w-sm">
                دار عطور رجالية متخصصة تصنع الحضور والهيبة من خلال خلاصات عطرية فاخرة. لأن العطر الحقيقي ليس مجرد رائحة.. بل بصمة تسبق كلماتك.
              </p>
              <div className="text-[11px] text-[#C4A265]/80 font-mono">
                OWN YOUR PRESENCE — عطور رجالية فقط
              </div>
            </div>

            <div className="md:col-span-3 space-y-2 text-xs">
              <div className="font-bold text-white mb-2">المجموعات العطرية</div>
              <ul className="space-y-2">
                <li><Link href="/preview/products/royal-oud" className="hover:text-[#ECC480] transition">عطر ROYAL OUD (متمرد)</Link></li>
                <li><Link href="/preview/products/chaos" className="hover:text-[#ECC480] transition">عطر CHAOS (كايوس)</Link></li>
                <li><Link href="/preview/products/alpha" className="hover:text-[#ECC480] transition">عطر ALPHA (ألفا)</Link></li>
                <li><Link href="/preview/products/darknight" className="hover:text-[#ECC480] transition">عطر DARKNIGHT (دارك نايت)</Link></li>
              </ul>
            </div>

            <div className="md:col-span-4 space-y-2 text-xs">
              <div className="font-bold text-white mb-2">خدمة العملاء والاستفسارات</div>
              <p className="text-[11px] text-[#8E867B] leading-relaxed">
                فريق العناية بالعملاء متاح يومياً لمساعدتك في اختيار العطر الأنسب لك أو كهدية فاخرة.
              </p>
              <div className="pt-2">
                <span className="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-[#161412] border border-[#2B2723] text-[#ECC480] text-xs font-medium">
                  <span>✦</span>
                  <span>خدمة عملاء ORQ المباشرة</span>
                </span>
              </div>
            </div>
          </div>
        </ScrollReveal>

        {/* Copyright */}
        <div className="pt-8 border-t border-[#181614] flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-zinc-500">
          <div>© 2026 ORQ Haute Parfumerie. جميع الحقوق محفوظة.</div>
          <div className="flex gap-4">
            <span className="hover:underline cursor-pointer">سياسة الاستبدال والاسترجاع</span>
            <span className="hover:underline cursor-pointer">الشروط والأحكام</span>
            <span className="hover:underline cursor-pointer">سياسة الخصوصية</span>
          </div>
        </div>

      </div>
    </footer>
  );
}
