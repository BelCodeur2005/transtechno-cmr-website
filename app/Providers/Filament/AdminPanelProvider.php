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
            ->passwordReset()
            ->profile(\App\Filament\Pages\EditProfile::class, isSimple: false)
            ->brandName('Transtechno Cameroun')
            ->brandLogo(fn () => file_exists(public_path('images/logo.png'))
                ? asset('images/logo.png')
                : null)
            ->brandLogoHeight('3rem')
            ->favicon(asset('favicon.ico'))
            ->spa()
            ->sidebarCollapsibleOnDesktop()
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
                    . '<style>.material-symbols-outlined{font-family:"Material Symbols Outlined";font-weight:normal;font-style:normal;line-height:1;letter-spacing:normal;text-transform:none;display:inline-block;white-space:nowrap;direction:ltr;}</style>'
                    // Bouton médiathèque dans la barre d'outils de chaque RichEditor (Trix).
                    // Placé en HEAD_END pour s'exécuter avant l'initialisation de Trix.
                    . '<script>
(function () {
    var ICON = \'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>\';

    function addButton(editor) {
        var toolbarId = editor.getAttribute("toolbar");
        if (!toolbarId) return;
        var toolbar = document.getElementById(toolbarId);
        if (!toolbar || toolbar.querySelector(".mlib-rich-btn")) return;

        var wrap = document.createElement("div");
        wrap.className = "flex gap-x-1";

        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "mlib-rich-btn flex h-8 min-w-[2rem] cursor-pointer items-center justify-center rounded-lg text-gray-700 transition duration-75 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5";
        btn.title = "Insérer une image depuis la médiathèque";
        btn.innerHTML = ICON;
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            window.__mediaTargetTrix = editor;
            editor.dispatchEvent(new CustomEvent("open-media-library", {
                detail: { fieldKey: "richeditor" },
                bubbles: true,
                composed: true,
            }));
        });

        wrap.appendChild(btn);
        var row = toolbar.querySelector(".flex.gap-x-3");
        if (row) row.appendChild(wrap);
    }

    // Trix lève trix-initialize sur le document quand un éditeur est prêt.
    document.addEventListener("trix-initialize", function (e) { addButton(e.target); });

    // Sécurité SPA : si des éditeurs existent déjà quand ce script s\'exécute.
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll("trix-editor").forEach(addButton);
        });
    } else {
        document.querySelectorAll("trix-editor").forEach(addButton);
    }

    // Insertion de l\'image choisie dans la médiathèque.
    window.addEventListener("media-inserted", function (e) {
        if (!e.detail || e.detail.fieldKey !== "richeditor") return;
        var url = e.detail.url || e.detail.path || "";
        var el = window.__mediaTargetTrix || document.querySelector("trix-editor");
        if (el && el.editor) { el.editor.insertHTML(\'<img src="\' + url + \'" alt="">\'); el.focus(); }
    });
}());
</script>',
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
