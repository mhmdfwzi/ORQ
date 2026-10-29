'use client';

import React, { useEffect, useRef } from 'react';
import { useCart } from './CartContext';
import { formatPrice } from '@/lib/format';

export default function CartDrawer() {
  const { items, isOpen, closeCart, removeFromCart, updateQuantity, totalAmount, totalItems } = useCart();
  const closeButtonRef = useRef<HTMLButtonElement | null>(null);
  const previouslyFocusedElementRef = useRef<HTMLElement | null>(null);

  // Manage focus trap and Escape key
  useEffect(() => {
    if (isOpen) {
      previouslyFocusedElementRef.current = document.activeElement as HTMLElement;
      // Focus close button on mount
      setTimeout(() => {
        closeButtonRef.current?.focus();
      }, 50);

      const handleKeyDown = (e: KeyboardEvent) => {
        if (e.key === 'Escape') {
          closeCart();
        }
      };

      window.addEventListener('keydown', handleKeyDown);
      return () => {
        window.removeEventListener('keydown', handleKeyDown);
        if (previouslyFocusedElementRef.current) {
          previouslyFocusedElementRef.current.focus();
        }
      };
    }
  }, [isOpen, closeCart]);

  if (!isOpen) return null;

  return (
    <div
      className="fixed inset-0 z-50 overflow-hidden"
      aria-labelledby="cart-title"
      role="dialog"
      aria-modal="true"
    >
      {/* Backdrop */}
      <div 
        className="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" 
        onClick={closeCart}
      />

      <div className="fixed inset-y-0 left-0 max-w-full flex pl-10 sm:pl-16">
        <div className="w-screen max-w-md bg-[#141210] border-r border-[#2C2723] text-[#F2EBDD] shadow-2xl flex flex-col">
          
          {/* Header */}
          <div className="p-5 border-b border-[#24201C] flex items-center justify-between">
            <div className="flex items-center gap-2">
              <h2 id="cart-title" className="text-base font-bold text-white">
                سلة المشتريات
              </h2>
              <span className="text-xs px-2 py-0.5 rounded-full bg-[#C4A265]/20 text-[#C4A265] border border-[#C4A265]/30">
                {totalItems} {totalItems === 1 ? 'عطر' : 'عطور'}
              </span>
            </div>
            <button
              ref={closeButtonRef}
              onClick={closeCart}
              className="text-[#A0988C] hover:text-white p-2 rounded-lg transition min-w-[44px] min-h-[44px] flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-[#C4A265]"
              aria-label="إغلاق سلة المشتريات"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          {/* Items List */}
          <div className="flex-1 overflow-y-auto p-5 space-y-4 hide-scrollbar">
            {items.length === 0 ? (
              <div className="text-center py-12 space-y-3">
                <div className="w-12 h-12 mx-auto rounded-full bg-[#1C1A18] flex items-center justify-center text-2xl">
                  🛒
                </div>
                <div className="text-sm font-semibold text-white">سلتك فارغة حالياً</div>
                <p className="text-xs text-[#A0988C]">استكشف مجموعة ORQ الفاخرة واختر عطر حضورك المميز.</p>
              </div>
            ) : (
              items.map((item) => (
                <div 
                  key={`${item.slug}-${item.size}`} 
                  className="bg-[#1A1815] border border-[#282420] rounded-xl p-3 flex gap-3 items-center"
                >
                  <div className="w-16 h-16 rounded-lg bg-black border border-[#332E28] overflow-hidden relative shrink-0">
                    <img 
                      src={item.image} 
                      alt={item.name} 
                      className="w-full h-full object-cover"
                    />
                  </div>
                  
                  <div className="flex-1 min-w-0">
                    <h3 className="text-xs font-bold text-white truncate">{item.name}</h3>
                    <div className="text-[11px] text-[#A0988C]">الحجم: {item.size}</div>
                    <div className="text-xs font-bold text-[#ECC480] mt-1 whitespace-nowrap">
                      {formatPrice(item.price * item.quantity)} ج.م
                    </div>
                  </div>

                  <div className="flex flex-col items-end gap-2 shrink-0">
                    <button 
                      onClick={() => removeFromCart(item.slug, item.size)}
                      className="text-[11px] text-zinc-500 hover:text-red-400 transition p-1"
                      aria-label={`حذف ${item.name} من السلة`}
                    >
                      حذف
                    </button>
                    <div className="flex items-center border border-[#332E28] rounded-lg bg-[#100F0E]">
                      <button 
                        onClick={() => updateQuantity(item.slug, item.size, item.quantity - 1)}
                        className="w-8 h-8 flex items-center justify-center text-zinc-400 hover:text-white transition focus:outline-none"
                        aria-label="تقليل الكمية"
                      >
                        -
                      </button>
                      <span className="px-2 text-white font-mono text-xs">{item.quantity}</span>
                      <button 
                        onClick={() => updateQuantity(item.slug, item.size, item.quantity + 1)}
                        className="w-8 h-8 flex items-center justify-center text-zinc-400 hover:text-white transition focus:outline-none"
                        aria-label="زيادة الكمية"
                      >
                        +
                      </button>
                    </div>
                  </div>
                </div>
              ))
            )}
          </div>

          {/* Footer Summary */}
          {items.length > 0 && (
            <div className="p-5 border-t border-[#24201C] bg-[#100F0E] space-y-4">
              <div className="space-y-1.5 text-xs">
                <div className="flex justify-between text-[#A0988C]">
                  <span>المجموع الفرعي</span>
                  <span className="whitespace-nowrap">{formatPrice(totalAmount)} ج.م</span>
                </div>
                <div className="flex justify-between text-[#A0988C]">
                  <span>الشحن لجميع المحافظات</span>
                  <span className="text-emerald-400 font-semibold">شحن مجاني</span>
                </div>
                <div className="flex justify-between text-sm font-bold text-white pt-2 border-t border-[#221F1C]">
                  <span>الإجمالي النهائي</span>
                  <span className="text-[#ECC480] font-mono text-base whitespace-nowrap">
                    {formatPrice(totalAmount)} ج.م
                  </span>
                </div>
              </div>

              <div className="space-y-2">
                <button 
                  onClick={() => alert('مرحباً بك! هذه معاينة تجريبية لواجهة المتجر (Preview Mode). شكراً لاختبارك متجر ORQ.')}
                  className="w-full gold-button py-3.5 rounded-xl text-xs font-bold text-center block min-h-[44px]"
                >
                  متابعة الطلب — الدفع كاش عند الاستلام
                </button>
                <div className="text-[10px] text-center text-zinc-500">
                  معاينة تصميم محلية • جميع الأسعار والمنتجات لأغراض العرض والمراجعة
                </div>
              </div>
            </div>
          )}

        </div>
      </div>
    </div>
  );
}
