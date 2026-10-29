'use client';

import React from 'react';
import Link from 'next/link';
import ScrollReveal from '@/components/ui/ScrollReveal';

export default function Footer() {
  const trustFeatures = [
    {
      icon: '🛡️',
      title: 'عينة تجربة مجانية',
      desc: 'اختبر العطر أولاً من العينة المرفقة قبل فتح الزجاجة الأصلية.',
    },
    {
      icon: '🚚',
      title: 'توصيل سريع لكافة المحافظات',
      desc: 'تغليف آمن ومحكم يصلك خلال 24 إلى 48 ساعة فقط.',
    },
    {
      icon: '💵',
      title: 'الدفع عند الاستلام (COD)',
      desc: 'عاين الشحنة واستلم طلبك براحة تامة ثم ادفع نقداً.',
    },
    {
      icon: '👑',
      title: 'زيوت عطرية أصلية نادرة',
      desc: 'تركيزات Eau de Parfum تدوم وتفوح لأكثر من 12 ساعة.',
    },
  ];

  return (
    <footer id="trust" className="border-t border-[#221F1C] bg-[#0A0908] text-[#8E867B] text-xs pt-12 pb-8 mt-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        {/* Trust Badges Bar with Staggered Entrance */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 pb-10 border-b border-[#1E1B18] text-center">
          {trustFeatures.map((f, idx) => (
            <ScrollReveal key={f.title} delay={idx * 110} direction="up">
              <div className="space-y-2 p-4 rounded-xl bg-[#110F0E] border border-[#221F1C] hover:border-[#C4A265]/40 transition h-full">
                <div className="text-2xl">{f.icon}</div>
                <div className="font-bold text-white text-xs">{f.title}</div>
                <p className="text-[11px] text-[#A0988C]">{f.desc}</p>
              </div>
            </ScrollReveal>
          ))}
        </div>

        {/* Footer Main Columns */}
        <ScrollReveal delay={150} direction="up">
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
              <ul className="space-y-1.5">
                <li><Link href="/preview/products/royal-oud" className="hover:text-[#ECC480]">عطر متمرد (Royal Oud)</Link></li>
                <li><Link href="/preview/products/chaos" className="hover:text-[#ECC480]">عطر CHAOS (Sauvage DNA)</Link></li>
                <li><Link href="/preview/products/alpha" className="hover:text-[#ECC480]">عطر ALPHA (Invictus DNA)</Link></li>
                <li><Link href="/preview/products/darknight" className="hover:text-[#ECC480]">عطر DARKNIGHT (Bleu DNA)</Link></li>
              </ul>
            </div>

            <div className="md:col-span-4 space-y-2 text-xs">
              <div className="font-bold text-white mb-2">خدمة العملاء والاستفسارات</div>
              <p className="text-[11px] text-[#8E867B]">فريق العناية بالعملاء متاح يومياً لمساعدتك في اختيار العطر الأنسب لك أو كهدية فاخرة.</p>
              <div className="pt-2 flex items-center gap-2">
                <span className="px-3 py-1.5 rounded bg-[#161412] border border-[#2B2723] text-zinc-300 text-[11px]">
                  واتساب المبيعات: 01000000000
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
