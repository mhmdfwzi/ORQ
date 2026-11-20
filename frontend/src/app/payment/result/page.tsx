import { Suspense } from 'react';
import type { Metadata } from 'next';
import StorePageLayout from '@/components/layout/StorePageLayout';
import PaymentResult from './PaymentResult';

export const metadata: Metadata = {
  title: 'حالة الدفع',
  robots: { index: false, follow: false },
  referrer: 'no-referrer',
};

export default function PaymentResultPage() {
  return (
    <StorePageLayout title="حالة الدفع">
      <Suspense fallback={<p role="status">جارٍ التحقق من الدفع…</p>}>
        <PaymentResult />
      </Suspense>
    </StorePageLayout>
  );
}
