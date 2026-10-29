import type { Metadata } from 'next';
import { Tajawal, Cinzel } from 'next/font/google';
import './globals.css';
import { CartProvider } from '@/components/cart/CartContext';
import CartDrawer from '@/components/cart/CartDrawer';
import ConceptSwitcherBar from '@/components/layout/ConceptSwitcherBar';

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

export const metadata: Metadata = {
  title: 'ORQ Haute Parfumerie | عطور رجالية فاخرة',
  description: 'المتجر الرسمي لعطور ORQ الرجالية الفاخرة - حضورك له أثر لا يمحى من الذاكرة.',
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="ar" dir="rtl" className={`${tajawal.variable} ${cinzel.variable}`}>
      <body className="bg-[#0B0B0B] text-[#F2EBDD] min-h-screen selection:bg-[#C4A265] selection:text-black">
        <CartProvider>
          {/* Top Sticky Bar to switch seamlessly between the 3 Design Concepts */}
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
