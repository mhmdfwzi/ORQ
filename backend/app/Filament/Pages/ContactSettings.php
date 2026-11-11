<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ContactSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;
    protected static ?string $navigationLabel = 'Contact Settings';
    protected static ?string $title = 'Contact Settings';
    protected static ?int $navigationSort = 1;
    
    protected string $view = 'filament.pages.contact-settings';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        
        $this->form->fill([
            'contact_phone' => $settings['contact_phone'] ?? '',
            'contact_phone_2' => $settings['contact_phone_2'] ?? '',
            'whatsapp_number' => $settings['whatsapp_number'] ?? '',
            'contact_email' => $settings['contact_email'] ?? '',
            'contact_email_2' => $settings['contact_email_2'] ?? '',
            'contact_address' => $settings['contact_address'] ?? '',
            'working_hours' => $settings['working_hours'] ?? '',
            'google_map_embed' => $settings['google_map_embed'] ?? '',
            'facebook_url' => $settings['facebook_url'] ?? '',
            'instagram_url' => $settings['instagram_url'] ?? '',
            'twitter_url' => $settings['twitter_url'] ?? '',
            'youtube_url' => $settings['youtube_url'] ?? '',
            'linkedin_url' => $settings['linkedin_url'] ?? '',
            'tiktok_url' => $settings['tiktok_url'] ?? '',
        ]);
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
                Section::make('Phone Numbers')
                    ->description('Configure your contact phone numbers')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label('Primary Phone')
                            ->tel()
                            ->placeholder('01XXXXXXXXX')
                            ->helperText('Main contact phone number'),
                        
                        TextInput::make('contact_phone_2')
                            ->label('Secondary Phone')
                            ->tel()
                            ->placeholder('01XXXXXXXXX')
                            ->helperText('Alternative phone number (optional)'),
                        
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp Number')
                            ->tel()
                            ->placeholder('201XXXXXXXXX')
                            ->helperText('Include country code (e.g., 201 for Egypt)'),
                    ])->columns(3),

                Section::make('Email Addresses')
                    ->description('Configure your contact email addresses')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        TextInput::make('contact_email')
                            ->label('Primary Email')
                            ->email()
                            ->placeholder('info@medicamo.com'),
                        
                        TextInput::make('contact_email_2')
                            ->label('Secondary Email')
                            ->email()
                            ->placeholder('support@medicamo.com'),
                    ])->columns(2),

                Section::make('Address & Location')
                    ->description('Configure your business address and map')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Textarea::make('contact_address')
                            ->label('Business Address')
                            ->rows(2)
                            ->placeholder('123 Street Name, City, Country')
                            ->columnSpanFull(),
                        
                        Textarea::make('google_map_embed')
                            ->label('Google Maps Embed URL')
                            ->rows(2)
                            ->placeholder('https://www.google.com/maps/embed?pb=...')
                            ->helperText('Get this from Google Maps > Share > Embed a map')
                            ->columnSpanFull(),
                    ]),

                Section::make('Working Hours')
                    ->description('Configure your business working hours')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Textarea::make('working_hours')
                            ->label('Working Hours')
                            ->rows(3)
                            ->placeholder("Saturday - Thursday: 9:00 AM - 10:00 PM\nFriday: Closed")
                            ->helperText('Enter each line separately'),
                    ]),

                Section::make('Social Media Links')
                    ->description('Configure your social media profiles')
                    ->icon('heroicon-o-share')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://facebook.com/yourpage')
                            ->prefixIcon('heroicon-o-globe-alt'),
                        
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('https://instagram.com/yourpage')
                            ->prefixIcon('heroicon-o-globe-alt'),
                        
                        TextInput::make('twitter_url')
                            ->label('Twitter / X')
                            ->url()
                            ->placeholder('https://twitter.com/yourpage')
                            ->prefixIcon('heroicon-o-globe-alt'),
                        
                        TextInput::make('youtube_url')
                            ->label('YouTube')
                            ->url()
                            ->placeholder('https://youtube.com/c/yourchannel')
                            ->prefixIcon('heroicon-o-globe-alt'),
                        
                        TextInput::make('linkedin_url')
                            ->label('LinkedIn')
                            ->url()
                            ->placeholder('https://linkedin.com/company/yourcompany')
                            ->prefixIcon('heroicon-o-globe-alt'),
                        
                        TextInput::make('tiktok_url')
                            ->label('TikTok')
                            ->url()
                            ->placeholder('https://tiktok.com/@yourpage')
                            ->prefixIcon('heroicon-o-globe-alt'),
                    ])->columns(2),
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

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value ?? '', 'contact');
        }

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }
}
