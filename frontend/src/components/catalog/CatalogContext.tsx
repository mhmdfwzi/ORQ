'use client';

import React, { createContext, useCallback, useContext, useMemo } from 'react';
import type { Product } from '@/types/product';
import type { BrandStory } from '@/types/brand';
import type { SiteSettings } from '@/types/site';

interface CatalogContextType {
  products: Product[];
  giftBundle: Product;
  brand: BrandStory;
  site: SiteSettings;
  getProductsByMood: (mood: string) => Product[];
}

const CatalogContext = createContext<CatalogContextType | undefined>(undefined);

/** Receives the catalogue fetched on the server (Laravel API) and shares it with client components. */
export function CatalogProvider({
  products,
  giftBundle,
  brand,
  site,
  children,
}: {
  products: Product[];
  giftBundle: Product;
  brand: BrandStory;
  site: SiteSettings;
  children: React.ReactNode;
}) {
  const getProductsByMood = useCallback(
    (mood: string) => (mood === 'all' ? products : products.filter((p) => p.mood === mood)),
    [products]
  );

  const value = useMemo(
    () => ({ products, giftBundle, brand, site, getProductsByMood }),
    [products, giftBundle, brand, site, getProductsByMood]
  );

  return <CatalogContext.Provider value={value}>{children}</CatalogContext.Provider>;
}

export function useCatalog() {
  const context = useContext(CatalogContext);
  if (!context) {
    throw new Error('useCatalog must be used within a CatalogProvider');
  }
  return context;
}

