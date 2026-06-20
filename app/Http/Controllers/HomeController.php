<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Pole;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $featured = Product::query()
            ->where('status', 'actif')
            ->where('is_featured', true)
            ->whereIn('product_type', ['equipement', 'consommable'])
            ->with(['brand:id,name', 'category:id,name,slug', 'coverImage:id,product_id,path'])
            ->orderBy('order')
            ->take(4)
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'reference' => $p->reference,
                'short_description' => $p->short_description,
                'category' => $p->category?->name,
                'stock_status' => $p->stock_status,
                'image' => $p->coverImage?->path ? url('/media-proxy/' . $p->coverImage->path) : null,
            ]);

        $partners = Brand::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->take(12)
            ->get(['name', 'slug', 'logo_path'])
            ->map(fn (Brand $b) => [
                'name' => $b->name,
                'logo' => $b->logo_path ? url('/media-proxy/' . $b->logo_path) : null,
            ]);

        $poles = Pole::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['name', 'slug', 'icon', 'description'])
            ->map(fn (Pole $p) => [
                'name' => $p->name,
                'slug' => $p->slug,
                'icon' => $p->icon,
                'description' => $p->description,
            ]);

        return Inertia::render('Public/Accueil', [
            'featured' => $featured,
            'partners' => $partners,
            'poles' => $poles,
        ]);
    }
}
