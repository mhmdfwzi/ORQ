import type { Metadata } from 'next';
import { Suspense } from 'react';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import ContactForm from '@/components/layout/ContactForm';
import StorePageLayout from '@/components/layout/StorePageLayout';
import { getSiteSettings } from '@/lib/api';
import { getStorePage, storePageGroups, storePages } from '@/lib/store-pages';
import { sharingMetadata } from '@/lib/sharing';

type Props = { params: Promise<{ slug: string }> };

export function generateStaticParams() {
  return Object.keys(storePages).map((slug) => ({ slug }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const content = getStorePage(slug);
  if (!content) notFound();

  const url = '/pages/' + slug;
  return {
    title: content.heading,
    description: content.description,
    alternates: { canonical: url, languages: { 'ar-EG': url, ar: url } },
    ...sharingMetadata({ title: content.heading, description: content.description, url }),
  };
}

function StorePageLinks({ current }: { current: string }) {
  return (
    <nav aria-label="صفحات المتجر" className="space-y-5 rounded-2xl border border-[#342820] bg-[#14110f] p-4 lg:sticky lg:top-24">
      {storePageGroups.map(({ title, slugs }) => (
        <div key={title}>
          <h2 className="mb-1 px-2 text-sm font-bold text-[#D7AF76]">{title}</h2>
          {slugs.map((slug) => (
            <Link
              key={slug}
              href={'/pages/' + slug}
              aria-current={current === slug ? 'page' : undefined}
              className="flex min-h-11 items-center rounded-lg px-2 text-sm text-[#E2D3BC] hover:bg-[#2A1E16] aria-[current=page]:bg-[#2A1E16] aria-[current=page]:font-bold aria-[current=page]:text-[#ECC480]"
            >
              {storePages[slug].label}
            </Link>
          ))}
        </div>
      ))}
    </nav>
  );
}

async function ContactDetails() {
  const contact = (await getSiteSettings())?.contact;
  const phones = [contact?.contact_phone, contact?.contact_phone_2].filter((phone): phone is string => !!phone?.trim());
  const emails = [contact?.contact_email, contact?.contact_email_2].filter((email): email is string => !!email?.trim());
  const whatsapp = contact?.whatsapp_number?.replace(/\D/g, '').replace(/^00/, '').replace(/^01/, '201');

  return (
    <div className="space-y-6">
      {Boolean(phones.length || emails.length || whatsapp || contact?.contact_address || contact?.working_hours) && (
        <div className="space-y-3 border-b border-[#342820] pb-5 text-[#D2C3AF]">
          {phones.map((phone) => (
            <a key={phone} dir="ltr" href={'tel:' + phone.replace(/[^\d+]/g, '')} className="block w-fit py-1 text-[#ECC480] hover:underline">{phone}</a>
          ))}
          {emails.map((email) => (
            <a key={email} dir="ltr" href={'mailto:' + email.trim()} className="block w-fit break-all py-1 text-[#ECC480] hover:underline">{email}</a>
          ))}
          {whatsapp && <a href={'https://wa.me/' + whatsapp} target="_blank" rel="noreferrer" className="inline-flex min-h-11 items-center text-[#ECC480] hover:underline">راسلنا على واتساب</a>}
          {contact?.contact_address && <p className="whitespace-pre-line">{contact.contact_address}</p>}
          {contact?.working_hours && <p className="whitespace-pre-line">{contact.working_hours}</p>}
        </div>
      )}
      <ContactForm />
    </div>
  );
}

export default function StorePage({ params }: Props) {
  return (
    <Suspense fallback={
      <StorePageLayout title="صفحات المتجر" loading>
        <p role="status" className="text-sm text-[#D2C3AF]">جارٍ تحميل الصفحة…</p>
      </StorePageLayout>
    }>
      <StorePageContent params={params} />
    </Suspense>
  );
}

async function StorePageContent({ params }: Props) {
  const { slug } = await params;
  const content = getStorePage(slug);
  if (!content) notFound();

  return (
    <StorePageLayout title={content.heading} intro={content.intro} sidebar={<StorePageLinks current={slug} />}>
      {slug === 'contact' ? <ContactDetails /> : (
        <div className="divide-y divide-[#342820] text-base leading-8 text-[#D2C3AF]">
          {content.sections?.map(({ heading, paragraphs, points }) => (
            <section key={heading} className="space-y-2 py-5 first:pt-0 last:pb-0">
              <h2 className="text-xl font-bold text-[#ECC480]">{heading}</h2>
              {points && <ul className="list-disc space-y-1 ps-6">{points.map((point) => <li key={point}>{point}</li>)}</ul>}
              {paragraphs?.map((text) => <p key={text}>{text}</p>)}
            </section>
          ))}
        </div>
      )}
      {['our-story', 'how-to-choose'].includes(slug) && (
        <Link href="/#signature" className="gold-button mt-7 inline-flex min-h-11 items-center rounded-lg px-5 text-sm">تسوّق العطور</Link>
      )}
      {slug === 'how-to-choose' && (
        <Link href="/blog/perfume-layering-guide" className="ms-4 mt-4 inline-flex min-h-11 items-center font-semibold text-[#ECC480] underline underline-offset-4">اقرأ دليل دمج العطور</Link>
      )}
      {['shipping-policy', 'return-exchange-policy', 'privacy-policy', 'payment-policy', 'terms'].includes(slug) && (
        <Link href="/pages/contact" className="mt-6 inline-flex min-h-11 items-center font-semibold text-[#ECC480] underline underline-offset-4">تواصل معنا</Link>
      )}
    </StorePageLayout>
  );
}
