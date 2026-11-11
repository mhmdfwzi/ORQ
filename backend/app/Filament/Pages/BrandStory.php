<?php

namespace App\Filament\Pages;

use App\Support\BrandStory as BrandStoryContent;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

/**
 * Admin editor for the storefront "شفرة ORQ" brand story section.
 */
class BrandStory extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;
    protected static ?string $navigationLabel = 'قصة البراند';
    protected static ?string $title = 'قصة البراند (شفرة ORQ)';
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.brand-story';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(BrandStoryContent::get());
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('العنوان والمقدمة')
                ->schema([
                    TextInput::make('eyebrow')->label('العنوان الصغير')->required()->maxLength(60),
                    TextInput::make('title')->label('العنوان الرئيسي')->required()->maxLength(160),
                    Textarea::make('name_intro')->label('معنى الاسم (مقدمة)')->rows(2)->required()->columnSpanFull(),
                    Textarea::make('story_intro')->label('حكاية البراند (مقدمة)')->rows(5)->required()->columnSpanFull(),
                ])->columns(2),

            Section::make('الركائز (O · R · Q)')
                ->schema([
                    Repeater::make('pillars')
                        ->label('الركائز')
                        ->schema([
                            TextInput::make('letter')->label('الحرف')->required()->maxLength(2),
                            TextInput::make('en')->label('English')->required()->maxLength(40),
                            TextInput::make('ar')->label('بالعربي')->required()->maxLength(40),
                            Textarea::make('meaning')->label('المعنى (جملة قصيرة)')->rows(2)->required()->columnSpanFull(),
                            Textarea::make('story')->label('الحكاية')->rows(4)->required()->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->minItems(1)
                        ->maxItems(6)
                        ->reorderableWithButtons()
                        ->collapsible(),
                ]),

            Section::make('الخاتمة')
                ->schema([
                    TextInput::make('closing')->label('الجملة الختامية')->required()->maxLength(255)->columnSpanFull(),
                    TextInput::make('signature')->label('التوقيع')->required()->maxLength(255)->columnSpanFull(),
                ]),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        BrandStoryContent::save($data);

        Notification::make()
            ->title('تم حفظ قصة البراند')
            ->success()
            ->send();
    }
}

