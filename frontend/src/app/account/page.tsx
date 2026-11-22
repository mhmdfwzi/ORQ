import type { Metadata } from 'next';
import { Suspense } from 'react';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import AccountPanel from '@/components/account/AccountPanel';

export const metadata: Metadata = {
  title: 'حسابي',
  robots: { index: false, follow: true },
  referrer: 'no-referrer',
};

export default function AccountPage() {
  return (
    <>
      <Header />
      <div className="min-h-[65vh] bg-[radial-gradient(ellipse_at_top_right,#291c13,#100d0a_48%,#0b0908)] px-4 py-8 sm:px-6 sm:py-14 lg:px-8">
        <Suspense fallback={<p role="status" className="mx-auto max-w-5xl text-sm text-[#D2C3AF]">جارٍ تحميل حسابك…</p>}>
          <AccountPanel />
        </Suspense>
      </div>
      <Footer />
    </>
  );
}
