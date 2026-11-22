'use client';

import { useEffect, useState } from 'react';
import { useSearchParams } from 'next/navigation';
import AccountAccess from './AccountAccess';
import AccountDashboard from './AccountDashboard';
import { accountMutation, accountRequest, type AccountSession } from '@/lib/account';

export default function AccountPanel() {
  const query = useSearchParams();
  const [session, setSession] = useState<AccountSession>();
  const [attempt, setAttempt] = useState(0);
  const [loadError, setLoadError] = useState(false);
  const [logoutPending, setLogoutPending] = useState(false);
  const [logoutError, setLogoutError] = useState(false);

  useEffect(() => {
    const controller = new AbortController();
    accountRequest<AccountSession>('/session', { signal: controller.signal }).then(value => {
      if (!controller.signal.aborted) setSession(value);
    }).catch(() => {
      if (!controller.signal.aborted) setLoadError(true);
    });
    return () => controller.abort();
  }, [attempt]);

  async function logout() {
    if (!session || logoutPending) return;
    setLogoutPending(true);
    setLogoutError(false);
    try {
      await accountMutation('/logout', session);
      setSession(undefined);
      setAttempt(value => value + 1);
    } catch {
      setLogoutError(true);
    } finally {
      setLogoutPending(false);
    }
  }

  if (loadError) {
    return <div role="alert" className="mx-auto max-w-5xl text-sm text-[#F1B69B]">
      تعذّر تحميل الحساب.
      <button type="button" onClick={() => { setLoadError(false); setAttempt(value => value + 1); }} className="ms-2 min-h-11 font-semibold text-[#ECC480] underline">حاول تاني</button>
    </div>;
  }
  if (!session) return <p role="status" className="mx-auto max-w-5xl text-sm text-[#D2C3AF]">جارٍ تحميل حسابك…</p>;

  const reset = query.get('reset');
  const email = query.get('email');
  const managePassword = query.get('password') === '1' && !!session.customer?.email;
  if (session.customer && !(reset && email) && !managePassword) {
    return <>
      {logoutError && <p role="alert" className="mx-auto mb-3 max-w-6xl text-sm text-[#F1B69B]">تعذّر تسجيل الخروج. جرّب تاني.</p>}
      <AccountDashboard session={session} customer={session.customer} onSession={setSession} onLogout={logout} logoutPending={logoutPending} />
    </>;
  }

  return <AccountAccess key={managePassword ? 'password' : 'access'} session={session} onSession={setSession}
    google={query.get('google')} resetToken={reset} resetEmail={email} managePassword={managePassword} />;
}
