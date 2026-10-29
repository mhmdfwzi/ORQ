import { MetadataRoute } from 'next';
import { PRODUCTS } from '@/data/products';

export default function sitemap(): MetadataRoute.Sitemap {
  const baseUrl = 'https://orq.mhmdfwzi.com';

  const productRoutes = PRODUCTS.map((p) => ({
    url: `${baseUrl}/preview/products/${p.slug}`,
    lastModified: new Date('2026-10-29'),
    changeFrequency: 'weekly' as const,
    priority: 0.8,
  }));

  return [
    {
      url: baseUrl,
      lastModified: new Date('2026-10-29'),
      changeFrequency: 'daily',
      priority: 1.0,
    },
    {
      url: `${baseUrl}/preview/concept-4`,
      lastModified: new Date('2026-10-29'),
      changeFrequency: 'daily',
      priority: 0.95,
    },
    {
      url: `${baseUrl}/preview/concept-5`,
      lastModified: new Date('2026-10-29'),
      changeFrequency: 'daily',
      priority: 0.95,
    },
    {
      url: `${baseUrl}/preview/concept-1`,
      lastModified: new Date('2026-10-29'),
      changeFrequency: 'weekly',
      priority: 0.85,
    },
    {
      url: `${baseUrl}/preview/concept-2`,
      lastModified: new Date('2026-10-29'),
      changeFrequency: 'weekly',
      priority: 0.85,
    },
    {
      url: `${baseUrl}/preview/concept-3`,
      lastModified: new Date('2026-10-29'),
      changeFrequency: 'weekly',
      priority: 0.85,
    },
    ...productRoutes,
  ];
}
