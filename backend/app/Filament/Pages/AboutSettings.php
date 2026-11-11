<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class AboutSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;
    protected static ?string $navigationLabel = 'About Page';
    protected static ?string $title = 'About Page Settings';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.about-settings';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $homeImage = Setting::getValue('about_home_image', '');
        $pageImage = Setting::getValue('about_page_image', '');

        $this->form->fill([
            'about_who_we_are' => Setting::getValue('about_who_we_are', ''),
            'about_who_we_are_ar' => Setting::getValue('about_who_we_are_ar', ''),
            'about_vision' => Setting::getValue('about_vision', ''),
            'about_vision_ar' => Setting::getValue('about_vision_ar', ''),
            'about_mission' => Setting::getValue('about_mission', ''),
            'about_mission_ar' => Setting::getValue('about_mission_ar', ''),
            'about_home_image' => $homeImage ? [$homeImage] : [],
            'about_page_image' => $pageImage ? [$pageImage] : [],
            
            // Why Choose Us
            'why_choose_title_en' => Setting::getValue('why_choose_title_en', 'Why Choose Medicamo?'),
            'why_choose_title_ar' => Setting::getValue('why_choose_title_ar', 'لماذا تختار ميديكامو؟'),
            'why_choose_subtitle_en' => Setting::getValue('why_choose_subtitle_en', 'We offer you the best pharmaceutical shopping experience'),
            'why_choose_subtitle_ar' => Setting::getValue('why_choose_subtitle_ar', 'نقدم لك أفضل تجربة تسوق للمنتجات الطبية'),
            'why_choose_features' => json_decode(Setting::getValue('why_choose_features', '[]'), true) ?: [],
        ]);
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            \Filament\Schemas\Components\Tabs::make('About Sections')
                ->tabs([
                    \Filament\Schemas\Components\Tabs\Tab::make('General About (Who We Are / Vision)')
                        ->schema([
                            Section::make('Images')
                                ->description('Upload images for the About sections. If left empty, default images will be used.')
                                ->schema([
                                    FileUpload::make('about_home_image')
                                        ->label('Homepage About Section Image')
                                        ->helperText('Image shown in the "Who We Are" section on the homepage.')
                                        ->disk('public')
                                        ->directory('about')
                                        ->image()
                                        ->visibility('public')
                                        ->openable()
                                        ->downloadable(),

                                    FileUpload::make('about_page_image')
                                        ->label('About Page Image')
                                        ->helperText('Image shown in the Vision & Mission section on the About page.')
                                        ->disk('public')
                                        ->directory('about')
                                        ->image()
                                        ->visibility('public')
                                        ->openable()
                                        ->downloadable(),
                                ])->columns(2),

                            Section::make('Who We Are - English')
                                ->schema([
                                    RichEditor::make('about_who_we_are')
                                        ->label('Content (English)')
                                        ->helperText('This appears on both the homepage and the about page.')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('من نحن - عربي')
                                ->schema([
                                    RichEditor::make('about_who_we_are_ar')
                                        ->label('المحتوى (عربي)')
                                        ->helperText('يظهر في الصفحة الرئيسية وصفحة من نحن.')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Our Vision')
                                ->schema([
                                    Textarea::make('about_vision')
                                        ->label('Vision Text (English)')
                                        ->rows(4),

                                    Textarea::make('about_vision_ar')
                                        ->label('نص الرؤية (عربي)')
                                        ->rows(4),
                                ])->columns(2),

                            Section::make('Our Mission')
                                ->schema([
                                    Textarea::make('about_mission')
                                        ->label('Mission Text (English)')
                                        ->rows(4),

                                    Textarea::make('about_mission_ar')
                                        ->label('نص الرسالة (عربي)')
                                        ->rows(4),
                                ])->columns(2),
                        ]),
                        
                    \Filament\Schemas\Components\Tabs\Tab::make('Why Choose Us Section')
                        ->schema([
                            Section::make('Section Header')
                                ->schema([
                                    \Filament\Forms\Components\TextInput::make('why_choose_title_en')
                                        ->label('Title (English)')
                                        ->required(),
                                    \Filament\Forms\Components\TextInput::make('why_choose_title_ar')
                                        ->label('Title (Arabic)')
                                        ->required(),
                                    \Filament\Forms\Components\TextInput::make('why_choose_subtitle_en')
                                        ->label('Subtitle (English)'),
                                    \Filament\Forms\Components\TextInput::make('why_choose_subtitle_ar')
                                        ->label('Subtitle (Arabic)'),
                                ])->columns(2),
                                
                            Section::make('Features List')
                                ->schema([
                                    \Filament\Forms\Components\Repeater::make('why_choose_features')
                                        ->label('Features')
                                        ->addActionLabel('Add Feature')
                                        ->schema([
                                            \Filament\Forms\Components\TextInput::make('icon')
                                                ->label('FontAwesome Icon Class')
                                                ->helperText('e.g., fas fa-certificate, fas fa-tag, fas fa-shipping-fast')
                                                ->required()
                                                ->columnSpanFull(),
                                            
                                            \Filament\Forms\Components\TextInput::make('title_en')
                                                ->label('Title (English)')
                                                ->required(),
                                            \Filament\Forms\Components\TextInput::make('title_ar')
                                                ->label('Title (Arabic)')
                                                ->required(),
                                                
                                            Textarea::make('desc_en')
                                                ->label('Description (English)')
                                                ->rows(3)
                                                ->required(),
                                            Textarea::make('desc_ar')
                                                ->label('Description (Arabic)')
                                                ->rows(3)
                                                ->required(),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(4)
                                        ->reorderableWithButtons()
                                        ->collapsible(),
                                ]),
                        ]),
                ])->columnSpanFull(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::setValue('about_who_we_are', $data['about_who_we_are'] ?? '');
        Setting::setValue('about_who_we_are_ar', $data['about_who_we_are_ar'] ?? '');
        Setting::setValue('about_vision', $data['about_vision'] ?? '');
        Setting::setValue('about_vision_ar', $data['about_vision_ar'] ?? '');
        Setting::setValue('about_mission', $data['about_mission'] ?? '');
        Setting::setValue('about_mission_ar', $data['about_mission_ar'] ?? '');
        
        // Why Choose Us
        Setting::setValue('why_choose_title_en', $data['why_choose_title_en'] ?? '');
        Setting::setValue('why_choose_title_ar', $data['why_choose_title_ar'] ?? '');
        Setting::setValue('why_choose_subtitle_en', $data['why_choose_subtitle_en'] ?? '');
        Setting::setValue('why_choose_subtitle_ar', $data['why_choose_subtitle_ar'] ?? '');
        Setting::setValue('why_choose_features', json_encode($data['why_choose_features'] ?? []));

        // FileUpload returns array, store first value as string
        $homeImage = $data['about_home_image'] ?? [];
        $pageImage = $data['about_page_image'] ?? [];
        Setting::setValue('about_home_image', is_array($homeImage) ? (reset($homeImage) ?: '') : $homeImage);
        Setting::setValue('about_page_image', is_array($pageImage) ? (reset($pageImage) ?: '') : $pageImage);

        Notification::make()
            ->title('About page settings saved successfully!')
            ->success()
            ->send();
    }
}
