import type { Metadata, Viewport } from 'next';
import { Cairo, Cormorant_Garamond } from 'next/font/google';
import './globals.css';
import { CartProvider } from '@/components/cart/CartContext';
import CartDrawer from '@/components/cart/CartDrawer';
import JsonLd from '@/components/seo/JsonLd';
import { CatalogProvider } from '@/components/catalog/CatalogContext';
import { getCatalog } from '@/lib/catalog';

const cairo = Cairo({
  subsets: ['arabic', 'latin'],
  weight: ['300', '400', '500', '600', '700', '800'],
  variable: '--font-cairo',
  display: 'swap',
});

const cormorant = Cormorant_Garamond({
  subsets: ['latin'],
  weight: ['500', '600', '700'],
  variable: '--font-display',
  display: 'swap',
});

export const viewport: Viewport = {
  themeColor: '#0A0908',
  width: 'device-width',
  initialScale: 1,
  maximumScale: 5,
};

// Public site URL is deployment config (not business data).
const SITE_URL = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://orq.mhmdfwzi.com';

/** All SEO text comes from the database (Filament → إعدادات الموقع). */
export async function generateMetadata(): Promise<Metadata> {
  const { site } = await getCatalog();
  const { seo } = site;

  return {
    metadataBase: new URL(SITE_URL),
    title: { default: seo.title_default, template: seo.title_template },
    description: seo.description,
    keywords: seo.keywords,
    authors: [{ name: site.name, url: SITE_URL }],
    creator: site.name,
    publisher: site.name,
    applicationName: site.name,
    category: 'ecommerce',
    formatDetection: { telephone: false, email: false, address: false },
    alternates: {
      canonical: SITE_URL,
      languages: { 'ar-EG': SITE_URL, ar: SITE_URL },
    },
    openGraph: {
      title: seo.title_default,
      description: seo.og_description,
      url: SITE_URL,
      siteName: site.name,
      locale: 'ar_EG',
      type: 'website',
      images: [{ url: '/og-image.jpg', width: 1200, height: 630, alt: `${site.name} ${site.tagline}` }],
    },
    twitter: {
      card: 'summary_large_image',
      title: seo.title_default,
      description: seo.twitter_description,
      images: ['/og-image.jpg'],
      creator: seo.twitter_handle,
    },
    icons: {
      icon: [
        { url: '/icon.svg', type: 'image/svg+xml' },
        { url: '/icon.png', sizes: '512x512', type: 'image/png' },
      ],
      shortcut: '/icon.svg',
      apple: [{ url: '/apple-icon.png', sizes: '180x180', type: 'image/png' }],
    },
    robots: {
      index: true,
      follow: true,
      googleBot: {
        index: true,
        follow: true,
        'max-video-preview': -1,
        'max-image-preview': 'large',
        'max-snippet': -1,
      },
    },
    other: {
      'geo.region': 'EG',
      'geo.placename': 'Cairo, Egypt',
      'geo.position': '30.0444;31.2357',
      ICBM: '30.0444, 31.2357',
    },
  };
}

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const { products, giftBundle, brand, site } = await getCatalog();

  return (
    <html lang="ar" dir="rtl" className={`${cairo.variable} ${cormorant.variable}`}>
      <head>
        <JsonLd />
      </head>
      <body className="bg-[#0B0B0B] text-[#F2EBDD] min-h-screen selection:bg-[#C4A265] selection:text-black">
        <CatalogProvider products={products} giftBundle={giftBundle} brand={brand} site={site}>
          <CartProvider>
            <main className="min-h-screen">
              {children}
            </main>

            {/* Interactive Slide-over Cart Drawer */}
            <CartDrawer />
          </CartProvider>
        </CatalogProvider>
      </body>
    </html>
  );
}
