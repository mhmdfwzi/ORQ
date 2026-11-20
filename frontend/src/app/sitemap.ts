import { MetadataRoute } from 'next';
import { getCatalog } from '@/lib/catalog';
import { SITE_URL } from '@/lib/site-url';
import { storePages } from '@/lib/store-pages';
import articles from '@/data/blog-articles.json';
import { collections } from '@/data/store-editorial.json';

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const { products } = await getCatalog();

  const productRoutes = products.map((p) => ({
    url: new URL('/products/' + (p.pageSlug ?? p.slug), SITE_URL).href,
    changeFrequency: 'weekly' as const,
    priority: 0.8,
  }));

  return [
    {
      url: SITE_URL,
      changeFrequency: 'daily',
      priority: 1.0,
    },
    ...productRoutes,
    ...['/blog', ...articles.map(({ slug }) => '/blog/' + slug), ...collections.map(({ slug }) => '/collections/' + slug)].map((path) => ({
      url: new URL(path, SITE_URL).href,
      changeFrequency: 'monthly' as const,
      priority: 0.6,
    })),
    ...Object.keys(storePages).map((slug) => ({
      url: new URL('/pages/' + slug, SITE_URL).href,
      changeFrequency: 'monthly' as const,
      priority: 0.5,
    })),
  ];
}

