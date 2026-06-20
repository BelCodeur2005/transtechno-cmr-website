<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Pole;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogueController extends Controller
{
    /**
     * Grille publique des catégories d'équipements.
     */
    public function categories(): Response
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->where('type', 'equipement')
            ->whereNull('parent_id')
            ->withCount(['products' => fn ($q) => $q->where('status', 'actif')->where('product_type', 'equipement')])
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon'])
            ->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'icon' => $c->icon ?: 'category',
                'products_count' => $c->products_count,
            ]);

        return Inertia::render('Public/Categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Fiche produit publique. Template différencié équipement / réactif (cf. CLAUDE.md).
     */
    public function show(string $slug): Response
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'actif')
            ->with(['brand:id,name,slug', 'category:id,name,slug', 'poles:id,name,slug', 'specs', 'media', 'reagentDetail',
                'relatedProducts' => fn ($q) => $q->where('status', 'actif')->with('coverImage:id,product_id,path'),
            ])
            ->firstOrFail();

        $images = $product->media
            ->where('type', 'image')
            ->sortBy('order')
            ->map(fn ($m) => url('/media-proxy/' . $m->path))
            ->values();

        $pdf = $product->media->firstWhere('type', 'pdf');

        // Priorité 1 : produits liés manuellement dans l'admin.
        $related = $product->relatedProducts->take(3);

        // Priorité 2 : même catégorie (si la curation manuelle donne moins de 3).
        if ($related->count() < 3 && $product->category_id) {
            $existingIds = $related->pluck('id')->push($product->id);
            $fill = Product::query()
                ->where('status', 'actif')
                ->whereNotIn('id', $existingIds)
                ->where('category_id', $product->category_id)
                ->with('coverImage:id,product_id,path')
                ->orderByDesc('is_featured')
                ->take(3 - $related->count())
                ->get();
            $related = $related->merge($fill);
        }

        // Fallback final : produits vedettes toutes catégories confondues.
        if ($related->count() < 3) {
            $existingIds = $related->pluck('id')->push($product->id);
            $fill = Product::query()
                ->where('status', 'actif')
                ->whereNotIn('id', $existingIds)
                ->where('is_featured', true)
                ->with('coverImage:id,product_id,path')
                ->orderBy('order')
                ->take(3 - $related->count())
                ->get();
            $related = $related->merge($fill);
        }

        $related = $related->map(fn (Product $p) => [
            'name' => $p->name,
            'slug' => $p->slug,
            'short_description' => $p->short_description,
            'image' => $p->coverImage?->path ? url('/media-proxy/' . $p->coverImage->path) : null,
        ]);

        return Inertia::render('Public/Produit', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'reference' => $product->reference,
                'short_description' => $product->short_description,
                'long_description' => $product->long_description,
                'brand' => $product->brand?->name,
                'category' => $product->category ? ['name' => $product->category->name, 'slug' => $product->category->slug] : null,
                'poles' => $product->poles->pluck('name'),
                'product_type' => $product->product_type,
                'stock_status' => $product->stock_status,
                'price_fcfa' => $product->price_visible ? $product->price_fcfa : null,
                'url' => url('/produits/' . $product->slug),
                'images' => $images,
                'pdf_url' => $pdf ? url('/media-proxy/' . $pdf->path) : null,
                'specs' => $product->specs->map(fn ($s) => ['label' => $s->label, 'value' => $s->value])->values(),
                'reagent' => $product->reagentDetail ? [
                    'storage_temp' => $product->reagentDetail->storage_temp,
                    'conditionnement' => $product->reagentDetail->conditionnement,
                    'volume_quantity' => $product->reagentDetail->volume_quantity,
                    'is_ivd' => (bool) $product->reagentDetail->is_ivd,
                    'lot_number_example' => $product->reagentDetail->lot_number_example,
                    'expiry_note' => $product->reagentDetail->expiry_note,
                ] : null,
            ],
            'related' => $related,
        ]);
    }

    /**
     * Suggestions d'autocomplétion (header) — recherche plein-texte Scout, JSON léger.
     */
    public function suggest(Request $request): \Illuminate\Http\JsonResponse
    {
        $term = trim($request->string('q')->toString());

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $results = Product::search($term)
            ->where('status', 'actif')
            ->take(6)
            ->get()
            ->map(fn (Product $p) => [
                'name' => $p->name,
                'slug' => $p->slug,
                'reference' => $p->reference,
                'product_type' => $p->product_type,
            ])
            ->values();

        return response()->json($results);
    }

    /**
     * Catalogue produits avec filtres (type, catégorie, recherche, stock, pôles, marques, tri).
     */
    public function catalogue(Request $request, string $scope = 'produits'): Response
    {
        // Deux hiérarchies séparées : "produits" (équipements/consommables) vs "reactifs" (ABM).
        $isReagentScope = $scope === 'reactifs';
        $scopeTypes = $isReagentScope ? ['reactif'] : ['equipement', 'consommable'];

        $filters = [
            // Le filtre "type" n'est proposé que dans le périmètre Produits (équipement/consommable).
            'type' => (! $isReagentScope && in_array($request->input('type'), ['equipement', 'consommable'], true))
                ? $request->input('type')
                : null,
            'categorie' => $request->string('categorie')->toString() ?: null,
            'q' => $request->string('q')->toString() ?: null,
            'en_stock' => $request->boolean('en_stock'),
            'poles' => array_filter((array) $request->input('poles', [])),
            'marques' => array_filter((array) $request->input('marques', [])),
            'tri' => in_array($request->input('tri'), ['az', 'za', 'nouveautes'], true)
                ? $request->input('tri')
                : 'pertinence',
        ];

        // Recherche plein-texte via Scout (MySQL Full-Text) : on récupère les IDs pertinents,
        // puis on les croise avec les filtres/facettes du catalogue.
        $searchIds = $filters['q']
            ? Product::search($filters['q'])->take(300)->keys()->all()
            : null;

        // Base commune (scope + type + catégorie + recherche + stock) — sert aussi aux compteurs facettés.
        $base = fn () => Product::query()
            ->where('status', 'actif')
            ->whereIn('product_type', $scopeTypes)
            ->when($filters['type'], fn ($q, $t) => $q->where('product_type', $t))
            ->when($filters['categorie'], fn ($q, $slug) => $q->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->when($searchIds !== null, fn ($q) => $q->whereIn('id', $searchIds))
            ->when($filters['en_stock'], fn ($q) => $q->where('stock_status', 'en_stock'));

        $products = $base()
            ->with(['brand:id,name,slug', 'coverImage:id,product_id,path'])
            ->when($filters['poles'], fn ($q, $slugs) => $q->whereHas('poles', fn ($p) => $p->whereIn('slug', $slugs)))
            ->when($filters['marques'], fn ($q, $slugs) => $q->whereHas('brand', fn ($b) => $b->whereIn('slug', $slugs)))
            ->when($filters['tri'] === 'az', fn ($q) => $q->orderBy('name'))
            ->when($filters['tri'] === 'za', fn ($q) => $q->orderByDesc('name'))
            ->when($filters['tri'] === 'nouveautes', fn ($q) => $q->orderByDesc('created_at'))
            ->when($filters['tri'] === 'pertinence', fn ($q) => $q->orderByDesc('is_featured')->orderBy('order'))
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'reference' => $p->reference,
                'short_description' => $p->short_description,
                'brand' => $p->brand?->name,
                'image' => $p->coverImage?->path ? url('/media-proxy/' . $p->coverImage->path) : null,
                'stock_status' => $p->stock_status,
                'is_featured' => (bool) $p->is_featured,
                'product_type' => $p->product_type,
            ]);

        // Compteurs facettés : pôles comptés avec le filtre marques actif (et vice-versa).
        $poles = Pole::where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug'])
            ->map(fn (Pole $pole) => [
                'name' => $pole->name,
                'slug' => $pole->slug,
                'count' => $base()
                    ->when($filters['marques'], fn ($q, $slugs) => $q->whereHas('brand', fn ($b) => $b->whereIn('slug', $slugs)))
                    ->whereHas('poles', fn ($p) => $p->where('poles.id', $pole->id))
                    ->count(),
            ])
            ->values();

        $brands = Brand::where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug'])
            ->map(fn (Brand $brand) => [
                'name' => $brand->name,
                'slug' => $brand->slug,
                'count' => $base()
                    ->when($filters['poles'], fn ($q, $slugs) => $q->whereHas('poles', fn ($p) => $p->whereIn('slug', $slugs)))
                    ->where('brand_id', $brand->id)
                    ->count(),
            ])
            ->filter(fn ($b) => $b['count'] > 0 || in_array($b['slug'], $filters['marques'], true))
            ->values();

        // Compteurs par type (uniquement périmètre Produits : équipement/consommable).
        $typeBase = fn () => Product::query()
            ->where('status', 'actif')
            ->whereIn('product_type', $scopeTypes)
            ->when($filters['categorie'], fn ($q, $slug) => $q->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->when($searchIds !== null, fn ($q) => $q->whereIn('id', $searchIds))
            ->when($filters['en_stock'], fn ($q) => $q->where('stock_status', 'en_stock'));

        $types = $isReagentScope
            ? collect()
            : collect([
                ['value' => 'equipement', 'label' => 'Équipements'],
                ['value' => 'consommable', 'label' => 'Consommables'],
            ])->map(fn ($t) => [
                ...$t,
                'count' => (clone $typeBase())->where('product_type', $t['value'])->count(),
            ])->filter(fn ($t) => $t['count'] > 0 || $filters['type'] === $t['value'])->values();

        return Inertia::render('Public/Catalogue', [
            'products' => $products,
            'filters' => $filters,
            'scope' => $scope,
            'types' => $types,
            'poles' => $poles,
            'brands' => $brands,
            'categories' => Category::where('is_active', true)
                ->where('type', $isReagentScope ? 'reactif' : 'equipement')
                ->orderBy('name')
                ->get(['name', 'slug']),
            'currentCategory' => $filters['categorie']
                ? Category::where('slug', $filters['categorie'])->first(['name', 'description', 'icon'])
                : null,
        ]);
    }
}
