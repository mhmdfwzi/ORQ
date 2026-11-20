import { MetadataRoute } from 'next';
import { SITE_URL } from '@/lib/site-url';

export default function robots(): MetadataRoute.Robots {
  return {
    rules: {
      userAgent: '*',
      allow: '/',
      disallow: ['/api/', '/admin', '/livewire/'],
    },
    sitemap: new URL('/sitemap.xml', SITE_URL).href,
  };
}

