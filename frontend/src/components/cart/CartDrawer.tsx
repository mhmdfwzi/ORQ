'use client';

import { useEffect, useRef, useState } from 'react';
import { useCart } from './CartContext';
import CheckoutForm from './CheckoutForm';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { formatPrice } from '@/lib/format';

export default function CartDrawer() {
  const { isOpen } = useCart();
  return isOpen ? <CartDialog /> : null;
}

function CartDialog() {
  const { items, closeCart, removeFromCart, updateQuantity, totalAmount, totalItems } = useCart();
  const { commerce } = useCatalog().site;
  const shipping = commerce.shipping_cost;
  const [checkingOut, setCheckingOut] = useState(false);
  const dialogRef = useRef<HTMLDialogElement>(null);

  useEffect(() => {
    const dialog = dialogRef.current;
    dialog?.showModal();
    return () => dialog?.close();
  }, []);

  return (
    <dialog
      ref={dialogRef}
      aria-labelledby="cart-title"
      onCancel={(event) => {
        event.preventDefault();
        closeCart();
      }}
      onClick={(event) => {
        if (event.target === event.currentTarget) closeCart();
      }}
      className="cart-dialog fixed inset-y-0 right-auto left-0 m-0 h-dvh max-h-none w-full max-w-none bg-[#141210] text-[#F2EBDD] shadow-2xl backdrop:bg-black/75 sm:max-w-md"
    >
      <div className="flex h-full flex-col">
        <header className="flex shrink-0 items-center justify-between gap-3 border-b border-[#24201C] px-4 py-2">
          <h2 id="cart-title" className="text-base font-bold text-white">
            {checkingOut ? 'بيانات التوصيل' : `السلة (${totalItems})`}
          </h2>
          <button type="button" onClick={closeCart} className="flex size-11 items-center justify-center rounded-lg text-[#B9B1A5] hover:text-white focus-visible:outline-[#C4A265]" aria-label="إغلاق السلة">
            <svg aria-hidden="true" className="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeWidth="2" d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>
        </header>

        {checkingOut ? (
          <CheckoutForm onBack={() => setCheckingOut(false)} />
        ) : (
          <>
            <div className="min-h-0 flex-1 space-y-3 overflow-y-auto overscroll-contain p-4">
              {items.length === 0 ? (
                <p className="py-12 text-center text-sm text-[#B9B1A5]">السلة فارغة</p>
              ) : (
                items.map((item) => (
                  <article key={`${item.slug}-${item.size}`} className="grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-x-3 gap-y-2 rounded-xl border border-[#282420] bg-[#1A1815] p-2.5">
                    {item.image && (
                      // Admin-uploaded product images can use any host.
                      // eslint-disable-next-line @next/next/no-img-element
                      <img src={item.image} alt="" className="size-12 rounded-md object-cover" />
                    )}
                    <div className={item.image ? undefined : 'col-span-2'}>
                      <h3 className="text-sm font-bold break-words text-white">{item.name}</h3>
                      <p className="text-xs text-[#B9B1A5]">{parseInt(item.size, 10)} مل</p>
                    </div>
                    <button type="button" onClick={() => removeFromCart(item.slug, item.size)} className="min-h-11 min-w-11 rounded-md text-xs text-[#B9B1A5] hover:text-red-400" aria-label={`حذف ${item.name}`}>
                      حذف
                    </button>
                    <div className="col-span-3 flex flex-wrap items-center justify-between gap-2">
                      <span className="text-sm font-bold text-[#ECC480]">{formatPrice(item.price * item.quantity)} {commerce.currency}</span>
                      <div className="flex items-center rounded-lg border border-[#332E28] bg-[#100F0E]" role="group" aria-label={`كمية ${item.name}`}>
                        <button type="button" onClick={() => updateQuantity(item.slug, item.size, item.quantity - 1)} className="size-11 text-[#E2D3BC]" aria-label="تقليل الكمية">−</button>
                        <span className="min-w-5 text-center text-sm text-white">{item.quantity}</span>
                        <button type="button" onClick={() => updateQuantity(item.slug, item.size, item.quantity + 1)} className="size-11 text-[#E2D3BC]" aria-label="زيادة الكمية">+</button>
                      </div>
                    </div>
                  </article>
                ))
              )}
            </div>

            {items.length > 0 && (
              <div className="shrink-0 space-y-3 border-t border-[#24201C] bg-[#100F0E] p-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
                <dl className="space-y-1.5 text-sm">
                  <div className="flex justify-between gap-3 text-[#B9B1A5]">
                    <dt>الشحن</dt>
                    <dd>{shipping === 0 ? commerce.shipping_free_label : `${formatPrice(shipping)} ${commerce.currency}`}</dd>
                  </div>
                  <div className="flex justify-between gap-3 font-bold text-white">
                    <dt>الإجمالي</dt>
                    <dd className="text-[#ECC480]">{formatPrice(totalAmount + shipping)} {commerce.currency}</dd>
                  </div>
                </dl>
                <button type="button" onClick={() => setCheckingOut(true)} className="gold-button min-h-11 w-full rounded-lg px-3 text-sm">
                  متابعة الطلب
                </button>
              </div>
            )}
          </>
        )}
      </div>
    </dialog>
  );
}
