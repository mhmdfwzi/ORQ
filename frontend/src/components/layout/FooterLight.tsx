'use client';

import React from 'react';
import ScrollReveal from '@/components/ui/ScrollReveal';

export default function FooterLight() {
  const trustFeatures = [
    {
      icon: (
        <svg className="w-6 h-6 text-[#996515]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
      ),
      title: 'عينة تجربة مجانية',
      desc: 'اختبر العطر أولاً من العينة المرفقة قبل فتح الزجاجة الأصلية مع ضمان استرجاع كامل.',
    },
    {
      icon: (
        <svg className="w-6 h-6 text-[#996515]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
        </svg>
      ),
      title: 'توصيل سريع ومؤمّن',
      desc: 'تغليف فاخر ومحكم ومقاوم للصدمات يصلك لباب بيتك خلال 24 إلى 48 ساعة.',
    },
    {
      icon: (
        <svg className="w-6 h-6 text-[#996515]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      ),
      title: 'الدفع عند الاستلام (COD)',
      desc: 'عاين الشحنة واستلم طلبك بجميع محتوياته براحة واطمئنان تام.',
    },
    {
      icon: (
        <svg className="w-6 h-6 text-[#996515]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
      ),
      title: 'خلاصات نيش أصلية نادرة',
      desc: 'تركيزات مكثفة تدوم لأكثر من 12 ساعة مع فوحان استثنائي متزن.',
    },
  ];

  return (
    <footer id="trust" className="border-t border-[#E8E2D5] bg-[#F2EEE7] text-[#6B645A] text-xs pt-14 pb-8 mt-16 scroll-mt-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        {/* Trust Badges Bar */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 pb-10 border-b border-[#E2DBD0] text-center">
          {trustFeatures.map((f, idx) => (
            <ScrollReveal key={f.title} delay={idx * 100} direction="up">
              <div className="space-y-2.5 p-5 rounded-2xl bg-white border border-[#EAE4D8] hover:border-[#C4A265] transition h-full flex flex-col items-center shadow-sm">
                <div className="p-3 rounded-full bg-[#FAF8F5] border border-[#E8E2D5]">
                  {f.icon}
                </div>
                <div className="font-bold text-[#1C1917] text-xs">{f.title}</div>
                <p className="text-[11px] text-[#7A7369] leading-relaxed">{f.desc}</p>
              </div>
            </ScrollReveal>
          ))}
        </div>

        {/* Footer Navigation & Brand Section */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8 text-right">
          <div className="md:col-span-5 space-y-3">
            <div className="flex items-center gap-2">
              <span className="font-serif tracking-[0.25em] text-2xl font-bold bg-gradient-to-r from-[#80581B] to-[#B38734] bg-clip-text text-transparent">
                ORQ
              </span>
              <span className="text-[9px] tracking-widest text-[#996515] font-mono border-r border-[#DCD5C7] pr-2 mr-1">
                HAUTE PARFUMERIE • WHITE EDITION
              </span>
            </div>
            <p className="text-xs text-[#6B645A] leading-relaxed max-w-sm">
              دار عطور عربية فاخرة تعيد صياغة العطور الرجالية الأكثر كاريزما وهيبة عالمياً بتركيزات نيش معتقة ومكونات نقية.
            </p>
            <div className="flex items-center gap-2 text-[11px] text-[#80581B]">
              <span>🛡️</span>
              <span>ضمان التجربة الذهبي: عينة مرفقة مجانية مع كل طلب</span>
            </div>
          </div>

          <div className="md:col-span-3 space-y-2.5">
            <h4 className="font-bold text-[#1C1917] text-xs tracking-wider">روابط استكشاف العطور</h4>
            <ul className="space-y-2 text-xs">
              <li>
                <a href="#signature" className="hover:text-[#A67C37] transition">
                  المجموعة المختارة
                </a>
              </li>
              <li>
                <a href="#moods" className="hover:text-[#A67C37] transition">
                  التسوق حسب الطابع والشخصية
                </a>
              </li>
              <li>
                <a href="#gifting" className="hover:text-[#A67C37] transition">
                  صندوق باقة الهدايا (750 ج.م)
                </a>
              </li>
              <li>
                <a href="#trust" className="hover:text-[#A67C37] transition">
                  سياسة الاسترجاع والضمان
                </a>
              </li>
            </ul>
          </div>

          <div className="md:col-span-4 space-y-3">
            <h4 className="font-bold text-[#1C1917] text-xs tracking-wider">خدمة العملاء والطلب الفوري</h4>
            <p className="text-xs text-[#6B645A] leading-relaxed">
              فريقنا متواجد على مدار الساعة لخدمتكم والإجابة على أي استفسارات تخص اختيار العطر الأنسب لشخصيتكم.
            </p>
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-[#E2DBD0] text-[11px] text-[#1C1917] font-medium shadow-sm">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              <span>خدمة العملاء متصلة ومتاحة على مدار الساعة</span>
            </div>
          </div>
        </div>

        {/* Bottom Copyright */}
        <div className="pt-6 border-t border-[#E2DBD0] flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#8C8478] gap-3">
          <div>
            جميع الحقوق محفوظة © 2026 ORQ Haute Parfumerie.
          </div>
          <div className="font-mono text-[#996515]">
            PARFUMS DE LUXE • WHITE EDITION
          </div>
        </div>

      </div>
    </footer>
  );
}
