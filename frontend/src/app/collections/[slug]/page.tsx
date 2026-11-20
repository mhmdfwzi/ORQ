import type { Metadata } from 'next';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import ProductCard from '@/components/catalog/ProductCard';
import StorePageLayout from '@/components/layout/StorePageLayout';
import { getProducts } from '@/lib/api';
import { collections } from '@/data/store-editorial.json';

type Props = { params: Promise<{ slug: string }> };

export function generateStaticParams() {
  return collections.map(({ slug }) => ({ slug }));
}

async function getCollection(params: Props['params']) {
  const { slug } = await params;
  const collection = collections.find((item) => item.slug === slug);
  if (!collection) notFound();
  return collection;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const collection = await getCollection(params);
  const url = '/collections/' + collection.slug;
  return {
    title: { absolute: collection.meta_title },
    description: collection.meta_description,
    alternates: { canonical: url, languages: { ar: url, 'ar-EG': url } },
    openGraph: { title: collection.h1, description: collection.meta_description, url },
  };
}

export default async function CollectionPage({ params }: Props) {
  const [collection, products] = await Promise.all([getCollection(params), getProducts()]);
  const bySlug = new Map(products.map((product) => [product.pageSlug ?? product.slug, product]));

  return (
    <StorePageLayout title={collection.h1} intro={collection.intro}>
      <section className="space-y-4">
        <h2 className="text-xl font-bold text-[#ECC480]">{collection.name}</h2>
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5">
          {collection.products.map((slug) => {
            const product = bySlug.get(slug);
            return product ? <ProductCard key={slug} product={product} showScentType /> : null;
          })}
        </div>
      </section>
      <div className="mt-8 grid gap-5 border-t border-[#342820] pt-6 text-base leading-8 text-[#D2C3AF]">
        {collection.guide.map(({ h2, text }) => (
          <section key={h2}><h2 className="mb-1 text-lg font-bold text-[#ECC480]">{h2}</h2><p>{text}</p></section>
        ))}
        <section>
          <h2 className="mb-3 text-lg font-bold text-[#ECC480]">أسئلة عن المجموعة</h2>
          <dl className="space-y-3">
            {collection.faq.map(({ q, a }) => <div key={q}><dt className="font-bold text-[#F2EBDD]">{q}</dt><dd>{a}</dd></div>)}
          </dl>
        </section>
        <nav aria-label="مجموعات ORQ الأخرى" className="flex flex-wrap gap-x-5">
          {collections.filter((item) => item.slug !== collection.slug).map((item) => <Link key={item.slug} href={'/collections/' + item.slug} className="inline-flex min-h-11 items-center font-semibold text-[#ECC480] hover:underline">{item.name}</Link>)}
        </nav>
      </div>
    </StorePageLayout>
  );
}
