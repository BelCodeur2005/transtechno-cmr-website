<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    /**
     * Liste publique des actualités (filtrable par catégorie).
     */
    public function index(Request $request): Response
    {
        $categorySlug = $request->string('categorie')->toString() ?: null;

        $base = fn () => News::query()
            ->where('status', 'publié')
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->when($categorySlug, fn (Builder $q, $slug) => $q->whereHas('category', fn ($c) => $c->where('slug', $slug)));

        // Article à la une : le plus récent (uniquement sans filtre catégorie actif).
        $featured = (clone $base())
            ->with(['category:id,name,slug', 'author:id,name'])
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->first();

        $articles = $base()
            ->with(['category:id,name,slug'])
            ->when($featured && ! $categorySlug, fn (Builder $q) => $q->where('id', '!=', $featured->id))
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(6)
            ->withQueryString()
            ->through(fn (News $n) => $this->card($n));

        return Inertia::render('Public/Actualites', [
            'featured' => $featured ? $this->card($featured, withExcerpt: true) : null,
            'articles' => $articles,
            'categories' => NewsCategory::where('is_active', true)
                ->withCount(['news' => fn (Builder $q) => $q->where('status', 'publié')])
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
                ->map(fn (NewsCategory $c) => ['name' => $c->name, 'slug' => $c->slug, 'count' => $c->news_count]),
            'currentCategory' => $categorySlug,
        ]);
    }

    /**
     * Détail d'un article.
     */
    public function show(string $slug): Response
    {
        $article = News::query()
            ->where('slug', $slug)
            ->where('status', 'publié')
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->with(['category:id,name,slug', 'author:id,name', 'tags:id,name,slug'])
            ->firstOrFail();

        $related = News::query()
            ->where('status', 'publié')
            ->where('id', '!=', $article->id)
            ->when($article->news_category_id, fn (Builder $q) => $q->where('news_category_id', $article->news_category_id))
            ->orderByDesc('published_at')
            ->take(3)
            ->with('category:id,name,slug')
            ->get()
            ->map(fn (News $n) => $this->card($n));

        return Inertia::render('Public/Article', [
            'article' => [
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                // SEO : valeurs personnalisées ou repli sur titre / extrait.
                'meta_title' => $article->meta_title ?: $article->title,
                'meta_description' => $article->meta_description ?: $article->excerpt,
                'content' => $article->content,
                'image' => $article->coverUrl(),
                'category' => $article->category ? ['name' => $article->category->name, 'slug' => $article->category->slug] : null,
                'author' => $article->author?->name,
                'published_at' => $article->published_at?->translatedFormat('d F Y'),
                // Champs supplémentaires pour JSON-LD et canonical URL.
                'published_at_iso' => $article->published_at?->toIso8601String(),
                'updated_at_iso' => $article->updated_at->toIso8601String(),
                'url' => url('/news/' . $article->slug),
                'tags' => $article->tags->map(fn ($t) => ['name' => $t->name, 'slug' => $t->slug]),
            ],
            'related' => $related,
        ]);
    }

    /**
     * @return array<string,mixed>
     */
    private function card(News $n, bool $withExcerpt = true): array
    {
        return [
            'title' => $n->title,
            'slug' => $n->slug,
            'excerpt' => $withExcerpt ? $n->excerpt : null,
            'image' => $n->coverUrl(),
            'category' => $n->category?->name,
            'category_slug' => $n->category?->slug,
            'published_at' => $n->published_at?->translatedFormat('d M Y'),
        ];
    }
}
