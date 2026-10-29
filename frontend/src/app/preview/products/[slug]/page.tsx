import React from 'react';
import Link from 'next/link';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import ProductDetailClient from '@/components/catalog/ProductDetailClient';
import { getProductBySlug, PRODUCTS } from '@/data/products';

export function generateStaticParams() {
  return PRODUCTS.map((product) => ({
    slug: product.slug,
  }));
}

export default async function ProductPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const product = getProductBySlug(slug);

  if (!product) {
    return (
      <div className="bg-[#0B0B0B] text-[#F2EBDD] min-h-screen">
        <Header />
        <div className="max-w-xl mx-auto px-4 py-24 text-center space-y-4">
          <div className="text-4xl">🔍</div>
          <h1 className="text-2xl font-bold text-white">العطر غير موجود</h1>
          <p className="text-xs text-[#A0988C]">
            لم نتمكن من العثور على العطر المطلوب. تفضل بزيارة المجموعة الكاملة.
          </p>
          <div className="pt-4">
            <Link href="/preview" className="gold-button px-5 py-2.5 rounded-lg text-xs font-bold inline-block">
              العودة للمتجر
            </Link>
          </div>
        </div>
        <Footer />
      </div>
    );
  }

  return (
    <div className="bg-[#0B0B0B] text-[#F2EBDD] min-h-screen">
      <Header />
      <ProductDetailClient product={product} />
      <Footer />
    </div>
  );
}
