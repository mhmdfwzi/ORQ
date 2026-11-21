import type { Metadata } from 'next';
import { Suspense } from 'react';
import StorePageLayout from '@/components/layout/StorePageLayout';
import AccountPanel from '@/components/account/AccountPanel';

export const metadata: Metadata = {
  title: 'حسابي',
  robots: { index: false, follow: true },
};

export default function AccountPage() {
  return (
    <StorePageLayout title="حسابي">
      <Suspense fallback={<p role="status" className="text-sm text-[#D2C3AF]">جارٍ تحميل حسابك…</p>}>
        <AccountPanel />
      </Suspense>
    </StorePageLayout>
  );
}
