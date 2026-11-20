import type { ReactNode } from 'react';
import Link from 'next/link';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';

export default function StorePageLayout({ title, intro, sidebar, children }: {
  title: string;
  intro?: string;
  sidebar?: ReactNode;
  children: ReactNode;
}) {
  return (
    <div id="top">
      <Header />
      <div className="border-b border-[#493321] bg-[radial-gradient(ellipse_at_top_right,#352218,#15100d_59%,#0b0908)]">
        <div className="mx-auto max-w-6xl px-5 pt-5 pb-10 sm:px-8 sm:pb-14 sm:pt-8">
          <Link href="/" className="inline-flex min-h-11 items-center text-sm text-[#D7AF76] hover:underline">الرئيسية</Link>
          <h1 className="mt-3 text-3xl font-bold leading-tight text-[#F4E7D5] sm:text-5xl">{title}</h1>
          {intro && <p className="mt-4 max-w-2xl text-base leading-8 text-[#D2C3AF] sm:text-lg">{intro}</p>}
        </div>
      </div>
      <div className={'mx-auto grid min-h-[42vh] max-w-6xl items-start gap-8 px-5 py-8 sm:px-8 sm:py-12' + (sidebar ? ' lg:grid-cols-[minmax(0,1fr)_16rem]' : '')}>
        <div className="min-w-0 rounded-2xl border border-[#342820] bg-[#14110f] p-5 sm:p-8">
          {children}
        </div>
        {sidebar}
      </div>
      <Footer />
    </div>
  );
}
