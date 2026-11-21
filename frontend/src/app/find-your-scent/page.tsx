import type { Metadata } from 'next';
import Link from 'next/link';
import StorePageLayout from '@/components/layout/StorePageLayout';
import { ScentQuiz } from '@/components/quiz/ScentQuiz';
import JsonLd from '@/components/seo/JsonLd';
import quiz from '@/data/scent-quiz.json';
import { getProducts } from '@/lib/api';
import { getProductEditorial } from '@/lib/product-editorial';
import { breadcrumbs } from '@/lib/structured-data';
import { sharingMetadata } from '@/lib/sharing';

const path = '/find-your-scent';
const title = 'اختار شفرتك: اعرف عطر ORQ المناسب لك';
const description = 'اختار شفرتك في 4 أسئلة عن المناسبة والريحة والموسم وقوة العطر، وخد ترشيحك مع اختيارين بدلاء.';

export const metadata: Metadata = {
  title,
  description,
  alternates: { canonical: path },
  ...sharingMetadata({ title, description, url: path }),
};

export default async function FindYourScentPage() {
  const products = (await getProducts()).flatMap((product) => {
    const editorial = getProductEditorial(product.slug);
    if (!editorial) return [];

    return [{
      slug: editorial.slug,
      name: product.name,
      tagline: product.subtitle,
      notes: product.notes,
      when: editorial.when,
      image: product.image,
      imageAlt: editorial.image_alt,
    }];
  });

  return (
    <StorePageLayout title={title} intro={quiz.intro}>
      <JsonLd data={{ '@context': 'https://schema.org', ...breadcrumbs({ name: quiz.title, href: path }) }} />
      <div className="mx-auto max-w-3xl">
        <ScentQuiz quiz={quiz} products={products} />
        <div className="mt-8 flex flex-wrap gap-x-6 border-t border-[#342820] pt-3 text-sm text-[#ECC480]">
          <Link className="inline-flex min-h-11 items-center underline" href="/pages/how-to-choose">دليل اختيار العطر</Link>
          <Link className="inline-flex min-h-11 items-center underline" href="/blog/perfume-layering-guide">دليل دمج العطور</Link>
        </div>
      </div>
    </StorePageLayout>
  );
}
