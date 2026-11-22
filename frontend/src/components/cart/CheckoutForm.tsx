'use client';

import React, { useRef, useState } from 'react';
import Link from 'next/link';
import { useCart } from './CartContext';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { formatPrice } from '@/lib/format';
import { OrderError, placeOrder, saveCheckoutSession, type FieldErrors, type PlacedOrder } from '@/lib/orders';

const inputClass =
  'min-h-11 w-full rounded-lg border border-[#40382F] bg-[#100F0E] px-3 py-2 text-base text-white placeholder:text-[#A0988C] focus:outline-none focus:border-[#C4A265]';
const labelClass = 'mb-1 block text-sm font-medium text-[#E2D3BC]';

/** Checkout rendered inside the cart drawer. */
export default function CheckoutForm({ onBack }: { onBack: () => void }) {
  const { items, totalAmount, clearCart, closeCart } = useCart();
  const { commerce, whatsapp_order_confirmation_available: whatsappAvailable, online_payment_available: cardAvailable } = useCatalog().site;
  const shipping = commerce.shipping_cost;
  const [form, setForm] = useState({ name: '', phone: '', governorate: '', address: '', notes: '' });
  const [whatsappRequested, setWhatsappRequested] = useState(false);
  const [errors, setErrors] = useState<FieldErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);
  const [placed, setPlaced] = useState<PlacedOrder | null>(null);
  const [method, setMethod] = useState<'cod' | 'card'>('cod');
  const [email, setEmail] = useState('');
  const attempt = useRef<{ fingerprint: string; key: string } | null>(null);
  const useCard = cardAvailable && method === 'card';

  const set = (key: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) =>
    setForm((prev) => ({ ...prev, [key]: e.target.value }));

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (submitting) return;
    setSubmitting(true);
    setErrors({});
    setFormError(null);
    try {
      const details = {
        ...form,
        notes: form.notes || undefined,
        whatsapp_confirmation_requested: !useCard && whatsappAvailable && whatsappRequested,
      };

      if (useCard) {
        const fingerprint = JSON.stringify([details, email, items]);
        if (attempt.current?.fingerprint !== fingerprint) {
          attempt.current = { fingerprint, key: crypto.randomUUID() };
        }
        const order = await placeOrder({
          ...details,
          payment_method: 'card',
          billing_email: email,
          checkout_key: attempt.current.key,
        }, items);
        const key = order.checkoutKey ?? attempt.current.key;
        saveCheckoutSession(key, { items, url: order.checkoutUrl ?? null });
        window.location.assign(order.checkoutUrl ?? '/payment/result?token=' + key);
        return;
      }

      const order = await placeOrder(details, items);
      clearCart();
      setPlaced(order);
    } catch (err) {
      if (err instanceof OrderError) {
        setErrors(err.fieldErrors);
        setFormError(err.message);
      } else {
        setFormError('حدث خطأ غير متوقع. حاول مرة أخرى.');
      }
    } finally {
      setSubmitting(false);
    }
  };

  if (placed) {
    return (
      <div className="space-y-3 p-5 text-center" role="status">
        <div className="text-lg font-bold text-white">تم استلام طلبك</div>
        <div className="text-sm text-[#A0988C]">
          رقم الطلب: <span className="font-mono text-[#ECC480]">{placed.orderNumber}</span>
        </div>
        <div className="text-sm text-[#A0988C]">
          الإجمالي: <span className="text-[#ECC480] font-bold">{formatPrice(placed.total)} {commerce.currency}</span> — {commerce.payment_note}
        </div>
        <p className="text-sm text-[#B9B1A5]">هنتواصل معك لتأكيد الطلب.</p>
        <Link href="/account" onClick={closeCart} className="inline-flex min-h-11 items-center text-sm font-semibold text-[#ECC480] underline">تابع طلباتك من حسابي</Link>
      </div>
    );
  }

  const fieldError = (key: string) =>
    errors[key]?.[0] ? <div id={`checkout-error-${key}`} className="mt-1 text-sm text-red-400">{errors[key][0]}</div> : null;
  const itemError = Object.entries(errors).find(([key]) => key === 'items' || key.startsWith('items.'))?.[1][0];
  const hasFieldError = [...Object.keys(form), 'billing_email'].some((key) => errors[key]?.length);

  return (
    <form onSubmit={submit} className="min-h-0 flex-1 space-y-3 overflow-y-auto overscroll-contain p-4 pb-[max(1rem,env(safe-area-inset-bottom))]" noValidate>
      <button type="button" onClick={onBack} disabled={submitting} className="min-h-11 text-sm text-[#ECC480] hover:underline">
        رجوع للسلة
      </button>

      <div>
        <label className={labelClass} htmlFor="checkout-name">الاسم بالكامل</label>
        <input id="checkout-name" className={inputClass} autoComplete="name" value={form.name} onChange={set('name')} aria-invalid={!!errors.name} aria-describedby={errors.name ? 'checkout-error-name' : undefined} required />
        {fieldError('name')}
      </div>
      <div>
        <label className={labelClass} htmlFor="checkout-phone">رقم الموبايل</label>
        <input id="checkout-phone" className={inputClass} type="tel" autoComplete="tel" dir="ltr" value={form.phone} onChange={set('phone')} aria-invalid={!!errors.phone} aria-describedby={errors.phone ? 'checkout-error-phone' : undefined} required />
        {fieldError('phone')}
        {whatsappAvailable && !useCard && (
          <label className="mt-1 flex min-h-11 items-center gap-2 text-sm text-[#E2D3BC]">
            <input
              type="checkbox"
              className="size-4 shrink-0 accent-[#C4A265]"
              checked={whatsappRequested}
              onChange={(e) => setWhatsappRequested(e.target.checked)}
            />
            أوافق على استلام تأكيد الطلب من ORQ على واتساب
          </label>
        )}
      </div>
      <div>
        <label className={labelClass} htmlFor="checkout-governorate">المحافظة</label>
        <select id="checkout-governorate" className={inputClass} autoComplete="address-level1" value={form.governorate} onChange={set('governorate')} aria-invalid={!!errors.governorate} aria-describedby={errors.governorate ? 'checkout-error-governorate' : undefined} required>
          <option value="">اختر</option>
          {commerce.governorates.map((g) => (
            <option key={g} value={g}>{g}</option>
          ))}
        </select>
        {fieldError('governorate')}
      </div>
      <div>
        <label className={labelClass} htmlFor="checkout-address">العنوان بالتفصيل</label>
        <textarea id="checkout-address" className={inputClass} rows={2} autoComplete="street-address" value={form.address} onChange={set('address')} aria-invalid={!!errors.address} aria-describedby={errors.address ? 'checkout-error-address' : undefined} required />
        {fieldError('address')}
      </div>
      <div>
        <label className={labelClass} htmlFor="checkout-notes">ملاحظات (اختياري)</label>
        <input id="checkout-notes" className={inputClass} value={form.notes} onChange={set('notes')} aria-invalid={!!errors.notes} aria-describedby={errors.notes ? 'checkout-error-notes' : undefined} />
        {fieldError('notes')}
      </div>

      {cardAvailable && (
        <fieldset className="space-y-2">
          <legend className={labelClass}>طريقة الدفع</legend>
          <label className="flex min-h-11 cursor-pointer items-center gap-2 rounded-lg border border-[#40382F] px-3 text-sm">
            <input className="accent-[#C4A265]" type="radio" name="payment-method" checked={method === 'cod'} onChange={() => setMethod('cod')} />
            الدفع عند الاستلام
          </label>
          <label className="flex min-h-11 cursor-pointer items-center gap-2 rounded-lg border border-[#40382F] px-3 text-sm">
            <input className="accent-[#C4A265]" type="radio" name="payment-method" checked={method === 'card'} onChange={() => setMethod('card')} />
            الدفع الإلكتروني بالبطاقة عبر Paymob
          </label>
        </fieldset>
      )}
      {useCard && (
        <div>
          <label className={labelClass} htmlFor="checkout-email">البريد الإلكتروني للفوترة</label>
          <input id="checkout-email" className={inputClass} type="email" dir="ltr" autoComplete="email" value={email} onChange={(event) => setEmail(event.target.value)} aria-invalid={!!errors.billing_email} aria-describedby={errors.billing_email ? 'checkout-error-billing_email' : undefined} required />
          {fieldError('billing_email')}
        </div>
      )}

      <div role="alert" className="text-sm text-red-400 empty:hidden">
        {itemError ?? (hasFieldError ? 'راجع البيانات الموضّحة في الحقول.' : formError)}
      </div>

      <div className="space-y-2 border-t border-[#221F1C] pt-3">
        <div className="flex justify-between gap-3 text-sm font-bold text-white">
          <span>الإجمالي</span>
          <span className="text-[#ECC480]">{formatPrice(totalAmount + shipping)} {commerce.currency}</span>
        </div>
        <p className="text-sm text-[#B9B1A5]">{useCard ? 'هتكمل الدفع على صفحة Paymob.' : commerce.payment_note} · {shipping === 0 ? commerce.shipping_free_label : 'الشحن ' + formatPrice(shipping) + ' ' + commerce.currency}</p>
        <p className="text-sm leading-6 text-[#B9B1A5]">
          بإتمام الطلب توافق على <Link className="text-[#ECC480] underline" href="/pages/terms" target="_blank">الشروط والأحكام</Link>. اطّلع على <Link className="text-[#ECC480] underline" href="/pages/privacy-policy" target="_blank">سياسة الخصوصية</Link>.
        </p>
      </div>

      <button
        type="submit"
        disabled={submitting}
        className="gold-button block min-h-11 w-full rounded-lg px-3 py-2 text-sm disabled:opacity-60"
      >
        {submitting ? 'جاري إرسال الطلب…' : useCard ? 'متابعة إلى الدفع' : 'تأكيد الطلب'}
      </button>
    </form>
  );
}
