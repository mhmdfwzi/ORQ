'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';
import { useCart } from '@/components/cart/CartContext';
import { useCatalog } from '@/components/catalog/CatalogContext';
import { formatPrice } from '@/lib/format';
import { getPaymentStatus, readCheckoutSession, removeCheckoutSession, type PaymentStatus } from '@/lib/orders';

const messages = {
  pending: ['الدفع قيد التحقق', 'يتم تحديث الحالة فور وصول تأكيد Paymob.'],
  declined: ['لم تُقبل آخر محاولة دفع', 'يمكنك الرجوع إلى صفحة الدفع إذا كانت الجلسة ما زالت متاحة.'],
  awaiting_confirmation: ['ننتظر تحديث حالة الدفع', 'انتهت جلسة الدفع. إذا خُصم المبلغ، تواصل معنا برقم الطلب قبل إعادة الشراء.'],
  expired: ['انتهت جلسة الدفع', 'يمكنك العودة إلى السلة للمحاولة مرة أخرى. إذا خُصم المبلغ، تواصل معنا أولاً برقم الطلب.'],
  paid: ['تم الدفع بنجاح', 'استلمنا دفعتك وطلبك قيد التجهيز.'],
  review: ['وصلت الدفعة وتحتاج إلى مراجعة', 'تواصل معنا برقم الطلب لمراجعة حالة الشحنة أو رد المبلغ.'],
  refunded: ['تم تسجيل رد المبلغ', 'تواصل معنا برقم الطلب إذا احتجت إلى مساعدة.'],
} satisfies Record<PaymentStatus['status'], [string, string]>;

export default function PaymentResult() {
  const token = useSearchParams().get('token');
  if (!token || !/^[a-f0-9]{8}(?:-[a-f0-9]{4}){3}-[a-f0-9]{12}$/i.test(token)) {
    return <p>افتح رابط حالة الدفع من صفحة Paymob لمتابعة العملية.</p>;
  }

  return <PaymentStatusDetails key={token} token={token} />;
}

function PaymentStatusDetails({ token }: { token: string }) {
  const { commerce } = useCatalog().site;
  const { restoreCart } = useCart();
  const [order, setOrder] = useState<PaymentStatus | null>(null);
  const [session, setSession] = useState<ReturnType<typeof readCheckoutSession>>(null);
  const [message, setMessage] = useState('');
  const [revision, setRevision] = useState(0);

  const status = order?.status;
  const canRefresh = !status || ['pending', 'declined', 'awaiting_confirmation'].includes(status);

  useEffect(() => {
    const controller = new AbortController();
    let scheduled: ReturnType<typeof setTimeout>;

    async function refresh(remaining: number) {
      try {
        const latest = await getPaymentStatus(token, controller.signal);
        if (controller.signal.aborted) return;
        if (!latest) {
          setMessage('تعذّر العثور على هذه العملية. راجع رابط حالة الدفع أو تواصل معنا.');
          return;
        }
        setOrder(latest);
        setMessage('');
        if (['paid', 'review', 'refunded'].includes(latest.status)) {
          removeCheckoutSession(token);
          setSession(null);
        } else {
          setSession(readCheckoutSession(token));
        }
        if (remaining > 0 && ['pending', 'declined', 'awaiting_confirmation'].includes(latest.status)) {
          scheduled = setTimeout(() => void refresh(remaining - 1), 4000);
        }
      } catch {
        if (!controller.signal.aborted) {
          setMessage('تعذّر التحقق من حالة الدفع حالياً. حاول تحديث الحالة.');
        }
      }
    }

    void refresh(14);
    return () => {
      controller.abort();
      clearTimeout(scheduled);
    };
  }, [token, revision]);

  return (
    <div className="space-y-5">
      <div className="space-y-2 leading-7 text-[#D2C3AF]" aria-live="polite">
        {status && (
          <>
            <h2 className="text-xl font-bold text-[#ECC480]">{messages[status][0]}</h2>
            <p>{messages[status][1]}</p>
          </>
        )}
        {message && <p className="text-red-300">{message}</p>}
        {!status && !message && <p>جارٍ التحقق من الدفع…</p>}
      </div>
      {order && (
        <dl className="space-y-2 border-y border-[#342820] py-4">
          <div className="flex flex-wrap justify-between gap-2"><dt>رقم الطلب</dt><dd dir="ltr" className="text-[#ECC480]">{order.orderNumber}</dd></div>
          <div className="flex flex-wrap justify-between gap-2"><dt>إجمالي الطلب</dt><dd>{formatPrice(order.total)} {commerce.currency}</dd></div>
        </dl>
      )}
      <div className="flex flex-wrap gap-x-5 gap-y-2">
        {canRefresh && <button className="gold-button min-h-11 rounded-lg px-4 text-sm" type="button" onClick={() => setRevision((value) => value + 1)}>تحديث الحالة</button>}
        {session?.url && (status === 'pending' || status === 'declined') && (
          <a className="inline-flex min-h-11 items-center text-[#ECC480] underline" href={session.url}>العودة إلى Paymob</a>
        )}
        {session?.items.length && status === 'expired' ? (
          <button className="gold-button min-h-11 rounded-lg px-4 text-sm" type="button" onClick={() => restoreCart(session.items)}>استعادة السلة</button>
        ) : null}
        {status === 'paid' && <Link href="/account" className="inline-flex min-h-11 items-center text-[#ECC480] underline">تابع طلباتك من حسابي</Link>}
        <Link href="/pages/contact" className="inline-flex min-h-11 items-center text-[#ECC480] underline">تواصل معنا</Link>
        <Link href="/#signature" className="inline-flex min-h-11 items-center text-[#ECC480] underline">متابعة التسوق</Link>
      </div>
    </div>
  );
}
