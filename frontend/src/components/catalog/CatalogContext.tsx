'use client';

import React, { createContext, useCallback, useContext, useMemo } from 'react';
import type { Product } from '@/types/product';

interface CatalogContextType {
  products: Product[];
  giftBundle: Product;
  getProductsByMood: (mood: string) => Product[];
}

const CatalogContext = createContext<CatalogContextType | undefined>(undefined);

/** Receives the catalogue fetched on the server (Laravel API) and shares it with client components. */
export function CatalogProvider({
  products,
  giftBundle,
  children,
}: {
  products: Product[];
  giftBundle: Product;
  children: React.ReactNode;
}) {
  const getProductsByMood = useCallback(
    (mood: string) => (mood === 'all' ? products : products.filter((p) => p.mood === mood)),
    [products]
  );

  const value = useMemo(
    () => ({ products, giftBundle, getProductsByMood }),
    [products, giftBundle, getProductsByMood]
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

