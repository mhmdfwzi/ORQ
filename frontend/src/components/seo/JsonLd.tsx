import React from 'react';
import { PRODUCTS } from '@/data/products';

export default function JsonLd() {
  const baseUrl = 'https://orq.mhmdfwzi.com';

  const schemaData = {
    '@context': 'https://schema.org',
    '@graph': [
      {
        '@type': 'Organization',
        '@id': `${baseUrl}/#organization`,
        name: 'ORQ Haute Parfumerie',
        alternateName: ['ORQ', 'دار عطور أو آر كيو'],
        url: baseUrl,
        logo: {
          '@type': 'ImageObject',
          url: `${baseUrl}/icon.svg`,
          width: 512,
          height: 512,
        },
        description:
          'دار عطور نيش فاخرة متخصصة في أرقى العطور الرجالية المعتقة بتركيزات مكثفة تدوم لأكثر من 12 ساعة.',
        address: {
          '@type': 'PostalAddress',
          addressLocality: 'Cairo',
          addressRegion: 'Cairo Governorate',
          addressCountry: 'EG',
        },
        contactPoint: {
          '@type': 'ContactPoint',
          contactType: 'customer service',
          areaServed: 'EG',
          availableLanguage: ['Arabic', 'English'],
        },
      },
      {
        '@type': 'WebSite',
        '@id': `${baseUrl}/#website`,
        url: baseUrl,
        name: 'ORQ Haute Parfumerie',
        description: 'المتجر الرسمي لعطور ORQ الرجالية الفاخرة',
        publisher: {
          '@id': `${baseUrl}/#organization`,
        },
        inLanguage: 'ar-EG',
      },
      {
        '@type': 'Store',
        '@id': `${baseUrl}/#store`,
        name: 'ORQ Haute Parfumerie Store',
        url: baseUrl,
        image: `${baseUrl}/og-image.jpg`,
        priceRange: '750 - 1290 EGP',
        currenciesAccepted: 'EGP',
        paymentAccepted: 'Cash on Delivery, Credit Card',
        areaServed: {
          '@type': 'Country',
          name: 'Egypt',
        },
        hasOfferCatalog: {
          '@type': 'OfferCatalog',
          name: 'ORQ Signature Perfume Collection',
          itemListElement: PRODUCTS.map((product, index) => ({
            '@type': 'Offer',
            itemOffered: {
              '@type': 'Product',
              name: `${product.name} (${product.arabicName})`,
              description: product.description,
              image: `${baseUrl}${product.image}`,
              sku: product.slug,
              brand: {
                '@type': 'Brand',
                name: 'ORQ',
              },
              offers: {
                '@type': 'Offer',
                price: product.variants[1]?.price || 1290,
                priceCurrency: 'EGP',
                availability: 'https://schema.org/InStock',
                url: `${baseUrl}/preview/products/${product.slug}`,
              },
            },
            position: index + 1,
          })),
        },
      },
    ],
  };

  return (
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(schemaData) }}
    />
  );
}
