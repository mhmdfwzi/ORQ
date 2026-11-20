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
  whatsapp_order_confirmation_available?: boolean;
  online_payment_available?: boolean;
  contact?: {
    contact_phone?: string;
    contact_phone_2?: string;
    whatsapp_number?: string;
    contact_email?: string;
    contact_email_2?: string;
    contact_address?: string;
    working_hours?: string;
  };
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

