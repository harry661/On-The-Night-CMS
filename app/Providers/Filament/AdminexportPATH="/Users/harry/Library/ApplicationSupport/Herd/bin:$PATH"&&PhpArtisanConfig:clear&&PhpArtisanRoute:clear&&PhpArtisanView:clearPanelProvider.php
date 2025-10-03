<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminexportPATH="/Users/harry/Library/ApplicationSupport/Herd/bin:$PATH"&&PhpArtisanConfig:clear&&PhpArtisanRoute:clear&&PhpArtisanView:clearPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('adminexport PATH="/Users/harry/Library/Application Support/Herd/bin:$PATH" && php artisan config:clear && php artisan route:clear && php artisan view:clear')
            ->path('adminexport PATH="/Users/harry/Library/Application Support/Herd/bin:$PATH" && php artisan config:clear && php artisan route:clear && php artisan view:clear')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/AdminexportPATH="/Users/harry/Library/ApplicationSupport/Herd/bin:$PATH"&&PhpArtisanConfig:clear&&PhpArtisanRoute:clear&&PhpArtisanView:clear/Resources'), for: 'App\\Filament\\AdminexportPATH="/Users/harry/Library/ApplicationSupport/Herd/bin:$PATH"&&PhpArtisanConfig:clear&&PhpArtisanRoute:clear&&PhpArtisanView:clear\\Resources')
            ->discoverPages(in: app_path('Filament/AdminexportPATH="/Users/harry/Library/ApplicationSupport/Herd/bin:$PATH"&&PhpArtisanConfig:clear&&PhpArtisanRoute:clear&&PhpArtisanView:clear/Pages'), for: 'App\\Filament\\AdminexportPATH="/Users/harry/Library/ApplicationSupport/Herd/bin:$PATH"&&PhpArtisanConfig:clear&&PhpArtisanRoute:clear&&PhpArtisanView:clear\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/AdminexportPATH="/Users/harry/Library/ApplicationSupport/Herd/bin:$PATH"&&PhpArtisanConfig:clear&&PhpArtisanRoute:clear&&PhpArtisanView:clear/Widgets'), for: 'App\\Filament\\AdminexportPATH="/Users/harry/Library/ApplicationSupport/Herd/bin:$PATH"&&PhpArtisanConfig:clear&&PhpArtisanRoute:clear&&PhpArtisanView:clear\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
