<?php

namespace App\Support;

use App\Models\Setting;

/**
 * ORQ brand story ("شفرة ORQ"): defaults + accessor.
 *
 * Stored as one JSON document in `settings.brand_story` and edited from the
 * Filament "Brand Story" page. Defaults are used until an admin saves changes.
 */
class BrandStory
{
    public const KEY = 'brand_story';

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'eyebrow' => 'شفرة ORQ',
            'title' => 'ORQ ليس مجرد أحرف عشوائية',
            'name_intro' => 'اسم ORQ هو اختصار لثلاث كلمات تمثل الركائز الأساسية التي يبحث عنها أي رجل أنيق في عطره.',
            'story_intro' => 'في عالم مليء بالخيارات المكررة والروائح المعتادة، يبحث الرجل الواثق عن شيء يعبر عنه بصدق.. عن بصمة تسبق خطواته وتتحدث بالنيابة عنه. من هنا، ابتكرنا ORQ. نحن لم نصنع عطراً تقليدياً، بل صممنا "شفرة" نجاح وتفرد تتكون من ثلاثة أركان صُممت خصيصاً لتعكس شخصيتك.',
            'pillars' => [
                [
                    'letter' => 'O',
                    'en' => 'Original',
                    'ar' => 'الأصالة',
                    'meaning' => 'لأنك لست نسخة من أحد، وعطرك يجب أن يكون توقيعك الخاص.',
                    'story' => 'بدأنا بالأصالة؛ إيماناً منا بأن العطر هو هويتك غير المرئية. ابتكرنا تركيبة غير مألوفة، تعبر عن الرجل الذي يكتب قواعده بنفسه ولا يتبع القطيع، ليكون عطرك فريداً يشبهك وحدك.',
                ],
                [
                    'letter' => 'R',
                    'en' => 'Rare',
                    'ar' => 'الندرة',
                    'meaning' => 'لأن حضورك لا يتكرر، واختياراتك دائماً استثنائية.',
                    'story' => 'ثم بحثنا عن الندرة؛ فاخترنا نوتات عطرية لا تجدها في كل مكان. دمجنا أندر المكونات لنخلق مزيجاً يحمل غموضاً جذاباً وتفرداً يجعلك محط الأنظار في أي مكان تدخله. حضورك نادر، وعطرك يجب أن يكون كذلك.',
                ],
                [
                    'letter' => 'Q',
                    'en' => 'Quality',
                    'ar' => 'الجودة',
                    'meaning' => 'لأنك لا تقبل بأنصاف الحلول، وتستحق أثراً يدوم ويثبت.',
                    'story' => 'وتوجنا هذه الرحلة بالجودة المطلقة؛ التزامنا الصارم باختيار أنقى الخلاصات وأكثرها ثباتاً. لم نساوم على الأداء، لنضمن لك عطراً يصمد معك طوال اليوم، ويليق برجل لا يرضى إلا بالامتياز.',
                ],
            ],
            'closing' => 'ORQ ليس مجرد عطر تضعه في الصباح.. بل هو شفرتك الخاصة.',
            'signature' => 'أصلي في هويته، نادر في سحره، واستثنائي في جودته.',
        ];
    }

    /** @return array<string, mixed> */
    public static function get(): array
    {
        $stored = json_decode((string) Setting::getValue(self::KEY, ''), true);

        return is_array($stored) ? array_replace(self::defaults(), $stored) : self::defaults();
    }

    /** @param array<string, mixed> $data */
    public static function save(array $data): void
    {
        Setting::setValue(self::KEY, json_encode($data, JSON_UNESCAPED_UNICODE), 'brand');
    }
}

