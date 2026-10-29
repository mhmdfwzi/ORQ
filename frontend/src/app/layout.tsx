import type { Metadata, Viewport } from 'next';
import { Tajawal, Cinzel } from 'next/font/google';
import './globals.css';
import { CartProvider } from '@/components/cart/CartContext';
import CartDrawer from '@/components/cart/CartDrawer';
import ConceptSwitcherBar from '@/components/layout/ConceptSwitcherBar';
import JsonLd from '@/components/seo/JsonLd';

const tajawal = Tajawal({
  subsets: ['arabic'],
  weight: ['300', '400', '500', '700', '800'],
  variable: '--font-tajawal',
  display: 'swap',
});

const cinzel = Cinzel({
  subsets: ['latin'],
  weight: ['500', '700'],
  variable: '--font-cinzel',
  display: 'swap',
});

export const viewport: Viewport = {
  themeColor: '#070707',
  width: 'device-width',
  initialScale: 1,
  maximumScale: 5,
};

export const metadata: Metadata = {
  metadataBase: new URL('https://orq.mhmdfwzi.com'),
  title: {
    default: 'ORQ Haute Parfumerie | عطور النيش الرجالية الفاخرة',
    template: '%s | ORQ Haute Parfumerie',
  },
  description:
    'دار عطور نيش فاخرة متخصصة في أرقى العطور الرجالية المعتقة بتركيزات Eau de Parfum تدوم لأكثر من 12 ساعة. استكشف تشكيلة 12 عطراً نادراً مع شحن سريع لجميع المحافظات والدفع عند الاستلام وعينة تجربة مجانية.',
  keywords: [
    'عطور رجالية',
    'عطور نيش',
    'عطور فاخرة',
    'ORQ',
    'عطور مصر',
    'عطر رويال عود',
    'عطور ثابتة',
    'عطور فواحة',
    'أفضل عطور رجالية',
    'عطور شتوية',
    'عطور صيفية',
    'عطور رسمية',
    'Haute Parfumerie Egypt',
    'Luxury Men Fragrances Cairo',
  ],
  authors: [{ name: 'ORQ Haute Parfumerie', url: 'https://orq.mhmdfwzi.com' }],
  creator: 'ORQ Haute Parfumerie',
  publisher: 'ORQ Haute Parfumerie',
  applicationName: 'ORQ Parfumerie',
  category: 'ecommerce',
  formatDetection: {
    telephone: false,
    email: false,
    address: false,
  },
  alternates: {
    canonical: 'https://orq.mhmdfwzi.com',
    languages: {
      'ar-EG': 'https://orq.mhmdfwzi.com',
      ar: 'https://orq.mhmdfwzi.com',
    },
  },
  openGraph: {
    title: 'ORQ Haute Parfumerie | عطور النيش الرجالية الفاخرة',
    description:
      'حضورك له أثر لا يمحى من الذاكرة • استكشف 12 عطراً نيش معتقاً بتركيزات تدوم لأكثر من 12 ساعة مع شحن سريع ودفع عند الاستلام وعينة تجربة مجانية.',
    url: 'https://orq.mhmdfwzi.com',
    siteName: 'ORQ Haute Parfumerie',
    locale: 'ar_EG',
    type: 'website',
    images: [
      {
        url: '/og-image.jpg',
        width: 1200,
        height: 630,
        alt: 'ORQ Haute Parfumerie Luxury Fragrances',
      },
    ],
  },
  twitter: {
    card: 'summary_large_image',
    title: 'ORQ Haute Parfumerie | عطور النيش الرجالية الفاخرة',
    description:
      'حضورك له أثر لا يمحى من الذاكرة • عطور رجالية بتركيز نيش مكثف وشحن سريع لكافة المحافظات.',
    images: ['/og-image.jpg'],
    creator: '@ORQperfumes',
  },
  icons: {
    icon: [
      { url: '/icon.svg', type: 'image/svg+xml' },
      { url: '/icon.png', sizes: '64x64', type: 'image/png' },
    ],
    shortcut: '/icon.svg',
    apple: [
      { url: '/apple-icon.png', sizes: '180x180', type: 'image/png' },
    ],
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

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="ar" dir="rtl" className={`${tajawal.variable} ${cinzel.variable}`}>
      <head>
        <JsonLd />
      </head>
      <body className="bg-[#0B0B0B] text-[#F2EBDD] min-h-screen selection:bg-[#C4A265] selection:text-black">
        <CartProvider>
          {/* Top Sticky Bar to switch seamlessly between Design Concepts */}
          <ConceptSwitcherBar />

          <main className="min-h-screen">
            {children}
          </main>

          {/* Interactive Slide-over Cart Drawer */}
          <CartDrawer />
        </CartProvider>
      </body>
    </html>
  );
}
