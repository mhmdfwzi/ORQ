<?php

namespace App\Providers\Filament;

use App\Http\Middleware\AdminLocale;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    // The production reverse proxy serves Laravel styles from /css/filament/.
    private const ADMIN_STYLESHEET = 'css/filament/orq/admin.css';

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->databaseNotifications()
            ->databaseNotificationsPolling('15s')
            ->brandName('ORQ')
            ->brandLogo(fn () => view('filament.admin-logo'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/orq-favicon.png'))
            ->colors([
                'primary' => Color::generateV3Palette('#a47743'),
                'gray' => Color::Stone,
            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->maxContentWidth(Width::Full)
            ->sidebarWidth('15rem')
            ->navigationGroups(['الطلبات', 'الكتالوج', 'التسويق', 'العملاء', 'المحتوى', 'التوظيف', 'الإعدادات'])
            ->renderHook(PanelsRenderHook::STYLES_AFTER, fn () => new HtmlString(
                '<link rel="stylesheet" href="'.e(asset(self::ADMIN_STYLESHEET).'?v='.filemtime(public_path(self::ADMIN_STYLESHEET))).'">',
            ))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->middleware([AdminLocale::class], isPersistent: true)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
