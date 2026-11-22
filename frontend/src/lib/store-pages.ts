import editorialPages from '@/data/store-pages-editorial.json';

type StorePage = {
  label: string;
  heading: string;
  metaTitle: string;
  description: string;
  intro?: string;
  body?: string[];
  faq?: { q: string; a: string }[];
};

const paymentPolicy: StorePage = {
  label: 'طرق الدفع',
  heading: 'طرق الدفع',
  metaTitle: 'طرق الدفع | ORQ',
  description: 'كيفية الدفع عند الاستلام أو بالبطاقة عبر Paymob ومتابعة حالة الدفع في متجر ORQ.',
  intro: 'تظهر طرق الدفع المتاحة وإجمالي الطلب بالجنيه المصري في السلة قبل إتمام الطلب.',
  body: [
    '## الدفع عند الاستلام\n\nادفع نقداً عند استلام طلبك. احتفظ برقم الطلب للتواصل مع فريق ORQ عند الحاجة.',
    '## الدفع الإلكتروني بالبطاقة\n\nعند ظهور خيار الدفع الإلكتروني، اختره وأدخل بريد الفوترة. ستنتقل إلى صفحة Paymob لإدخال بيانات البطاقة، ثم تعود إلى المتجر لمتابعة حالة الدفع.',
    '## متابعة العملية\n\nإذا ظهر أن العملية قيد التحقق، انتظر تحديث الحالة. إذا خُصم المبلغ وحالة الطلب ما زالت قيد التحقق، تواصل معنا برقم الطلب قبل بدء عملية جديدة.',
  ],
};

export const storePages: Record<string, StorePage> = {
  ...Object.fromEntries(editorialPages.map((page) => [page.slug, {
    label: page.title,
    heading: page.h1,
    metaTitle: page.meta_title,
    description: page.meta_description,
    intro: page.intro,
    body: page.body,
    faq: page.faq,
  }])),
  'payment-policy': paymentPolicy,
};

export const storePageGroups = [
  { title: 'تعرّف على ORQ', slugs: ['our-story', 'how-to-choose', 'quality-guarantee'] },
  { title: 'خدمة العملاء', slugs: ['contact', 'faq', 'shipping-policy', 'payment-policy', 'return-exchange-policy'] },
  { title: 'سياسات الموقع', slugs: ['privacy-policy', 'terms'] },
];

export function getStorePage(slug: string): StorePage | undefined {
  return Object.hasOwn(storePages, slug) ? storePages[slug] : undefined;
}
