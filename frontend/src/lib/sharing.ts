import type { Metadata } from 'next';
import { SITE_SHARE_IMAGE } from '@/lib/site-url';

export function sharingMetadata({ title, description, url, image, publishedTime }: {
  title: string;
  description: string;
  url: string;
  image?: string | null;
  publishedTime?: string;
}): Pick<Metadata, 'openGraph' | 'twitter'> {
  const shared = { title, description, images: [image || SITE_SHARE_IMAGE] };

  return {
    openGraph: {
      ...shared,
      url,
      siteName: 'ORQ',
      locale: 'ar_EG',
      ...(publishedTime ? { type: 'article', publishedTime } : { type: 'website' }),
    },
    twitter: { ...shared, card: 'summary_large_image' },
  };
}
