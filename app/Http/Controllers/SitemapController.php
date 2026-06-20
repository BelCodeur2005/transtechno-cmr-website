<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect();

        // ── Pages statiques ──────────────────────────────────────────────────
        $static = [
            ['loc' => '/',                          'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => '/catalogue',                 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => '/reactifs',                  'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/reactifs/abm',              'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/categories',                'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/actualites',                'priority' => '0.7', 'changefreq' => 'daily'],
            ['loc' => '/services-sav',              'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/pole-hemodialyse',          'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => '/qui-sommes-nous',           'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/contact',                   'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => '/mentions-legales',          'priority' => '0.2', 'changefreq' => 'yearly'],
            ['loc' => '/politique-confidentialite', 'priority' => '0.2', 'changefreq' => 'yearly'],
            ['loc' => '/conditions-vente',          'priority' => '0.2', 'changefreq' => 'yearly'],
        ];
        foreach ($static as $s) {
            $urls->push($s + ['lastmod' => now()->toDateString()]);
        }

        // ── Catégories ───────────────────────────────────────────────────────
        Category::where('is_active', true)->orderBy('slug')->each(function (Category $cat) use ($urls) {
            $urls->push([
                'loc'        => '/catalogue?categorie=' . $cat->slug,
                'lastmod'    => $cat->updated_at->toDateString(),
                'priority'   => '0.7',
                'changefreq' => 'weekly',
            ]);
        });

        // ── Produits ─────────────────────────────────────────────────────────
        Product::where('status', 'actif')->orderBy('slug')->each(function (Product $p) use ($urls) {
            $urls->push([
                'loc'        => '/produits/' . $p->slug,
                'lastmod'    => $p->updated_at->toDateString(),
                'priority'   => '0.8',
                'changefreq' => 'weekly',
            ]);
        });

        // ── Actualités ───────────────────────────────────────────────────────
        News::where('status', 'publié')->orderBy('slug')->each(function (News $n) use ($urls) {
            $urls->push([
                'loc'        => '/news/' . $n->slug,
                'lastmod'    => ($n->published_at ?? $n->updated_at)->toDateString(),
                'priority'   => '0.6',
                'changefreq' => 'monthly',
            ]);
        });

        $base = rtrim(config('app.url'), '/');
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . e($base . $url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type'  => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
