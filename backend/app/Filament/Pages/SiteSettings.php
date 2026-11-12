<?php

namespace App\Filament\Pages;

use App\Support\SiteSettings as SiteSettingsContent;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

/**
 * Admin editor for all storefront-wide content stored in the database.
 */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static ?string $navigationLabel = 'إعدادات الموقع';
    protected static ?string $title = 'إعدادات الموقع';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.site-settings';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSettingsContent::get());
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('Site')->tabs([

                Tab::make('الهوية والـ SEO')->schema([
                    Section::make('الهوية')->schema([
                        TextInput::make('name')->label('اسم البراند')->required()->maxLength(60),
                        TextInput::make('tagline')->label('الوصف تحت الاسم')->required()->maxLength(80),
                        TextInput::make('motto')->label('الشعار (الفوتر)')->maxLength(160)->columnSpanFull(),
                    ])->columns(2),
                    Section::make('محركات البحث والمشاركة')->schema([
                        TextInput::make('seo.title_default')->label('عنوان الصفحة الافتراضي')->required()->maxLength(160)->columnSpanFull(),
                        TextInput::make('seo.title_template')->label('قالب العنوان (استخدم %s)')->required()->maxLength(160)->columnSpanFull(),
                        Textarea::make('seo.description')->label('الوصف (Meta Description)')->rows(3)->required()->columnSpanFull(),
                        Textarea::make('seo.og_description')->label('وصف المشاركة (Open Graph)')->rows(2)->columnSpanFull(),
                        Textarea::make('seo.twitter_description')->label('وصف تويتر')->rows(2)->columnSpanFull(),
                        Textarea::make('seo.organization_description')->label('وصف المنشأة (Schema.org)')->rows(2)->columnSpanFull(),
                        TagsInput::make('seo.keywords')->label('الكلمات المفتاحية')->columnSpanFull(),
                        TextInput::make('seo.twitter_handle')->label('حساب تويتر')->maxLength(60),
                    ])->columns(2),
                ]),

                Tab::make('الفوتر')->schema([
                    Section::make('النصوص')->schema([
                        Textarea::make('footer.description')->label('وصف البراند')->rows(3)->required()->columnSpanFull(),
                        TextInput::make('footer.links_title')->label('عنوان قائمة العطور'),
                        TextInput::make('footer.support_title')->label('عنوان خدمة العملاء'),
                        Textarea::make('footer.support_text')->label('نص خدمة العملاء')->rows(2)->columnSpanFull(),
                        TextInput::make('footer.support_badge')->label('شارة خدمة العملاء'),
                        TextInput::make('footer.copyright')->label('حقوق النشر')->columnSpanFull(),
                        TagsInput::make('footer.legal_links')->label('الروابط القانونية')->columnSpanFull(),
                    ])->columns(2),
                    Section::make('مزايا الثقة (4 مربعات)')->schema([
                        Repeater::make('footer.trust_features')->label('المزايا')
                            ->schema([
                                Select::make('icon')->label('الأيقونة')->options([
                                    'sample' => 'عينة', 'delivery' => 'توصيل', 'cod' => 'دفع عند الاستلام', 'oils' => 'زيوت/جودة',
                                ])->required(),
                                TextInput::make('title')->label('العنوان')->required(),
                                Textarea::make('desc')->label('الوصف')->rows(2)->required()->columnSpanFull(),
                            ])->columns(2)->maxItems(6)->reorderableWithButtons()->collapsible(),
                    ]),
                ]),

                Tab::make('فلاتر الطابع (Mood)')->schema([
                    TextInput::make('moods.all_label')->label('اسم فلتر "الكل" (العدد يُضاف تلقائياً)')->required(),
                    Repeater::make('moods.items')->label('الفلاتر')
                        ->schema([
                            Select::make('id')->label('الطابع')->options([
                                'royal_oud' => 'royal_oud', 'night_allure' => 'night_allure',
                                'sporty_fresh' => 'sporty_fresh', 'warm_formal' => 'warm_formal',
                            ])->required(),
                            TextInput::make('label')->label('الاسم الظاهر')->required(),
                            TextInput::make('icon')->label('إيموجي')->maxLength(8),
                        ])->columns(3)->maxItems(4)->reorderableWithButtons(),
                ]),

                Tab::make('الدفع والشحن')->schema([
                    Section::make('الشحن والدفع')->schema([
                        TextInput::make('commerce.currency')->label('رمز العملة')->required()->maxLength(12),
                        TextInput::make('commerce.shipping_cost')->label('رسوم الشحن الثابتة (0 = مجاني)')
                            ->numeric()->minValue(0)->required()
                            ->helperText('تُطبّق على كل طلب وتظهر في السلة. السيرفر هو من يحسبها.'),
                        TextInput::make('commerce.shipping_note')->label('نص الشحن في السلة'),
                        TextInput::make('commerce.shipping_free_label')->label('نص الشحن المجاني'),
                        TextInput::make('commerce.payment_note')->label('نص طريقة الدفع'),
                    ])->columns(2),
                    Section::make('المحافظات')->schema([
                        TagsInput::make('commerce.governorates')->label('المحافظات المتاحة للشحن')->columnSpanFull(),
                    ]),
                ]),

                Tab::make('الشعار والباقة')->schema([
                    Section::make('جملة الهوية (أسفل الصفحة الرئيسية)')->schema([
                        TextInput::make('creed.eyebrow')->label('العنوان الصغير'),
                        TextInput::make('creed.quote')->label('الاقتباس')->columnSpanFull(),
                        Textarea::make('creed.text')->label('النص')->rows(3)->columnSpanFull(),
                    ])->columns(2),
                    Section::make('قسم باقة الهدايا')->schema([
                        TextInput::make('bundle.badge')->label('الشارة'),
                        TextInput::make('bundle.cta')->label('نص زر الطلب'),
                        Repeater::make('bundle.highlights')->label('محتويات الباقة')
                            ->schema([
                                TextInput::make('title')->label('العنوان')->required(),
                                TextInput::make('desc')->label('الوصف')->required(),
                            ])->columns(2)->maxItems(4)->reorderableWithButtons()->columnSpanFull(),
                    ])->columns(2),
                ]),

            ])->columnSpanFull(),
        ];
    }

    public function save(): void
    {
        SiteSettingsContent::save($this->form->getState());

        Notification::make()->title('تم حفظ إعدادات الموقع')->success()->send();
    }
}

