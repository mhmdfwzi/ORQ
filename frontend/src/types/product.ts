export type ScentMood = 'all' | 'royal_oud' | 'night_allure' | 'sporty_fresh' | 'warm_formal';

export interface PerfumeNotes {
  top: string[];
  heart: string[];
  base: string[];
}

export interface ProductVariant {
  size: '50ml' | '100ml';
  price: number; // in EGP
  inStock: boolean;
}

export interface Product {
  slug: string;
  name: string;
  arabicName: string;
  subtitle: string;
  originalInspiration: string;
  category: string;
  brandFeel: string;
  scentType: string;
  description: string;
  mood: ScentMood;
  notes: PerfumeNotes;
  variants: ProductVariant[];
  image: string;
  isBestSeller?: boolean;
  isNew?: boolean;
}

export interface CartItem {
  slug: string;
  name: string;
  size: '50ml' | '100ml';
  price: number;
  quantity: number;
  image: string;
}

