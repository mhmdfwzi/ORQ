import React from 'react';
import { getCatalog } from '@/lib/catalog';
import { SITE_SHARE_IMAGE, SITE_URL as baseUrl } from '@/lib/site-url';

export default async function JsonLd() {
  const { products: PRODUCTS, site } = await getCatalog();
  const fullName = `${site.name} ${site.tagline}`;
  const { minPrice, maxPrice } = site.stats;

  const schemaData = {
    '@context': 'https://schema.org',
    '@graph': [
      {
        '@type': 'Organization',
        '@id': `${baseUrl}/#organization`,
        name: fullName,
        alternateName: [site.name],
        slogan: 'Original · Rare · Quality',
        url: baseUrl,
        logo: {
          '@type': 'ImageObject',
          url: `${baseUrl}/icon.png`,
          width: 512,
          height: 512,
        },
        description: site.seo.organization_description,
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
        name: fullName,
        description: site.seo.description,
        publisher: {
          '@id': `${baseUrl}/#organization`,
        },
        inLanguage: 'ar-EG',
      },
      {
        '@type': 'Store',
        '@id': `${baseUrl}/#store`,
        name: `${fullName} Store`,
        url: baseUrl,
        image: new URL(SITE_SHARE_IMAGE, baseUrl).href,
        priceRange: minPrice !== null && maxPrice !== null ? `${minPrice} - ${maxPrice} EGP` : undefined,
        currenciesAccepted: 'EGP',
        paymentAccepted: 'Cash on Delivery, Credit Card',
        areaServed: {
          '@type': 'Country',
          name: 'Egypt',
        },
        hasOfferCatalog: {
          '@type': 'OfferCatalog',
          name: `${site.name} Signature Perfume Collection`,
          itemListElement: PRODUCTS.map((product, index) => ({
            '@type': 'Offer',
            itemOffered: {
              '@type': 'Product',
              name: `${product.name} (${product.arabicName})`,
              description: product.description,
              image: product.image ? new URL(product.image, baseUrl).href : undefined,
              sku: product.slug,
              brand: {
                '@type': 'Brand',
                name: site.name,
              },
              offers: {
                '@type': 'Offer',
                price: product.variants[product.variants.length - 1]?.price,
                priceCurrency: 'EGP',
                availability: 'https://schema.org/InStock',
                url: new URL('/products/' + (product.pageSlug ?? product.slug), baseUrl).href,
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

