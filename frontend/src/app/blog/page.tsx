import Image from 'next/image';
import Link from 'next/link';
import type { Metadata } from 'next';
import StorePageLayout from '@/components/layout/StorePageLayout';
import articles from '@/data/blog-articles.json';
import { sharingMetadata } from '@/lib/sharing';

const title = 'مدونة ORQ';
const description = 'دليلك لاختيار العطر الرجالي ودمج العطور واستخدامها في مختلف المناسبات.';

export const metadata: Metadata = {
  title: { absolute: title },
  description,
  alternates: { canonical: '/blog' },
  ...sharingMetadata({ title, description, url: '/blog' }),
};

export default function BlogPage() {
  return (
    <StorePageLayout title="مدونة ORQ" intro="أدلة لاختيار عطرك ودمجه واستخدامه حسب الموسم والمناسبة.">
      <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        {articles.map((article) => (
          <Link key={article.slug} href={'/blog/' + article.slug} className="group overflow-hidden rounded-xl border border-[#493321] bg-[#0D0B09] transition hover:border-[#D7AF76]">
            <Image src={article.image} alt={article.imageAlt} width={540} height={405} sizes="(min-width: 1024px) 330px, (min-width: 640px) 50vw, 100vw" className="aspect-[4/3] w-full object-cover" />
            <div className="space-y-2 p-4">
              <h2 className="font-bold leading-7 text-[#F2EBDD] group-hover:text-[#ECC480]">{article.title}</h2>
              <p className="text-sm leading-6 text-[#D2C3AF]">{article.excerpt}</p>
            </div>
          </Link>
        ))}
      </div>
    </StorePageLayout>
  );
}
