import type { Metadata } from 'next';
import { Suspense } from 'react';
import Link from 'next/link';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import ProductDetailClient from '@/components/catalog/ProductDetailClient';
import { getProductBySlug } from '@/lib/api';
import { getCatalog } from '@/lib/catalog';
import { getProductEditorial } from '@/lib/product-editorial';
import JsonLd from '@/components/seo/JsonLd';
import { productGraph } from '@/lib/structured-data';
import { sharingMetadata } from '@/lib/sharing';

type ProductPageProps = { params: Promise<{ slug: string }> };

export async function generateStaticParams() {
  const { products } = await getCatalog();
  return products.map((product) => ({
    slug: product.pageSlug ?? product.slug,
  }));
}

export async function generateMetadata({ params }: ProductPageProps): Promise<Metadata> {
  const { slug } = await params;
  const product = await getProductBySlug(slug);
  if (!product) return { robots: { index: false } };

  const path = '/products/' + (product.pageSlug ?? product.slug);
  const editorial = getProductEditorial(product.slug);
  return {
    title: editorial ? { absolute: editorial.meta_title } : product.name,
    description: editorial?.meta_description ?? product.description,
    alternates: { canonical: path, languages: { 'ar-EG': path, ar: path } },
    ...sharingMetadata({
      title: editorial?.meta_title ?? product.name,
      description: editorial?.meta_description ?? product.description,
      url: path,
      image: product.image,
    }),
  };
}

export default function ProductPage({ params }: ProductPageProps) {
  return (
    <div className="min-h-screen bg-[#0B0B0B] text-[#F2EBDD]">
      <Header />
      <Suspense fallback={<p role="status" className="px-4 py-16 text-center text-sm text-[#B9B1A5]">جارٍ تحميل العطر…</p>}>
        <ProductContent params={params} />
      </Suspense>
      <Footer />
    </div>
  );
}

async function ProductContent({ params }: ProductPageProps) {
  const { slug } = await params;
  const product = await getProductBySlug(slug);

  if (!product) {
    return (
      <div className="mx-auto max-w-xl space-y-5 px-4 py-24 text-center">
        <h1 className="text-2xl font-bold text-white">العطر غير موجود</h1>
        <Link href="/#signature" className="gold-button inline-block rounded-lg px-5 py-3 text-sm">
          العودة للمتجر
        </Link>
      </div>
    );
  }

  return (
    <>
      <JsonLd data={productGraph(product)} />
      <ProductDetailClient key={product.slug} product={product} editorial={getProductEditorial(product.slug)} />
    </>
  );
}
