import articles from '@/data/blog-articles.json';
import { collections } from '@/data/store-editorial.json';
import { getProductEditorial } from '@/lib/product-editorial';
import { SITE_URL } from '@/lib/site-url';
import type { Product } from '@/types/product';
import type { SiteSettings } from '@/types/site';

type DataNode = Record<string, unknown>;
type Crumb = { name: string; href: string };
type Faq = { q: string; a: string };

const absolute = (path: string) => new URL(path, SITE_URL).href;
const organization = absolute('/#organization');
const home = { name: 'الرئيسية', href: '/' };
const graph = (...nodes: DataNode[]) => ({ '@context': 'https://schema.org', '@graph': nodes });

export function breadcrumbs(...crumbs: Crumb[]) {
  return {
    '@type': 'BreadcrumbList',
    itemListElement: [home, ...crumbs].map(({ name, href }, index) => ({
      '@type': 'ListItem', position: index + 1, name, item: absolute(href),
    })),
  };
}

function faqPage(items: Faq[]) {
  return {
    '@type': 'FAQPage',
    mainEntity: items.map(({ q, a }) => ({
      '@type': 'Question', name: q, acceptedAnswer: { '@type': 'Answer', text: a },
    })),
  };
}

export function faqGraph(items: Faq[]) {
  return graph(faqPage(items));
}

export function siteGraph(site: SiteSettings) {
  return graph({
    '@type': 'Organization',
    '@id': organization,
    name: site.name,
    url: absolute('/'),
    logo: absolute('/icon.png'),
    slogan: 'Let Ego Own Space',
    description: site.seo.organization_description,
    areaServed: { '@type': 'Country', name: 'Egypt' },
  }, {
    '@type': 'WebSite',
    '@id': absolute('/#website'),
    url: absolute('/'),
    name: site.name,
    inLanguage: 'ar-EG',
    publisher: { '@id': organization },
  });
}

export function productGraph(product: Product) {
  const editorial = getProductEditorial(product.slug);
  const collection = collections.find((item) => item.slug === editorial?.collection);
  const variant = product.variants.find((item) => item.size === '50ml');
  const href = `/products/${product.slug}`;
  const url = absolute(href);
  const properties = [
    ['Concentration', 'Eau de Parfum'],
    ['Fragrance Family', product.scentType],
    ['Top Notes', product.notes.top.join('، ')],
    ['Heart Notes', product.notes.heart.join('، ')],
    ['Base Notes', product.notes.base.join('، ')],
  ];

  return graph({
    '@type': 'Product',
    '@id': `${url}#product`,
    name: `عطر ${product.name}`,
    url,
    sku: product.sku || editorial?.sku,
    description: editorial?.meta_description ?? product.description,
    image: product.image ? [absolute(product.image)] : undefined,
    brand: { '@type': 'Brand', name: 'ORQ' },
    category: 'عطور رجالية',
    size: '50 ml',
    color: editorial?.bottle_color_en,
    additionalProperty: properties.filter(([, value]) => value).map(([name, value]) => ({
      '@type': 'PropertyValue', name, value,
    })),
    offers: variant && {
      '@type': 'Offer',
      url,
      price: variant.price.toFixed(2),
      priceCurrency: 'EGP',
      availability: `https://schema.org/${variant.inStock ? 'InStock' : 'OutOfStock'}`,
      itemCondition: 'https://schema.org/NewCondition',
      seller: { '@id': organization },
      acceptedPaymentMethod: { '@type': 'PaymentMethod', name: 'Cash on Delivery' },
      hasMerchantReturnPolicy: {
        '@type': 'MerchantReturnPolicy',
        applicableCountry: 'EG',
        returnPolicyCategory: 'https://schema.org/MerchantReturnFiniteReturnWindow',
        merchantReturnDays: 14,
        merchantReturnLink: absolute('/pages/return-exchange-policy'),
      },
    },
  }, breadcrumbs(
    ...(collection ? [{ name: collection.name, href: `/collections/${collection.slug}` }] : []),
    { name: product.name, href },
  ));
}

export function collectionGraph(collection: (typeof collections)[number]) {
  return graph(
    breadcrumbs({ name: collection.name, href: `/collections/${collection.slug}` }),
    faqPage(collection.faq),
  );
}

function articleDateTime(value: string): string {
  // The editorial handoff uses 10:00 Cairo time for date-only publication records.
  const timestamp = /^\d{4}-\d{2}-\d{2}$/.test(value) ? `${value}T10:00:00+02:00` : value;
  return new Date(timestamp).toISOString();
}

export function articleGraph(article: (typeof articles)[number] & { dateModified?: string }) {
  const href = `/blog/${article.slug}`;
  const url = absolute(href);
  return graph({
    '@type': 'BlogPosting',
    '@id': `${url}#article`,
    mainEntityOfPage: { '@type': 'WebPage', '@id': url },
    headline: article.title,
    description: article.description,
    inLanguage: 'ar-EG',
    datePublished: articleDateTime(article.datePublished),
    dateModified: articleDateTime(article.dateModified ?? article.datePublished),
    image: [absolute(article.image)],
    author: { '@type': 'Organization', name: 'فريق تحرير ORQ', url: absolute('/blog') },
    publisher: { '@id': organization },
  }, breadcrumbs({ name: 'المدونة', href: '/blog' }, { name: article.title, href }), faqPage(article.faq));
}
