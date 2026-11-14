import { MetadataRoute } from 'next';
import { getCatalog } from '@/lib/catalog';

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const baseUrl = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://orq.mhmdfwzi.com';
  const { products } = await getCatalog();

  const productRoutes = products.map((p) => ({
    url: new URL(`/products/${p.slug}`, baseUrl).href,
    changeFrequency: 'weekly' as const,
    priority: 0.8,
  }));

  return [
    {
      url: baseUrl,
      changeFrequency: 'daily',
      priority: 1.0,
    },
    ...productRoutes,
  ];
}

