<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class NotificationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;
    protected static ?string $navigationLabel = 'Notification Channels';
    protected static ?string $title = 'Notification Channels';
    protected static ?int $navigationSort = 2;
    
    protected string $view = 'filament.pages.notification-settings';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        
        $this->form->fill([
            'otp_verify_channel' => $settings['otp_verify_channel'] ?? 'whatsapp',
            'otp_reset_channel' => $settings['otp_reset_channel'] ?? 'whatsapp',
            'order_confirm_customer_channel' => $settings['order_confirm_customer_channel'] ?? 'whatsapp',
            'order_confirm_admin_channel' => $settings['order_confirm_admin_channel'] ?? 'whatsapp',
            'admin_email' => $settings['admin_email'] ?? 'medicamodrug@gmail.com',
        ]);
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        $channelOptions = [
            'whatsapp' => '📱 WhatsApp Only',
            'email' => '📧 Email Only',
            'both' => '📱📧 Both (WhatsApp + Email)',
        ];

        return [
            Section::make('OTP - Account Verification')
                ->description('Choose how to send OTP codes for new account verification')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Radio::make('otp_verify_channel')
                        ->label('Channel')
                        ->options($channelOptions)
                        ->default('whatsapp')
                        ->required(),
                ]),

            Section::make('OTP - Password Reset')
                ->description('Choose how to send OTP codes for password reset')
                ->icon('heroicon-o-key')
                ->schema([
                    Radio::make('otp_reset_channel')
                        ->label('Channel')
                        ->options($channelOptions)
                        ->default('whatsapp')
                        ->required(),
                ]),

            Section::make('Order Confirmation - Customer')
                ->description('Choose how to send order confirmations to customers')
                ->icon('heroicon-o-shopping-bag')
                ->schema([
                    Radio::make('order_confirm_customer_channel')
                        ->label('Channel')
                        ->options($channelOptions)
                        ->default('whatsapp')
                        ->required(),
                ]),

            Section::make('Order Notification - Admin')
                ->description('Choose how to receive new order notifications')
                ->icon('heroicon-o-bell-alert')
                ->schema([
                    Radio::make('order_confirm_admin_channel')
                        ->label('Channel')
                        ->options($channelOptions)
                        ->default('whatsapp')
                        ->required(),

                    TextInput::make('admin_email')
                        ->label('Admin Email (for email notifications)')
                        ->email()
                        ->placeholder('medicamodrug@gmail.com')
                        ->helperText('Email address to receive admin notifications'),
                ]),
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
            Setting::setValue($key, $value ?? '', 'notifications');
        }

        Notification::make()
            ->title('Notification settings saved successfully!')
            ->success()
            ->send();
    }
}
