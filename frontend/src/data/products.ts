import { Product } from '@/types/product';

export const PRODUCTS: Product[] = [
  {
    slug: 'royal-oud',
    name: 'ROYAL OUD',
    arabicName: 'متمرد',
    subtitle: 'عود عصري متمرد على التقاليد',
    originalInspiration: 'توليفة خشبية حرة مع العود المصفى',
    category: 'Oud / Luxury',
    brandFeel: 'Rebellious royalty',
    scentType: 'عود • خشبي • متبل',
    description: 'عود استثنائي نقي بنكهة عصرية راقية غير ثقيلة. تمتزج فيه حمضيات منعشة مع فلفل أسود وخشب الأرز لتبرز نغمة العود الملكي المصفى بأعلى درجات النقاء والجاذبية.',
    mood: 'royal_oud',
    isBestSeller: true,
    notes: {
      top: ['الليمون الصقلي', 'البرغموت الإيطالي', 'التوت الوردي'],
      heart: ['خشب الأرز الأطلسي', 'الفلفل الأسود الحار', 'الأنجيليكا'],
      base: ['العود النادر الفاخر', 'خشب الصندل', 'المسك الدخاني']
    },
    variants: [
      { size: '50ml', price: 1190, inStock: true },
      { size: '100ml', price: 1999, inStock: true }
    ],
    image: '/media/orq/royal_oud_photoreal.jpg'
  },
  {
    slug: 'chaos',
    name: 'CHAOS',
    arabicName: 'كايوس',
    subtitle: 'حضور بري وهيمنة مغناطيسية',
    originalInspiration: 'توليفة برية من البرغموت والأمبروكسان',
    category: 'Power / Signature',
    brandFeel: 'Wild dominance',
    scentType: 'منعش • متبل • خشبي',
    description: 'عطر يفرض هيبته بلا استئذان. يبدأ بانفجار حمضي منعش من البرغموت، يتسلل بعده فلفل أسود حاد ولافندر أرستقراطي، ليستقر على سيطرة ساحرة من الأمبروكسان النقي وخشب الأرز.',
    mood: 'sporty_fresh',
    isBestSeller: true,
    notes: {
      top: ['البرغموت الإيطالي', 'الفلفل السيشواني'],
      heart: ['اللافندر الخزامى', 'الفلفل الوردي', 'نجيل الهند'],
      base: ['الأمبروكسان النقي', 'خشب الأرز', 'اللابدانوم']
    },
    variants: [
      { size: '50ml', price: 790, inStock: true },
      { size: '100ml', price: 1290, inStock: true }
    ],
    image: '/media/orq/334ae943-c027-4d78-bba3-94c02df1ffdd.jpg'
  },
  {
    slug: 'alpha',
    name: 'ALPHA',
    arabicName: 'ألفا',
    subtitle: 'طاقة النصر ومغناطيس الإطراءات',
    originalInspiration: 'توليفة بحرية مفعمة بالحيوية والظفر',
    category: 'Power / Signature',
    brandFeel: 'Winner / leader',
    scentType: 'منعش • حلو • مائي',
    description: 'رمز الرجل الفائز المفعم بالطاقة والثقة. توليفة رياضية جذابة تجمع بين انتعاش النوتات البحرية والجريب فروت، مع لمسة ورق الغار وقاعدة دافئة من عنبر الحوت وخشب الغاياك.',
    mood: 'sporty_fresh',
    isBestSeller: true,
    notes: {
      top: ['الجريب فروت المضيء', 'نوتات المحيط المنعشة'],
      heart: ['أوراق الغار العطرية', 'الياسمين المائي'],
      base: ['عنبر الحوت الفاخر', 'خشب الغاياك', 'طحلب السنديان']
    },
    variants: [
      { size: '50ml', price: 790, inStock: true },
      { size: '100ml', price: 1290, inStock: true }
    ],
    image: '/media/orq/11206483-815d-4610-a05d-b5d5fc2397db.jpg'
  },
  {
    slug: 'dom',
    name: 'DOM',
    arabicName: 'دوم',
    subtitle: 'عطر السهرات والجاذبية الصريحة',
    originalInspiration: 'توليفة ليلية جذابة بالباشن فروت والتونكا',
    category: 'Social / Loud',
    brandFeel: 'Social dominance',
    scentType: 'حلو • عطري • مسائي',
    description: 'صُمم ليخطف الأضواء في الليالي والمناسبات الخاصة. مزيج مغرٍ من فاكهة الباشن فروت والروم الفاخر، يعززه عبير الفانيليا الدافئة وحبوب التونكا التي تترك أثراً لا يقاوم.',
    mood: 'night_allure',
    notes: {
      top: ['باشن فروت الاستوائي', 'الزنجبيل المنعش'],
      heart: ['الروم الكاريبي الفاخر', 'التوابل المسائية'],
      base: ['حبوب التونكا', 'الفانيليا المدغشقرية', 'الجلد الأسود']
    },
    variants: [
      { size: '50ml', price: 790, inStock: true },
      { size: '100ml', price: 1290, inStock: true }
    ],
    image: '/media/orq/334ae943-c027-4d78-bba3-94c02df1ffdd.jpg'
  },
  {
    slug: 'pompous',
    name: 'POMPOUS',
    arabicName: 'بومبوس',
    subtitle: 'فخامة صاخبة وأثر عطري هائل',
    originalInspiration: 'توليفة فاكهية مخملية بالمسك النقي',
    category: 'Attention / Loud',
    brandFeel: 'Flashy luxury',
    scentType: 'فاكهي • مسكي • حلو',
    description: 'انفجار من الفواكه الاستوائية الناضجة المغموسة في المسك الأبيض المخملي والفانيليا الغنية. عطر ذو فوحان جبار يسبق خطوتك ويلفت كل الأنظار نحوك.',
    mood: 'night_allure',
    notes: {
      top: ['برتقال صقلية', 'ليمون كالابريا', 'برغموت'],
      heart: ['سلة فواكه البحر المتوسط الفاخرة'],
      base: ['المسك الأبيض النقي', 'الفانيليا', 'العنبر الذهبي']
    },
    variants: [
      { size: '50ml', price: 820, inStock: true },
      { size: '100ml', price: 1350, inStock: true }
    ],
    image: '/media/orq/ac8eac34-874a-49dc-8ecf-dd73aa4d4d22.jpg'
  },
  {
    slug: 'electro',
    name: 'ELECTRO',
    arabicName: 'إلكترو',
    subtitle: 'طاقة حركية وحضور مشحون بالنقاء',
    originalInspiration: 'توليفة رياضية حمضية متألقة',
    category: 'Energy / Active',
    brandFeel: 'Charged presence',
    scentType: 'حمضي • كريمي • رياضي',
    description: 'نقاء وحيوية لا مثيل لها. يبدأ بنفحات المندرين والبرتقال المنعش، ثم يتحول تدريجياً إلى قاعدة كريمية ساحرة من حبوب التونكا والمسك والفانيليا الراقية.',
    mood: 'sporty_fresh',
    notes: {
      top: ['البرتقال الإيطالي', 'المندرين', 'نسيم البحر'],
      heart: ['زهر البرتقال (النيرولي)', 'الفلفل الأسود'],
      base: ['حبوب التونكا الكريمية', 'المسك الأبيض', 'الفانيليا', 'خشب الأرز']
    },
    variants: [
      { size: '50ml', price: 820, inStock: true },
      { size: '100ml', price: 1350, inStock: true }
    ],
    image: '/media/orq/334ae943-c027-4d78-bba3-94c02df1ffdd.jpg'
  },
  {
    slug: 'gigantic',
    name: 'GIGANTIC',
    arabicName: 'جيجانتيك',
    subtitle: 'دفء مغرٍ وحضور آسر للقلوب',
    originalInspiration: 'توليفة كستناء مدخنة وفانيليا آسرة',
    category: 'Intense / Addictive',
    brandFeel: 'Overwhelming presence',
    scentType: 'دافئ • حلو • فانيليا',
    description: 'دفء آسر ومشاعر عميقة. النوتة النجمية للكستناء المحمصة المتبلة بالهيل والفلفل الوردي مع جرعة مكثفة من الفانيليا والعنبر تجعله العطر الأقوى للمواعيد الرومانسية.',
    mood: 'warm_formal',
    isBestSeller: true,
    notes: {
      top: ['حب الهال الهندي', 'الفلفل الوردي', 'أوراق البنفسج'],
      heart: ['الكستناء المحمصة الفاخرة', 'المريمية'],
      base: ['الفانيليا المدخنة', 'العنبر الدافئ', 'خشب الأرز']
    },
    variants: [
      { size: '50ml', price: 890, inStock: true },
      { size: '100ml', price: 1490, inStock: true }
    ],
    image: '/media/orq/royal_oud_photoreal.jpg'
  },
  {
    slug: 'darknight',
    name: 'DARKNIGHT',
    arabicName: 'دارك نايت',
    subtitle: 'الهيبة الصامتة والعمق الأرستقراطي',
    originalInspiration: 'توليفة خشبية بخورية كلاسيكية غامضة',
    category: 'Fresh / Deep',
    brandFeel: 'Silent dominance',
    scentType: 'خشبي • عطري • منعش',
    description: 'توازن عبقري بين نضارة الحمضيات الصباحية وعمق البخور وخشب الصندل الليلي. عطر التوقيع الدائم للرجل الأنيق والواثق في كافة الاجتماعات والمناسبات.',
    mood: 'warm_formal',
    isBestSeller: true,
    notes: {
      top: ['الليمون المنعش', 'الجريب فروت', 'النعناع الأخضر'],
      heart: ['الزنجبيل الحار', 'جوزة الطيب', 'الياسمين المائي'],
      base: ['خشب الصندل الأبيض', 'البخور الملكي', 'الفيتيفر', 'الباتشولي']
    },
    variants: [
      { size: '50ml', price: 890, inStock: true },
      { size: '100ml', price: 1490, inStock: true }
    ],
    image: '/media/orq/aa51d35b-b89c-475b-9c4b-0e4e192d327c.jpg'
  },
  {
    slug: 'vigorous',
    name: 'VIGOROUS',
    arabicName: 'فيجوروس',
    subtitle: 'انتعاش مائي وحيوية نقية لا تنضب',
    originalInspiration: 'توليفة بحرية عشبية منعشة',
    category: 'Fresh / Clean',
    brandFeel: 'Active freshness',
    scentType: 'مائي • أخضر • منعش',
    description: 'نسيم المحيط البارد وأمواج المياه النقية ممتزجة مع النعناع واللافندر وأخشاب الأرز العطرة. عطر الانتعاش الكلاسيكي الراقي لصباحات العمل وأيام الصيف.',
    mood: 'sporty_fresh',
    notes: {
      top: ['النعناع البارد', 'نوتات عشبية خضراء', 'الكزبرة'],
      heart: ['اللافندر الخزامى', 'إكليل الجبل (الروزماري)', 'الياسمين'],
      base: ['المسك النظيف', 'خشب الأرز', 'خشب الصندل', 'العنبر']
    },
    variants: [
      { size: '50ml', price: 720, inStock: true },
      { size: '100ml', price: 1199, inStock: true }
    ],
    image: '/media/orq/11206483-815d-4610-a05d-b5d5fc2397db.jpg'
  },
  {
    slug: 'saint',
    name: 'SAINT',
    arabicName: 'سينت',
    subtitle: 'نقاء المسك البودري ورقي الحضور الهادئ',
    originalInspiration: 'توليفة نقية من أصفى أنواع المسك الأبيض',
    category: 'Clean / Skin',
    brandFeel: 'Pure superiority',
    scentType: 'مسكي • بودري • ناعم',
    description: 'أرقى درجات المسك الأبيض الفاخر الممزوج بلمسات بودرية وزهور بيضاء ناعمة. يمنحك شعوراً بالفخامة النظيفة الفائقة وكأنها رائحة بشرتك الطبيعية بأسمى صورها.',
    mood: 'warm_formal',
    notes: {
      top: ['المسك الأبيض النقي', 'زهر البرتقال الخفيف'],
      heart: ['زهور بيضاء مخملية', 'لمسات بودرية'],
      base: ['المسك الصافي', 'خشب الأرز الخفيف']
    },
    variants: [
      { size: '50ml', price: 790, inStock: true },
      { size: '100ml', price: 1290, inStock: true }
    ],
    image: '/media/orq/334ae943-c027-4d78-bba3-94c02df1ffdd.jpg'
  },
  {
    slug: 'oud-ambassador',
    name: 'غرور',
    arabicName: 'Oud Ambassador',
    subtitle: 'العود الناعم والتفوق الهادئ',
    originalInspiration: 'عود معتق فاخر بلمسات الورد والزعفران',
    category: 'Oud / Luxury',
    brandFeel: 'Calm superiority',
    scentType: 'عود • حلو • خشبي',
    description: 'تناغم ساحر بين عراقة الشرق ورقة الغرب. عود هادئ معتق مغمور ببتلات الورد الدمشقي والعنبر الذهبي والفانيليا الناعمة، ليمنحك هالة أرستقراطية آسرة.',
    mood: 'royal_oud',
    isBestSeller: true,
    notes: {
      top: ['الورد الدمشقي المخملي', 'الزعفران الملكي'],
      heart: ['العنبر الذهبي المصفى', 'خشب الصندل'],
      base: ['العود المعتق الفاخر', 'الفانيليا المخملية']
    },
    variants: [
      { size: '50ml', price: 1190, inStock: true },
      { size: '100ml', price: 1999, inStock: true }
    ],
    image: '/media/orq/e15d2bf1-23e2-4bf9-9a8b-e8845ffd973d.jpg'
  },
  {
    slug: 'oud-madawi',
    name: 'كاسح',
    arabicName: 'Oud Madawi',
    subtitle: 'فوحان كاسح وهيمنة لا تتكرر',
    originalInspiration: 'عود شرقي مكثف بلمسات الفاكهة والعنبر',
    category: 'Oud / Luxury',
    brandFeel: 'Overpowering dominance',
    scentType: 'عود • حلو • فاكهي',
    description: 'الاسم يعبر تماماً عن الأثر. فوحان جبار وثبات أسطوري يملأ المكان بمزيج فريد من التفاح والخوخ مع مسك نقي وعود شرقي كثيف يترك بصمتك أينما حللت.',
    mood: 'royal_oud',
    notes: {
      top: ['زهر التفاح', 'الخوخ البري الناضج'],
      heart: ['زهرة الأناناس', 'المسك الأبيض الفاخر'],
      base: ['العود الكثيف الملكي', 'العنبر الأسود', 'الباتشولي']
    },
    variants: [
      { size: '50ml', price: 1190, inStock: true },
      { size: '100ml', price: 1999, inStock: true }
    ],
    image: '/media/orq/ac8eac34-874a-49dc-8ecf-dd73aa4d4d22.jpg'
  }
];

// Dedicated Royal Gift Box Bundle SKU
export const GIFT_BUNDLE: Product = {
  slug: 'bundle-gift-box',
  name: 'باقة الهدايا الملكية',
  arabicName: 'صندوق التجربة المتكامل',
  subtitle: 'عطر 50 مل + معطر سيارة + 4 عينات لاكتشاف التشكيلة',
  originalInspiration: 'إصدار الهدايا والتجربة الشاملة من ORQ',
  category: 'Exclusive Bundle',
  brandFeel: 'The Complete Gift Experience',
  scentType: 'صندوق فاخر متكامل',
  description: 'كل ما تحتاجه في صندوق فاخر واحد: اختر أي عطر مفضل لديك بحجم 50 مل، بالإضافة إلى معطر سيارة فاخر من نفس الهوية، و4 عينات عطرية مجانية لاكتشاف باقي المجموعة.',
  mood: 'royal_oud',
  isBestSeller: true,
  notes: {
    top: ['خلاصات البرغموت المنعشة'],
    heart: ['الأخشاب والجلود النادرة'],
    base: ['العود الملكي والمسك النقي']
  },
  variants: [
    { size: '50ml', price: 750, inStock: true }
  ],
  image: '/media/orq/52adf269-3af1-47ab-9da9-af148cbd6349.jpg'
};

export function getProductBySlug(slug: string): Product | undefined {
  if (slug === GIFT_BUNDLE.slug) return GIFT_BUNDLE;
  return PRODUCTS.find((p) => p.slug === slug);
}

export function getProductsByMood(mood: string): Product[] {
  if (mood === 'all') return PRODUCTS;
  return PRODUCTS.filter((p) => p.mood === mood);
}
