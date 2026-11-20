'use client';

import { useState, type FormEvent } from 'react';

const API_URL = process.env.NEXT_PUBLIC_API_URL ?? 'http://127.0.0.1:8001/api/v1';
const inputClass = 'mt-1 min-h-11 w-full rounded-lg border border-[#554438] bg-[#100e0c] px-3 py-2 leading-7 text-[#F2EBDD] outline-none focus:border-[#ECC480]';

export default function ContactForm() {
  const [error, setError] = useState('');
  const [sent, setSent] = useState(false);
  const [sending, setSending] = useState(false);

  async function submit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    if (sending) return;

    const form = event.currentTarget;
    const data = Object.fromEntries(new FormData(form));
    setSending(true);
    setError('');
    setSent(false);

    try {
      const response = await fetch(API_URL + '/contact', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify(data),
      });
      if (response.status === 204) {
        form.reset();
        setSent(true);
      } else if (response.status === 429) {
        setError('أرسلت عدة رسائل في وقت قصير. حاول مرة أخرى بعد قليل.');
      } else if (response.status === 422) {
        setError('راجع الاسم والبريد الإلكتروني وتأكد أن الرسالة تتضمن 10 أحرف على الأقل.');
      } else {
        setError('تعذّر إرسال الرسالة. حاول مرة أخرى لاحقاً.');
      }
    } catch {
      setError('تعذّر الاتصال بالمتجر. حاول مرة أخرى.');
    } finally {
      setSending(false);
    }
  }

  return (
    <form onSubmit={submit} className="space-y-4">
      <h2 className="text-xl font-bold text-[#ECC480]">راسل فريق ORQ</h2>
      <div className="grid gap-4 sm:grid-cols-2">
        <label className="block">الاسم
          <input name="name" autoComplete="name" maxLength={100} className={inputClass} required />
        </label>
        <label className="block">البريد الإلكتروني
          <input name="email" type="email" dir="ltr" autoComplete="email" maxLength={254} className={inputClass} required />
        </label>
      </div>
      <label className="block">رسالتك
        <textarea name="message" rows={5} minLength={10} maxLength={2000} className={inputClass} required />
      </label>
      <p className="text-sm leading-6 text-[#C3B29D]">نستخدم بريدك للرد على رسالتك. تجنّب إرفاق بيانات الدفع.</p>
      {error && <p role="alert" className="text-red-300">{error}</p>}
      {sent && <p role="status" className="text-[#ECC480]">وصلت رسالتك إلى فريق ORQ.</p>}
      <button type="submit" disabled={sending} className="gold-button min-h-11 rounded-lg px-6 text-sm disabled:opacity-60">
        {sending ? 'جارٍ الإرسال…' : 'إرسال الرسالة'}
      </button>
    </form>
  );
}
