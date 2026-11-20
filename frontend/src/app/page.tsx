import type { Metadata } from 'next';
import ConceptFourView from '@/components/cinematic/ConceptFourView';
import { home } from '@/data/store-editorial.json';
import { SITE_SHARE_IMAGE } from '@/lib/site-url';

export const metadata: Metadata = {
  title: { absolute: home.meta_title },
  description: home.meta_description,
  openGraph: {
    title: home.og_title,
    description: home.og_description,
    url: '/',
    images: [SITE_SHARE_IMAGE],
  },
};

export default function HomePage() {
  return <ConceptFourView />;
}
