'use client';

import { useEffect } from 'react';
import { usePathname, useSearchParams } from 'next/navigation';
import { trackStorefrontStage } from '@/lib/attribution';

export default function StorefrontTracking() {
  const pathname = usePathname();
  const searchParams = useSearchParams();

  useEffect(() => {
    if (pathname !== '/payment/result') trackStorefrontStage('visit');
  }, [pathname, searchParams]);

  return null;
}
