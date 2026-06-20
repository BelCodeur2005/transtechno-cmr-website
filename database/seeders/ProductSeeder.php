<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Pole;
use App\Models\Product;
use App\Models\ProductSpec;
use App\Models\ReagentDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brands = Brand::pluck('id', 'slug');
        $categories = Category::pluck('id', 'slug');
        $poles = Pole::pluck('id', 'slug');

        // ── Équipements (brochure) ───────────────────────────────
        $equipment = [
            ['name' => 'Table chauffante de réanimation', 'brand' => 'biobase', 'category' => 'reanimation', 'pole' => 'monitoring-et-soins-critiques', 'price' => 4500000, 'featured' => true],
            ['name' => 'Système pour ORL complète', 'brand' => 'huger', 'category' => 'orl', 'pole' => 'imagerie-medicale', 'price' => null],
            ['name' => 'Table d\'opération', 'brand' => 'biobase', 'category' => 'bloc-operatoire', 'pole' => null, 'price' => 8200000, 'featured' => true],
            ['name' => 'Moniteur de patient', 'brand' => 'mindray', 'category' => 'monitoring', 'pole' => 'monitoring-et-soins-critiques', 'price' => 1850000, 'featured' => true],
            ['name' => 'Oximètre de pouls', 'brand' => 'gima', 'category' => 'monitoring', 'pole' => 'monitoring-et-soins-critiques', 'price' => 124000],
            ['name' => 'Microscope trinoculaire digital avec caméra', 'brand' => 'biobase', 'category' => 'laboratoire', 'pole' => 'hematologie-et-laboratoire', 'price' => 2300000],
            ['name' => 'Chambre froide', 'brand' => 'biobase', 'category' => 'equipements-hospitaliers', 'pole' => null, 'price' => null],
            ['name' => 'Appareil d\'Anesthésie', 'brand' => 'mindray', 'category' => 'bloc-operatoire', 'pole' => null, 'price' => 6700000],
            ['name' => 'Couveuse Néonatale', 'brand' => 'biobase', 'category' => 'reanimation', 'pole' => 'monitoring-et-soins-critiques', 'price' => 3900000],
            ['name' => 'Aspirateur de Mucosité électrique', 'brand' => 'gima', 'category' => 'urgence', 'pole' => null, 'price' => 285000],
            ['name' => 'Lit d\'hôpital de luxe électrique', 'brand' => 'biobase', 'category' => 'mobilier-medical', 'pole' => null, 'price' => 1450000],
            ['name' => 'Centrifugeuse', 'brand' => 'biobase', 'category' => 'laboratoire', 'pole' => 'hematologie-et-laboratoire', 'price' => 950000],
            ['name' => 'SMART CYCLER PCR', 'brand' => 'biobase', 'category' => 'biologie', 'pole' => 'hematologie-et-laboratoire', 'price' => null, 'featured' => true],
            ['name' => 'Fauteuil Orthopédique', 'brand' => 'plusmed', 'category' => 'orthopedie', 'pole' => null, 'price' => 780000],
            ['name' => 'Scialytique', 'brand' => 'biobase', 'category' => 'bloc-operatoire', 'pole' => null, 'price' => 2100000],
            ['name' => 'Électrocardiogramme', 'brand' => 'mindray', 'category' => 'cardiologie', 'pole' => 'monitoring-et-soins-critiques', 'price' => 1350000],
            ['name' => 'Banque de sang', 'brand' => 'biobase', 'category' => 'laboratoire', 'pole' => 'hematologie-et-laboratoire', 'price' => null],
            ['name' => 'Appareil d\'hématologie 19 paramètres', 'brand' => 'mindray', 'category' => 'laboratoire', 'pole' => 'hematologie-et-laboratoire', 'price' => 5400000],
            ['name' => 'Appareil d\'hématologie 27 paramètres', 'brand' => 'mindray', 'category' => 'laboratoire', 'pole' => 'hematologie-et-laboratoire', 'price' => 7200000],
            ['name' => 'Table d\'accouchement', 'brand' => 'biobase', 'category' => 'accouchement-et-maternite', 'pole' => null, 'price' => 1900000],
        ];

        foreach ($equipment as $i => $item) {
            $product = Product::firstOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'reference' => 'EQ-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                    'brand_id' => $brands[$item['brand']] ?? null,
                    'category_id' => $categories[$item['category']] ?? null,
                    'short_description' => $item['name'] . ' — équipement médical certifié OMS, importé par Transtechno Cameroun.',
                    'price_fcfa' => $item['price'],
                    'price_visible' => $item['price'] !== null,
                    'status' => 'actif',
                    'is_featured' => $item['featured'] ?? false,
                    'order' => $i + 1,
                    'product_type' => 'equipement',
                    'stock_status' => 'en_stock',
                ]
            );

            if (! empty($item['pole']) && isset($poles[$item['pole']])) {
                $product->poles()->syncWithoutDetaching([$poles[$item['pole']]]);
            }
        }

        // ── Produit vedette détaillé : HiPo MPP-96 (Biosan) ──────
        $hipo = Product::firstOrCreate(
            ['slug' => 'hipo-mpp-96-microplate-photometer'],
            [
                'name' => 'Photomètre pour microplaques HiPo MPP-96',
                'reference' => 'BS-050108-A02',
                'brand_id' => $brands['biosan'] ?? null,
                'category_id' => $categories['laboratoire'] ?? null,
                'short_description' => 'Photomètre pour microplaques 96 puits (ELISA + microbiologie). Plage 0–4.3 OD, résolution 0.0001 DO, source LED.',
                'long_description' => '<p>Le HiPo MPP-96 est un photomètre compact pour microplaques 96 puits, conçu pour les applications ELISA et microbiologie. Livré avec le logiciel QuantAssay.</p>',
                'price_fcfa' => null,
                'price_visible' => false,
                'status' => 'actif',
                'is_featured' => true,
                'order' => 50,
                'product_type' => 'equipement',
                'stock_status' => 'en_stock',
            ]
        );
        if (isset($poles['hematologie-et-laboratoire'])) {
            $hipo->poles()->syncWithoutDetaching([$poles['hematologie-et-laboratoire']]);
        }
        if ($hipo->specs()->count() === 0) {
            $specs = [
                ['Plage de mesures', '0–4.3 OD'],
                ['Résolution', '0.0001 DO'],
                ['Source lumineuse', 'LED'],
                ['Détecteur', '8 photodiodes silicium'],
                ['Longueurs d\'onde', '400–700 nm'],
                ['Standards', '405, 450, 492, 620 nm'],
                ['Vitesse lecture', '5–8 s/longueur d\'onde'],
                ['Dimensions', '140×300×130 mm'],
                ['Poids', '4.6 kg'],
                ['Logiciel', 'QuantAssay (inclus)'],
            ];
            foreach ($specs as $j => [$label, $value]) {
                ProductSpec::create([
                    'product_id' => $hipo->id,
                    'label' => $label,
                    'value' => $value,
                    'order' => $j + 1,
                ]);
            }
        }

        // ── Réactifs ABM ─────────────────────────────────────────
        $reagents = [
            ['name' => 'EvaGreen 2X qPCR MasterMix', 'category' => 'biologie-moleculaire', 'cond' => 'Kit 200 réactions', 'vol' => '1 mL', 'temp' => '-20°C', 'ivd' => false],
            ['name' => 'OneScript Plus cDNA Synthesis Kit', 'category' => 'biologie-moleculaire', 'cond' => 'Kit 50 réactions', 'vol' => '50 rxn', 'temp' => '-20°C', 'ivd' => false],
            ['name' => 'Milieu de culture LB Broth', 'category' => 'microbiologie', 'cond' => 'Flacon 500 g', 'vol' => '500 g', 'temp' => 'Ambiante', 'ivd' => false],
            ['name' => 'Kit d\'extraction ADN génomique', 'category' => 'genomique-et-sequencage', 'cond' => 'Kit 100 préparations', 'vol' => '100 prep', 'temp' => '+2-8°C', 'ivd' => true],
            ['name' => 'lentiCRISPR sgRNA Library', 'category' => 'crispr-et-edition-genique', 'cond' => 'Kit', 'vol' => '20 µg', 'temp' => '-80°C', 'ivd' => false],
        ];

        foreach ($reagents as $i => $item) {
            $product = Product::firstOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'reference' => 'ABM-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                    'brand_id' => $brands['abm'] ?? null,
                    'category_id' => $categories[$item['category']] ?? null,
                    'short_description' => $item['name'] . ' — réactif ABM (Applied Biological Materials).',
                    'price_fcfa' => null,
                    'price_visible' => false,
                    'status' => 'actif',
                    'is_featured' => false,
                    'order' => $i + 1,
                    'product_type' => 'reactif',
                    'stock_status' => 'en_stock',
                ]
            );

            if (isset($poles['reactifs-et-biologie-moleculaire'])) {
                $product->poles()->syncWithoutDetaching([$poles['reactifs-et-biologie-moleculaire']]);
            }

            ReagentDetail::firstOrCreate(
                ['product_id' => $product->id],
                [
                    'storage_temp' => $item['temp'],
                    'conditionnement' => $item['cond'],
                    'volume_quantity' => $item['vol'],
                    'is_ivd' => $item['ivd'],
                    'lot_number_example' => 'LOT' . rand(100000, 999999),
                    'expiry_note' => '12 mois à compter de la date de production',
                ]
            );
        }
    }
}
