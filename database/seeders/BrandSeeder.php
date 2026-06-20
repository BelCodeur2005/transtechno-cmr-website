<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        // Logos téléchargés dans public/partner-logos/ → copiés dans storage/app/public/brands/
        $logoSourceDir = public_path('partner-logos');

        $brands = [
            ['name' => 'BIOBASE',    'website' => 'https://www.biobaseglobal.com', 'logo_file' => 'biobase.png'],
            ['name' => 'HUGER',      'website' => 'https://www.huger.cn',          'logo_file' => 'huger.png'],
            ['name' => 'GIMA',       'website' => 'https://gimamedical.com',        'logo_file' => 'gima.png'],
            ['name' => 'BIOSAN',     'website' => 'https://biosan.lv',             'logo_file' => 'biosan.png'],
            ['name' => 'Mindray',    'website' => 'https://www.mindray.com',        'logo_file' => 'mindray.webp'],
            ['name' => 'ALL TEST',   'website' => 'https://www.alltests.com.cn',   'logo_file' => 'alltest.png'],
            ['name' => 'plusmed',    'website' => 'https://www.plusmed.com.cn',    'logo_file' => 'plusmed.jpg'],
            ['name' => 'AccuBioMed', 'website' => 'https://www.accubiomed.com',    'logo_file' => 'accubiomed.jpg'],
            ['name' => 'LABEX',      'website' => 'https://www.labex.co.za',       'logo_file' => 'labex.png'],
            ['name' => 'WNSCIENCE',  'website' => null,                            'logo_file' => null],
            ['name' => 'ABM',        'website' => 'https://www.abmgood.com',       'logo_file' => 'abm.png'],
        ];

        Storage::disk('public')->makeDirectory('brands');

        foreach ($brands as $i => $data) {
            $logoPath = null;

            if ($data['logo_file']) {
                $source = $logoSourceDir . DIRECTORY_SEPARATOR . $data['logo_file'];
                $dest   = 'brands/' . $data['logo_file'];

                if (file_exists($source) && ! Storage::disk('public')->exists($dest)) {
                    Storage::disk('public')->put($dest, file_get_contents($source));
                }

                if (Storage::disk('public')->exists($dest)) {
                    $logoPath = $dest;
                }
            }

            $brand = Brand::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'      => $data['name'],
                    'website'   => $data['website'],
                    'is_active' => true,
                    'order'     => $i + 1,
                ]
            );

            // Ne remplace le logo que si la marque n'en a pas encore (préserve les uploads manuels)
            if ($logoPath !== null && ! $brand->logo_path) {
                $brand->update(['logo_path' => $logoPath]);
            }
        }
    }
}
