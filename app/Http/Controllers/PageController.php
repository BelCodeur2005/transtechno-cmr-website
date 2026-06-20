<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function about(): Response
    {
        $partners = Brand::where('is_active', true)
            ->orderBy('order')
            ->get(['name', 'logo_path'])
            ->map(fn (Brand $b) => [
                'name' => $b->name,
                'logo' => $b->logo_path ? url('/media-proxy/' . $b->logo_path) : null,
            ]);

        return Inertia::render('Public/QuiSommesNous', [
            'partners' => $partners,
        ]);
    }

    public function legal(): Response
    {
        return Inertia::render('Public/MentionsLegales');
    }

    public function privacy(): Response
    {
        return Inertia::render('Public/PolitiqueConfidentialite');
    }

    public function terms(): Response
    {
        return Inertia::render('Public/ConditionsVente');
    }
}
