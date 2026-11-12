export type TrustIcon = 'sample' | 'delivery' | 'cod' | 'oils';

export interface MoodItem {
  id: string;
  label: string;
  icon: string;
}

/** Storefront-wide content served by GET /api/v1/site (edited in Filament → إعدادات الموقع). */
export interface SiteSettings {
  name: string;
  tagline: string;
  motto: string;
  seo: {
    title_default: string;
    title_template: string;
    description: string;
    og_description: string;
    twitter_description: string;
    keywords: string[];
    twitter_handle: string;
    organization_description: string;
  };
  footer: {
    description: string;
    links_title: string;
    support_title: string;
    support_text: string;
    support_badge: string;
    copyright: string;
    legal_links: string[];
    trust_features: { icon: TrustIcon; title: string; desc: string }[];
  };
  moods: { all_label: string; items: MoodItem[] };
  commerce: {
    currency: string;
    shipping_cost: number;
    shipping_note: string;
    shipping_free_label: string;
    payment_note: string;
    governorates: string[];
  };
  creed: { eyebrow: string; quote: string; text: string };
  bundle: {
    badge: string;
    cta: string;
    highlights: { title: string; desc: string }[];
  };
  stats: { productCount: number; minPrice: number | null; maxPrice: number | null };
}

