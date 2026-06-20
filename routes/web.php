<?php

use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Proxy média S3 (contourne le certificat auto-signé de N0C Storage) ────
Route::get('/media-proxy/{path}', function (string $path) {
    // Bloquer path traversal et chemins absolus
    if (str_contains($path, '..') || str_starts_with($path, '/') || str_contains($path, "\0")) {
        abort(404);
    }

    // Restreindre aux dossiers médias connus uniquement
    $allowed = ['media/', 'brands/', 'products/', 'news/'];
    $isAllowed = collect($allowed)->contains(fn ($prefix) => str_starts_with($path, $prefix));
    if (! $isAllowed) {
        abort(404);
    }

    // N'autoriser que les types MIME image/pdf — jamais HTML, JS, SVG
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif', 'application/pdf'];

    try {
        $stream = \Illuminate\Support\Facades\Storage::disk('s3')->readStream($path);
        if (! $stream) {
            abort(404);
        }
        $mime = \Illuminate\Support\Facades\Storage::disk('s3')->mimeType($path) ?: 'application/octet-stream';
        if (! in_array($mime, $allowedMimes, true)) {
            abort(403);
        }
        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type'           => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy'=> "default-src 'none'",
            'Cache-Control'          => 'public, max-age=31536000',
        ]);
    } catch (\Throwable) {
        abort(404);
    }
})->where('path', '[a-zA-Z0-9._/-]+')->name('media.proxy');

Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Catalogue public ───────────────────────────────────────
Route::get('/categories', [CatalogueController::class, 'categories'])->name('categories');
Route::get('/recherche/suggestions', [CatalogueController::class, 'suggest'])->middleware('throttle:60,1')->name('search.suggest');
Route::get('/catalogue', [CatalogueController::class, 'catalogue'])->name('catalogue');
Route::get('/reactifs/abm', [ServicesController::class, 'abm'])->name('abm');
Route::get('/reactifs', [CatalogueController::class, 'catalogue'])->defaults('scope', 'reactifs')->name('reactifs');
Route::get('/produits/{slug}', [CatalogueController::class, 'show'])->name('produits.show');

// ─── Cotation (devis) ───────────────────────────────────────
Route::get('/cotation', [QuotationController::class, 'recap'])->name('cotation');
Route::get('/demande-cotation', [QuotationController::class, 'demande'])->name('demande');
Route::get('/cotation/confirmation/{reference}', [QuotationController::class, 'confirmation'])->name('cotation.confirmation');
Route::get('/cotation/{quotation}/devis', [QuotationController::class, 'devis'])->middleware('auth')->name('cotation.devis');

// ─── Services & SAV ─────────────────────────────────────────
Route::get('/services-sav', [ServicesController::class, 'sav'])->name('services');
Route::get('/sav/demande', [ServicesController::class, 'savDemande'])->name('sav.demande');
Route::get('/pole-hemodialyse', [ServicesController::class, 'hemodialyse'])->name('pole.hemodialyse');

// ─── Actualités ─────────────────────────────────────────────
Route::get('/actualites', [NewsController::class, 'index'])->name('actualites');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// ─── Sitemap ────────────────────────────────────────────────
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ─── Pages statiques ────────────────────────────────────────
Route::get('/qui-sommes-nous', [PageController::class, 'about'])->name('about');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::get('/mentions-legales', [PageController::class, 'legal'])->name('legal');
Route::get('/politique-confidentialite', [PageController::class, 'privacy'])->name('privacy');
Route::get('/conditions-vente', [PageController::class, 'terms'])->name('terms');

// ─── Formulaires publics (anti-spam : 10 requêtes/minute/IP) ─
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/cotation', [QuotationController::class, 'store'])->name('cotation.store');
    Route::post('/demande-cotation', [QuotationController::class, 'storeDemande'])->name('demande.store');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::post('/sav/demande', [ServicesController::class, 'storeSavDemande'])->name('sav.demande.store');
    Route::post('/pole-hemodialyse', [ServicesController::class, 'storeProject'])->name('pole.project.store');
    Route::post('/reactifs/abm', [ServicesController::class, 'storeAbm'])->name('abm.store');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
