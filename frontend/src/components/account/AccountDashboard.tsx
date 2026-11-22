'use client';

import { useEffect, useState, type FormEvent } from 'react';
import Link from 'next/link';
import { accountMutation, accountRequest, AccountError, type AccountOrder, type AccountOrderDetails, type AccountSession, type Customer } from '@/lib/account';
import { formatPrice } from '@/lib/format';

const sections = [
  { key: 'orders', label: 'طلباتي', icon: 'M4 7h16v13H4zM8 7V4h8v3m-8 5h8m-8 4h5' },
  { key: 'payments', label: 'المعاملات', icon: 'M3 6h18v12H3zM3 10h18m-14 5h4' },
  { key: 'profile', label: 'بياناتي', icon: 'M16 8a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM4 21v-2a5 5 0 0 1 5-5h6a5 5 0 0 1 5 5v2' },
] as const;
type Section = (typeof sections)[number]['key'];
type Page = { data: AccountOrder[]; nextPage: number | null };

const statuses: Record<string, string> = {
  pending: 'تم استلام الطلب', confirmed: 'تم التأكيد', processing: 'قيد التجهيز',
  shipped: 'تم الشحن', delivered: 'تم التسليم', cancelled: 'ملغي',
};
const paymentStatuses: Record<string, string> = { pending: 'في انتظار الدفع', paid: 'مدفوع', refunded: 'تم رد المبلغ' };
const methods: Record<string, string> = { cod: 'الدفع عند الاستلام', card: 'بطاقة إلكترونية', paypal: 'PayPal' };
const dateFormat = new Intl.DateTimeFormat('ar-EG', { dateStyle: 'medium', timeZone: 'Africa/Cairo' });
const panel = 'rounded-2xl border border-[#3D2E23] bg-[#16110D] p-4 sm:p-6';
const textLink = 'inline-flex min-h-11 items-center font-semibold text-[#ECC480] underline underline-offset-4';
const money = (value: number) => formatPrice(value) + ' ج.م';
const date = (value: string) => dateFormat.format(new Date(value));

function Status({ order, payment }: { order: AccountOrder; payment?: boolean }) {
  const neutral = 'bg-[#3B2C1D] text-[#F2CE95]';
  const cancelled = !payment && order.status === 'cancelled';
  const good = payment ? order.paymentStatus === 'paid' : order.status === 'delivered';
  const tone = cancelled ? 'bg-[#3A211A] text-[#F3AA9B]' : good ? 'bg-[#203325] text-[#B7DAB6]' : neutral;
  const label = payment ? paymentStatuses[order.paymentStatus] : statuses[order.status];
  return <span className={'inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold ' + tone}>{label ?? 'الحالة غير متاحة'}</span>;
}

function OrderDetails({ order }: { order: AccountOrderDetails }) {
  const totals = [
    ['قيمة المنتجات', order.subtotal],
    ['الشحن', order.shippingCost],
    ...(order.discount > 0 ? [['الخصم', -order.discount] as const] : []),
    ['الإجمالي', order.total],
  ] as const;
  return (
    <div className="space-y-5 border-t border-[#3D2E23] px-4 py-4 text-sm sm:px-5">
      <div className="grid gap-5 md:grid-cols-2">
        <section>
          <h3 className="mb-3 font-bold text-[#F5EAD9]">متابعة الطلب</h3>
          <ol aria-label="مراحل الطلب" className="space-y-2.5 border-s border-[#53402F] ps-4 text-sm">
            {order.timeline.filter(step => order.status !== 'cancelled' || step.completed).map(step => (
              <li key={step.status} aria-current={step.status === order.status ? 'step' : undefined} className={'flex flex-wrap items-center justify-between gap-x-2 ' + (step.completed ? 'text-[#F4DEBE]' : 'text-[#A99C8C]')}>
                <span>{step.completed ? '✓ ' : '○ '}{statuses[step.status] ?? step.status}</span>
                {step.date && <time dateTime={step.date} className="text-xs">{date(step.date)}</time>}
              </li>
            ))}
          </ol>
        </section>
        <section className="space-y-2 text-[#D8C7B3]">
          <h3 className="mb-3 font-bold text-[#F5EAD9]">التوصيل والدفع</h3>
          {order.address && <p>{order.address}</p>}
          {order.shippingCarrier && <p>شركة الشحن <bdi>{order.shippingCarrier}</bdi></p>}
          {order.trackingNumber && <p>رقم التتبع <bdi className="font-semibold text-[#F2CF9D]">{order.trackingNumber}</bdi></p>}
          <p>{methods[order.paymentMethod] ?? 'طريقة الدفع غير متاحة'}</p>
          <Status order={order} payment />
        </section>
      </div>
      <section className="border-t border-[#3D2E23] pt-4">
        <h3 className="mb-2 font-bold text-[#F5EAD9]">المنتجات</h3>
        <ul className="divide-y divide-[#30251D]">
          {order.items.map((item, index) => (
            <li key={item.name + index} className="flex justify-between gap-3 py-2.5 text-[#DED1BC]">
              <div><bdi className="font-semibold">{item.name}</bdi>
                <span className="ms-2 text-xs text-[#C2A98F]">{item.quantity} × {item.size ? parseInt(item.size, 10) + ' مل' : 'قطعة'}</span>
              </div>
              <bdi>{money(item.total)}</bdi>
            </li>
          ))}
        </ul>
        <dl className="ms-auto mt-3 max-w-xs space-y-2 rounded-lg bg-[#211810] p-3 text-[#D8C7B3]">
          {totals.map(([label, amount]) => (
            <div key={label} className="flex justify-between gap-2 last:font-bold last:text-[#F0CF9B]">
              <dt>{label}</dt><dd><bdi>{money(amount)}</bdi></dd>
            </div>
          ))}
        </dl>
      </section>
      <Link href="/pages/contact" className={textLink}>مساعدة بخصوص الطلب</Link>
    </div>
  );
}

function Profile({ session, customer, onSession }: { session: AccountSession; customer: Customer; onSession: (value: AccountSession) => void }) {
  const [name, setName] = useState(customer.name);
  const [status, setStatus] = useState<string>();
  const [error, setError] = useState<string>();
  const [saving, setSaving] = useState(false);

  async function save(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSaving(true);
    setStatus(undefined);
    setError(undefined);
    try {
      onSession(await accountMutation<AccountSession>('/profile', session, { name }, 'PATCH'));
      setStatus('تم حفظ بياناتك.');
    } catch (cause) {
      setError(cause instanceof AccountError ? cause.message : 'تعذّر حفظ بياناتك.');
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className={panel}>
      <h2 className="mb-5 text-xl font-bold text-[#F5EAD9]">البيانات الشخصية</h2>
      <form onSubmit={save} className="max-w-xl space-y-4">
        <label className="block text-sm font-semibold text-[#D8C7B3]">الاسم
          <input value={name} onChange={event => setName(event.target.value)} autoComplete="name" maxLength={120} required
            className="mt-1.5 block min-h-11 w-full rounded-lg border border-[#4B3A2B] bg-[#120E0B] px-3 text-base text-[#F5EAD9] outline-none focus:border-[#D7AF76]" />
        </label>
        <dl className="grid gap-3 rounded-xl border border-[#382A1F] p-3 text-sm sm:grid-cols-2">
          <div><dt className="text-[#BCA891]">البريد الإلكتروني</dt><dd className="break-words text-[#F5EAD9]"><bdi>{customer.email ?? 'غير مسجّل'}</bdi></dd></div>
          <div><dt className="text-[#BCA891]">رقم الموبايل</dt><dd className="text-[#F5EAD9]"><bdi>{customer.phone ?? 'غير مسجّل'}</bdi></dd></div>
        </dl>
        <button type="submit" disabled={saving || !name.trim() || name.trim() === customer.name} className="gold-button min-h-11 rounded-lg px-5 text-sm font-bold disabled:opacity-60">
          {saving ? 'جارٍ الحفظ…' : 'حفظ البيانات'}
        </button>
        {status && <p role="status" className="text-sm text-[#CEE0BE]">{status}</p>}
        {error && <p role="alert" className="text-sm text-[#F3AD9A]">{error}</p>}
      </form>
      {customer.email && <Link href="/account?password=1" className={'mt-3 text-sm ' + textLink}>تعيين أو تغيير كلمة المرور</Link>}
    </div>
  );
}

export default function AccountDashboard({ session, customer, onSession, onLogout, logoutPending }: {
  session: AccountSession;
  customer: Customer;
  onSession: (value: AccountSession) => void;
  onLogout: () => Promise<void>;
  logoutPending: boolean;
}) {
  const [section, setSection] = useState<Section>('orders');
  const [page, setPage] = useState<Page>();
  const [listError, setListError] = useState(false);
  const [loadingMore, setLoadingMore] = useState(false);
  const [selected, setSelected] = useState<string>();
  const [detail, setDetail] = useState<AccountOrderDetails>();
  const [detailError, setDetailError] = useState(false);
  const [reload, setReload] = useState(0);

  useEffect(() => {
    const controller = new AbortController();
    accountRequest<Page>('/orders', { signal: controller.signal }).then(setPage).catch(() => {
      if (!controller.signal.aborted) setListError(true);
    });
    return () => controller.abort();
  }, [reload]);

  useEffect(() => {
    if (!selected) return;
    const controller = new AbortController();
    accountRequest<{ data: AccountOrderDetails }>('/orders/' + encodeURIComponent(selected), { signal: controller.signal })
      .then(response => { if (!controller.signal.aborted) setDetail(response.data); })
      .catch(() => { if (!controller.signal.aborted) setDetailError(true); });
    return () => controller.abort();
  }, [selected]);

  function showOrder(number: string) {
    if (selected === number) setSelected(undefined);
    else setSelected(number);
    setDetail(undefined);
    setDetailError(false);
  }

  async function loadMore() {
    if (!page?.nextPage || loadingMore) return;
    setLoadingMore(true);
    setListError(false);
    try {
      const more = await accountRequest<Page>('/orders?page=' + page.nextPage);
      setPage({ data: [...page.data, ...more.data], nextPage: more.nextPage });
    } catch {
      setListError(true);
    } finally {
      setLoadingMore(false);
    }
  }

  return (
    <section className="mx-auto max-w-6xl">
      <div className="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
          <h1 className="text-3xl font-bold text-[#F5EAD9] sm:text-4xl">حسابي</h1>
          <p className="mt-2 text-sm text-[#D5C0A6]">أهلًا، <bdi>{customer.name}</bdi></p>
        </div>
        <button type="button" onClick={onLogout} disabled={logoutPending}
          className="min-h-11 rounded-lg border border-[#543E2B] px-4 text-sm text-[#ECC480] hover:border-[#ECC480] disabled:opacity-60">
          {logoutPending ? 'جارٍ تسجيل الخروج…' : 'تسجيل الخروج'}
        </button>
      </div>
      <div className="grid items-start gap-4 md:grid-cols-[13rem_minmax(0,1fr)] lg:gap-6">
        <nav aria-label="أقسام الحساب" className="grid grid-cols-3 gap-1 rounded-xl border border-[#3D2E23] bg-[#16110D] p-1.5 md:grid-cols-1">
          {sections.map(({ key, icon, label }) => (
            <button type="button" key={key} aria-current={section === key ? 'page' : undefined} onClick={() => setSection(key)}
              className="flex min-h-12 items-center justify-center gap-2 rounded-lg px-1 text-sm font-semibold text-[#D7BDA3] hover:bg-[#251A13] aria-[current=page]:bg-[#39291B] aria-[current=page]:text-[#F5D498] md:justify-start md:px-3">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" className="size-4 shrink-0" aria-hidden="true"><path d={icon} /></svg>
              {label}
            </button>
          ))}
        </nav>
        {section === 'profile' ? <Profile session={session} customer={customer} onSession={onSession} /> : (
          <div className={panel}>
            <h2 className="mb-4 text-xl font-bold text-[#F5EAD9]">{section === 'orders' ? 'طلباتي' : 'معاملات الدفع'}</h2>
            {!page && !listError && <p role="status" className="text-sm text-[#D8C7B3]">جارٍ تحميل الطلبات…</p>}
            {listError && (
              <div role="alert" className="text-sm text-[#F3AD9A]">
                تعذّر تحميل الطلبات.
                <button type="button" className={'ms-2 ' + textLink} onClick={() => {
                  if (page) void loadMore();
                  else { setListError(false); setReload(value => value + 1); }
                }}>حاول تاني</button>
              </div>
            )}
            {page?.data.length === 0 && (
              <div className="rounded-xl border border-dashed border-[#57412D] bg-[#1E160F] p-5 text-sm text-[#DBC5A8]">
                <p>{section === 'orders' ? 'مفيش طلبات مرتبطة بحسابك لسه.' : 'مفيش معاملات دفع مرتبطة بحسابك لسه.'}</p>
                <Link href="/#signature" className={textLink}>تسوّق العطور</Link>
              </div>
            )}
            <div className="space-y-3">
              {page?.data.map(order => (
                <article key={order.number} className="overflow-hidden rounded-xl border border-[#4A3828] bg-[#1C150F]">
                  <button type="button" aria-expanded={selected === order.number} onClick={() => showOrder(order.number)}
                    className="grid min-h-20 w-full grid-cols-[minmax(0,1fr)_auto] items-center gap-3 p-4 text-start hover:bg-[#281D14] sm:grid-cols-[1.4fr_1fr_auto]">
                    <span className="min-w-0">
                      <bdi className="block break-words text-sm font-bold text-[#F4E2CC]">{order.number}</bdi>
                      <span className="text-xs text-[#C4AE95]">{date(order.date)} · {section === 'payments' ? methods[order.paymentMethod] ?? 'الدفع' : order.itemCount + ' منتجات'}</span>
                    </span>
                    <Status order={order} payment={section === 'payments'} />
                    <span className="col-span-2 flex flex-wrap items-center justify-between gap-2 text-sm text-[#ECC480] sm:col-span-1 sm:justify-end">
                      <bdi className="font-bold">{money(order.total)}</bdi>
                      <span className="text-xs underline underline-offset-4">{selected === order.number ? 'إخفاء التفاصيل' : 'عرض التفاصيل'}</span>
                    </span>
                  </button>
                  {selected === order.number && (detail?.number === order.number ? <OrderDetails order={detail} />
                    : <p role={detailError ? 'alert' : 'status'} className="border-t border-[#3D2E23] p-4 text-sm text-[#D8C7B3]">
                      {detailError ? 'تعذّر تحميل التفاصيل. اقفل الطلب وافتحه للمحاولة تاني.' : 'جارٍ تحميل التفاصيل…'}
                    </p>)}
                </article>
              ))}
            </div>
            {page?.nextPage && !listError && <button type="button" disabled={loadingMore} onClick={loadMore} className={'mt-3 ' + textLink}>{loadingMore ? 'جارٍ التحميل…' : 'عرض طلبات أقدم'}</button>}
            {section === 'orders' && page && <p className="mt-4 text-sm text-[#BFA990]">طلبت قبل كده كضيف؟ <Link href="/pages/contact" className={textLink}>تواصل معنا برقم الطلب</Link></p>}
          </div>
        )}
      </div>
    </section>
  );
}
