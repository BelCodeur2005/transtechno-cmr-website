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
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(\App\Filament\Pages\EditProfile::class, isSimple: false)
            ->brandName('Transtechno Cameroun')
            ->brandLogo(fn () => file_exists(public_path('images/logo.png'))
                ? asset('images/logo.png')
                : null)
            ->brandLogoHeight('3rem')
            ->favicon(asset('favicon.ico'))
            ->font('Inter')
            ->colors([
                'primary' => Color::hex('#004A80'),   // navy-blue — couleur structurelle
                'danger' => Color::hex('#E31E24'),    // brand-red — CTA / urgent
                'success' => Color::hex('#16a34a'),   // En stock / Gagné
                'warning' => Color::hex('#ea580c'),   // Critique
                'info' => Color::hex('#006099'),      // tertiary — En transit / liens
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            // Police Material Symbols : aperçu visuel des icônes dans les sélecteurs (catégories, pôles).
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />'
                    . '<style>.material-symbols-outlined{font-family:"Material Symbols Outlined";font-weight:normal;font-style:normal;line-height:1;letter-spacing:normal;text-transform:none;display:inline-block;white-space:nowrap;direction:ltr;}</style>',
            )
            // Modale médiathèque (sélecteur d'image type WordPress) disponible sur tout le panel.
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('<livewire:media-library mode="picker" lazy />'),
            )
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
                \App\Http\Middleware\TrackLastSeen::class,
            ])
            ->plugin(
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            );
    }
}
