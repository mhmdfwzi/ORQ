'use client';

import { useEffect, useState } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';

type AccountSession = {
  googleAvailable: boolean;
  customer: { name: string; email: string } | null;
  csrfToken: string | null;
};

const accountApi = '/api/v1/account';

function googleMessage(status: string | null): string | undefined {
  switch (status) {
    case 'cancelled': return 'تم إلغاء تسجيل الدخول. تقدر تبدأ من جديد في أي وقت.';
    case 'unavailable': return 'تسجيل الدخول بجوجل غير متاح حاليًا.';
    case 'existing': return 'البريد ده مرتبط بحساب موجود. تواصل مع خدمة العملاء للمساعدة.';
    case 'failed': return 'تعذّر تسجيل الدخول بجوجل. جرّب مرة تانية.';
  }
}

export default function AccountPanel() {
  const search = useSearchParams();
  const [session, setSession] = useState<AccountSession | null>(null);
  const [loadError, setLoadError] = useState(false);
  const [logoutPending, setLogoutPending] = useState(false);
  const [logoutError, setLogoutError] = useState(false);
  const message = googleMessage(search.get('google'));

  useEffect(() => {
    const controller = new AbortController();

    async function load() {
      try {
        const response = await fetch(`${accountApi}/session`, { cache: 'no-store', signal: controller.signal });
        if (!response.ok) throw new Error('Account service unavailable');
        setSession(await response.json());
      } catch {
        if (!controller.signal.aborted) setLoadError(true);
      }
    }

    void load();
    return () => controller.abort();
  }, []);

  async function logout() {
    if (!session?.csrfToken || logoutPending) return;
    setLogoutPending(true);
    setLogoutError(false);
    try {
      const response = await fetch(`${accountApi}/logout`, {
        method: 'POST',
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': session.csrfToken },
      });
      if (!response.ok) throw new Error('Could not sign out');
      setSession({ ...session, customer: null, csrfToken: null });
    } catch {
      setLogoutError(true);
    } finally {
      setLogoutPending(false);
    }
  }

  if (loadError) {
    return <p role="alert" className="text-sm text-[#F1B69B]">تعذّر تحميل الحساب. حدّث الصفحة وحاول مرة تانية.</p>;
  }
  if (!session) {
    return <p role="status" className="text-sm text-[#D2C3AF]">جارٍ تحميل حسابك…</p>;
  }

  return (
    <section className="mx-auto max-w-md space-y-4 py-3 sm:py-6">
      {session.customer ? (
        <>
          <h2 className="text-2xl font-bold text-[#F4E7D5]">أهلًا، <bdi>{session.customer.name}</bdi></h2>
          <p className="break-words text-sm text-[#D2C3AF]"><bdi>{session.customer.email}</bdi></p>
          <button type="button" onClick={logout} disabled={logoutPending} className="min-h-11 rounded-lg border border-[#675035] px-4 text-sm font-semibold text-[#ECC480] hover:border-[#ECC480] disabled:opacity-60">
            {logoutPending ? 'جارٍ تسجيل الخروج…' : 'تسجيل الخروج'}
          </button>
          {logoutError && <p role="alert" className="text-sm text-[#F1B69B]">تعذّر تسجيل الخروج. حدّث الصفحة وحاول مرة تانية.</p>}
        </>
      ) : (
        <>
          <h2 className="text-2xl font-bold text-[#F4E7D5]">تسجيل الدخول</h2>
          {session.googleAvailable && (
            <a href={`${accountApi}/google`} className="inline-flex min-h-11 items-center gap-2.5 rounded-md border border-[#747775] bg-white px-3 text-sm leading-5 font-semibold text-[#1F1F1F] hover:bg-[#F4F6FC] focus-visible:outline-2 focus-visible:outline-offset-3 focus-visible:outline-[#ECC480]">
              <Image src="/google-g.png" alt="" width={18} height={18} className="size-[18px] object-contain" />
              <span>متابعة باستخدام <bdi>Google</bdi></span>
            </a>
          )}
          {(message || !session.googleAvailable) && (
            <p role="status" className="text-sm leading-7 text-[#E6C4A8]">
              {message ?? googleMessage('unavailable')}{' '}
              {search.get('google') === 'existing' && <Link href="/pages/contact" className="text-[#ECC480] underline">تواصل معنا</Link>}
            </p>
          )}
        </>
      )}
    </section>
  );
}
