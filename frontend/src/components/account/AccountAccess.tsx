'use client';

import { useState, type FormEvent } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { accountApi, accountMutation, AccountError, type AccountSession } from '@/lib/account';

type Mode = 'login' | 'register' | 'forgot' | 'reset';
const titles: Record<Mode, string> = {
  login: 'تسجيل الدخول',
  register: 'إنشاء حساب',
  forgot: 'استعادة كلمة المرور',
  reset: 'تعيين كلمة مرور جديدة',
};
const endpoints: Record<Mode, string> = { login: '/login', register: '/register', forgot: '/forgot-password', reset: '/reset-password' };
const inputStyle = 'mt-1.5 min-h-12 w-full rounded-lg border border-[#4B3A2B] bg-[#120E0B] px-3 text-base text-[#F5EAD9] outline-none focus:border-[#D7AF76]';

function Field({ name, label, type = 'text', autoComplete, optional, value, readOnly }: {
  name: string;
  label: string;
  type?: string;
  autoComplete?: string;
  optional?: boolean;
  value?: string;
  readOnly?: boolean;
}) {
  return (
    <label className="block text-sm font-semibold text-[#E4D4BE]">
      {label}
      <input name={name} type={type} dir={name === 'name' ? 'auto' : 'ltr'} autoComplete={autoComplete}
        className={inputStyle} required={!optional} defaultValue={value} readOnly={readOnly}
        minLength={autoComplete === 'new-password' ? 8 : undefined} maxLength={255}
        inputMode={type === 'tel' ? 'tel' : undefined} />
    </label>
  );
}

function googleMessage(status: string | null) {
  if (status === 'cancelled') return 'تم إلغاء تسجيل الدخول بجوجل.';
  if (status === 'unavailable') return 'الدخول بجوجل غير متاح حاليًا.';
  if (status === 'existing') return 'البريد مرتبط بحساب موجود. سجّل بكلمة المرور أو استعدها من هنا.';
  if (status === 'failed') return 'تعذّر تسجيل الدخول بجوجل. جرّب تاني.';
}

export default function AccountAccess({ session, google, resetToken, resetEmail, managePassword, onSession }: {
  session: AccountSession;
  google: string | null;
  resetToken: string | null;
  resetEmail: string | null;
  managePassword: boolean;
  onSession: (value: AccountSession) => void;
}) {
  const [mode, setMode] = useState<Mode>(resetToken && resetEmail ? 'reset' : managePassword ? 'forgot' : 'login');
  const [method, setMethod] = useState<'email' | 'phone'>('email');
  const [pending, setPending] = useState(false);
  const [notice, setNotice] = useState(googleMessage(google));
  const [error, setError] = useState<string>();

  function switchMode(next: Mode) {
    setError(undefined);
    setNotice(undefined);
    setMode(next);
  }

  async function submit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    if (pending) return;
    const data = Object.fromEntries(new FormData(event.currentTarget));
    setPending(true);
    setNotice(undefined);
    setError(undefined);
    try {
      if (mode === 'login' || mode === 'register') {
        onSession(await accountMutation<AccountSession>(endpoints[mode], session, { ...data, method }));
      } else if (mode === 'forgot') {
        await accountMutation(endpoints.forgot, session, data);
        setNotice('لو الإيميل مرتبط بحساب، هيوصلك رابط تعيين كلمة المرور.');
      } else {
        await accountMutation(endpoints.reset, session, { ...data, token: resetToken });
        window.history.replaceState(null, '', '/account');
        setMode('login');
        setNotice('تم تعيين كلمة المرور. تقدر تدخل دلوقتي.');
      }
    } catch (cause) {
      const details = cause instanceof AccountError ? Object.values(cause.fields).flat()[0] : undefined;
      setError(details && /[\u0600-\u06ff]/.test(details) ? details
        : cause instanceof AccountError ? cause.message : 'تعذّر الاتصال. جرّب تاني.');
    } finally {
      setPending(false);
    }
  }

  return (
    <section aria-labelledby="account-access-title" className="mx-auto grid max-w-5xl overflow-hidden rounded-2xl border border-[#503A28] bg-[#17110D] shadow-[0_18px_70px_#0007] md:grid-cols-2">
      <div className="px-6 py-8 sm:p-10 lg:p-12">
        <h1 id="account-access-title" className="mb-6 text-3xl font-bold text-[#F5EAD9] sm:text-4xl">{titles[mode]}</h1>
        {mode === 'login' && (
          <div role="group" aria-label="طريقة تسجيل الدخول" className="mb-5 grid grid-cols-2 gap-1 rounded-lg border border-[#443224] bg-[#100D0A] p-1">
            {(['email', 'phone'] as const).map(option => (
              <button type="button" key={option} aria-pressed={method === option} disabled={pending}
                onClick={() => { setMethod(option); setError(undefined); }}
                className="min-h-10 rounded-md px-1 text-sm font-semibold text-[#D4B99B] aria-pressed:bg-[#382919] aria-pressed:text-[#FFE0A9]">
                {option === 'email' ? 'البريد الإلكتروني' : 'رقم الموبايل'}
              </button>
            ))}
          </div>
        )}
        <form key={mode} onSubmit={submit} className="space-y-4">
          {mode === 'login' ? (
            <Field key={method} name="identity" label={method === 'email' ? 'البريد الإلكتروني' : 'رقم الموبايل'}
              type={method === 'email' ? 'email' : 'tel'} autoComplete="username" />
          ) : (
            <>
              {mode === 'register' && <Field name="name" label="الاسم" autoComplete="name" />}
              <Field name="email" label="البريد الإلكتروني" type="email" autoComplete="email"
                value={mode === 'reset' ? resetEmail ?? undefined : managePassword ? session.customer?.email ?? undefined : undefined}
                readOnly={mode === 'reset' || managePassword} />
              {mode === 'register' && <Field name="phone" label="رقم الموبايل (اختياري)" type="tel" autoComplete="tel" optional />}
            </>
          )}
          {mode !== 'forgot' && <Field name="password" label="كلمة المرور" type="password" autoComplete={mode === 'login' ? 'current-password' : 'new-password'} />}
          {(mode === 'register' || mode === 'reset') && <Field name="password_confirmation" label="تأكيد كلمة المرور" type="password" autoComplete="new-password" />}
          {mode === 'login' && <button type="button" disabled={pending} onClick={() => switchMode('forgot')} className="text-sm font-semibold text-[#ECC480] underline underline-offset-4">نسيت كلمة المرور؟</button>}
          {notice && <p role="status" className="text-sm leading-6 text-[#D9C8B0]">{notice}</p>}
          {error && <p role="alert" className="text-sm leading-6 text-[#F3AD9A]">{error}</p>}
          <button type="submit" disabled={pending} className="gold-button min-h-12 w-full rounded-lg px-5 text-sm font-bold disabled:opacity-60">
            {pending ? 'جارٍ التنفيذ…' : mode === 'forgot' ? 'أرسل رابط الاستعادة' : titles[mode]}
          </button>
        </form>
        {mode === 'login' && session.googleAvailable && (
          <div className="mt-5 border-t border-[#443224] pt-5">
            <a href={accountApi + '/google'} className="flex min-h-11 w-full items-center justify-center gap-2 rounded-lg border border-[#747775] bg-white px-4 text-sm font-semibold text-[#1F1F1F] hover:bg-[#F4F6FC]">
              <Image src="/google-g.png" alt="" width={18} height={18} />
              <span>متابعة باستخدام <bdi>Google</bdi></span>
            </a>
          </div>
        )}
        {session.customer ? (
          <Link href="/account" className="mt-4 flex min-h-11 w-full items-center justify-center text-sm font-semibold text-[#ECC480] underline underline-offset-4">الرجوع لحسابي</Link>
        ) : (
          <button type="button" disabled={pending} onClick={() => switchMode(mode === 'login' ? 'register' : 'login')}
            className="mt-4 flex min-h-11 w-full items-center justify-center text-sm font-semibold text-[#ECC480] underline underline-offset-4">
            {mode === 'login' ? 'إنشاء حساب جديد' : 'الرجوع لتسجيل الدخول'}
          </button>
        )}
      </div>
      <div className="relative hidden min-h-full overflow-hidden bg-[#1A120C] md:block">
        <Image src="/media/orq/catalog/chaos-2.webp" alt="" fill unoptimized className="object-cover object-center" />
        <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/15" aria-hidden="true" />
      </div>
    </section>
  );
}
