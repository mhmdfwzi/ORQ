import type { Metadata } from 'next';
import Image from 'next/image';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import Markdown from 'react-markdown';
import remarkGfm from 'remark-gfm';
import StorePageLayout from '@/components/layout/StorePageLayout';
import articles from '@/data/blog-articles.json';

type Props = { params: Promise<{ slug: string }> };

export function generateStaticParams() {
  return articles.map(({ slug }) => ({ slug }));
}

async function getArticle(params: Props['params']) {
  const { slug } = await params;
  const article = articles.find((item) => item.slug === slug);
  if (!article) notFound();
  return article;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const article = await getArticle(params);
  const url = '/blog/' + article.slug;
  return {
    title: { absolute: article.metaTitle },
    description: article.description,
    alternates: { canonical: url, languages: { ar: url, 'ar-EG': url } },
    openGraph: { title: article.title, description: article.description, url, type: 'article', images: [article.image] },
  };
}

export default async function ArticlePage({ params }: Props) {
  const article = await getArticle(params);
  return (
    <StorePageLayout title={article.title} intro={article.excerpt}>
      <article className="mx-auto max-w-3xl">
        <Link href="/blog" className="mb-4 inline-flex min-h-11 items-center text-sm font-bold text-[#ECC480] hover:underline">كل المقالات</Link>
        <Image src={article.image} alt={article.imageAlt} width={768} height={520} sizes="(min-width: 800px) 768px, 100vw" className="mx-auto mb-8 max-h-[32rem] w-full rounded-xl border border-[#493321] bg-[#0C0A08] object-contain" />
        <div className="space-y-4 text-base leading-8 text-[#D2C3AF] [&_h2]:pt-5 [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:text-[#ECC480] [&_h3]:pt-3 [&_h3]:text-lg [&_h3]:font-bold [&_h3]:text-[#F2EBDD] [&_ol]:list-decimal [&_ul]:list-disc [&_ol]:ps-6 [&_ul]:ps-6 [&_li]:pb-1 [&_strong]:text-[#F2EBDD] [&_th]:bg-[#241A14] [&_th]:text-[#ECC480] [&_td]:border [&_th]:border [&_td]:border-[#493321] [&_th]:border-[#493321] [&_td]:p-3 [&_th]:p-3 [&_th]:text-start">
          <Markdown
            remarkPlugins={[remarkGfm]}
            skipHtml
            components={{
              a: ({ href, children }) => href?.startsWith('/') && !href.startsWith('//')
                ? <Link className="text-[#ECC480] underline underline-offset-4" href={href}>{children}</Link>
                : <a className="text-[#ECC480] underline underline-offset-4" href={href}>{children}</a>,
              table: ({ children }) => <div className="overflow-x-auto"><table className="w-full border-collapse text-sm">{children}</table></div>,
            }}
          >
            {article.body}
          </Markdown>
        </div>
      </article>
    </StorePageLayout>
  );
}
