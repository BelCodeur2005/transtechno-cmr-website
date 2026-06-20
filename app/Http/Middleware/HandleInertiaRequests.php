<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            // Méga-menus pilotés par la BDD (catégories actives, en cache) — partagés à toutes les pages.
            'nav' => fn () => Category::navMenus(),
            // Informations de l'entreprise — gérées depuis Admin → Paramètres.
            'company' => fn () => [
                'phone_1'       => Setting::get('phone_1', '+237 674 64 53 54'),
                'phone_2'       => Setting::get('phone_2', '+237 699 67 40 36'),
                'phone_3'       => Setting::get('phone_3', '+237 233 37 33 62'),
                'whatsapp'      => Setting::get('whatsapp', '237699674036'),
                'email'         => Setting::get('email', 'office@transtechno-cmr.com'),
                'address_line1' => Setting::get('address_line1', 'Village carrefour, entrée bille'),
                'address_line2' => Setting::get('address_line2', 'Immeuble Pharmacie Sainte-Marie, 2ème niveau'),
                'address_city'  => Setting::get('address_city', 'Douala, Cameroun'),
                'facebook_url'  => Setting::get('facebook_url', ''),
                'linkedin_url'  => Setting::get('linkedin_url', ''),
                'company_name'  => Setting::get('company_name', 'Transtechno Cameroun'),
                'rccm'          => Setting::get('rccm', ''),
                'niu'           => Setting::get('niu', ''),
                'host_name'     => Setting::get('host_name', 'PlanetHoster Inc.'),
                'host_address'  => Setting::get('host_address', '4416 Louis-B.-Mayer, Laval, Québec, H7P 0G1, Canada'),
            ],
        ];
    }
}
