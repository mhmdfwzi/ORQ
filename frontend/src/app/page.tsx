import type { Metadata } from 'next';
import ConceptFourView from '@/components/cinematic/ConceptFourView';
import { home } from '@/data/store-editorial.json';
import { sharingMetadata } from '@/lib/sharing';

export const metadata: Metadata = {
  title: { absolute: home.meta_title },
  description: home.meta_description,
  ...sharingMetadata({
    title: home.og_title,
    description: home.og_description,
    url: '/',
  }),
};

export default function HomePage() {
  return <ConceptFourView />;
}
