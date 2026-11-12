'use client';

import React, { useState } from 'react';
import { useCart } from './CartContext';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { formatPrice } from '@/lib/format';
import { OrderError, placeOrder, type FieldErrors, type PlacedOrder } from '@/lib/orders';

const inputClass =
  'w-full bg-[#100F0E] border border-[#332E28] rounded-lg px-3 py-2.5 text-xs text-white placeholder:text-zinc-600 focus:outline-none focus:border-[#C4A265] min-h-[44px]';

/** Guest checkout (cash on delivery) rendered inside the cart drawer footer. */
export default function CheckoutForm({ onBack }: { onBack: () => void }) {
  const { items, totalAmount, clearCart } = useCart();
  const { commerce } = useCatalog().site;
  const shipping = commerce.shipping_cost;
  const [form, setForm] = useState({ name: '', phone: '', governorate: '', address: '', notes: '' });
  const [errors, setErrors] = useState<FieldErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);
  const [placed, setPlaced] = useState<PlacedOrder | null>(null);

  const set = (key: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) =>
    setForm((prev) => ({ ...prev, [key]: e.target.value }));

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (submitting) return;
    setSubmitting(true);
    setErrors({});
    setFormError(null);
    try {
      const order = await placeOrder({ ...form, notes: form.notes || undefined }, items);
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
      <div className="p-5 border-t border-[#24201C] bg-[#100F0E] space-y-3 text-center" role="status">
        <div className="text-3xl">✅</div>
        <div className="text-sm font-bold text-white">تم استلام طلبك بنجاح</div>
        <div className="text-xs text-[#A0988C]">
          رقم الطلب: <span className="font-mono text-[#ECC480]">{placed.orderNumber}</span>
        </div>
        <div className="text-xs text-[#A0988C]">
          الإجمالي: <span className="text-[#ECC480] font-bold">{formatPrice(placed.total)} {commerce.currency}</span> — {commerce.payment_note}
        </div>
        <p className="text-[11px] text-zinc-500">سنتواصل معك على رقم الموبايل لتأكيد الطلب.</p>
      </div>
    );
  }

  const fieldError = (key: string) =>
    errors[key]?.[0] ? <div className="text-[11px] text-red-400 mt-1">{errors[key][0]}</div> : null;

  return (
    <form onSubmit={submit} className="p-5 border-t border-[#24201C] bg-[#100F0E] space-y-3 max-h-[60vh] overflow-y-auto" noValidate>
      <div className="flex items-center justify-between">
        <h3 className="text-sm font-bold text-white">بيانات التوصيل</h3>
        <button type="button" onClick={onBack} className="text-[11px] text-[#A0988C] hover:text-white transition">
          رجوع للسلة
        </button>
      </div>

      <div>
        <input className={inputClass} placeholder="الاسم بالكامل" autoComplete="name" value={form.name} onChange={set('name')} required />
        {fieldError('name')}
      </div>
      <div>
        <input className={inputClass} placeholder="رقم الموبايل (01xxxxxxxxx)" inputMode="tel" autoComplete="tel" dir="ltr" value={form.phone} onChange={set('phone')} required />
        {fieldError('phone')}
      </div>
      <div>
        <select className={inputClass} value={form.governorate} onChange={set('governorate')} required>
          <option value="">اختر المحافظة</option>
          {commerce.governorates.map((g) => (
            <option key={g} value={g}>{g}</option>
          ))}
        </select>
        {fieldError('governorate')}
      </div>
      <div>
        <textarea className={inputClass} rows={2} placeholder="العنوان بالتفصيل (الشارع، رقم العقار، علامة مميزة)" autoComplete="street-address" value={form.address} onChange={set('address')} required />
        {fieldError('address')}
      </div>
      <div>
        <input className={inputClass} placeholder="ملاحظات (اختياري)" value={form.notes} onChange={set('notes')} />
      </div>

      {fieldError('items')}
      {formError && !errors.items && Object.keys(errors).length === 0 && (
        <div className="text-[11px] text-red-400" role="alert">{formError}</div>
      )}

      <div className="flex justify-between text-sm font-bold text-white pt-2 border-t border-[#221F1C]">
        <span>الإجمالي ({shipping === 0 ? commerce.shipping_free_label : `${commerce.shipping_note}: ${formatPrice(shipping)} ${commerce.currency}`})</span>
        <span className="text-[#ECC480] font-mono whitespace-nowrap">{formatPrice(totalAmount + shipping)} {commerce.currency}</span>
      </div>

      <button
        type="submit"
        disabled={submitting}
        className="w-full gold-button py-3.5 rounded-xl text-xs font-bold text-center block min-h-[44px] disabled:opacity-60"
      >
        {submitting ? 'جاري إرسال الطلب...' : `تأكيد الطلب — ${commerce.payment_note}`}
      </button>
    </form>
  );
}
