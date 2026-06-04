<?php
namespace Database\Seeders;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'BIOBASE','website' => 'https://www.biobase.cn'],
            ['name' => 'HUGER','website' => null],
            ['name' => 'GIMA','website' => 'https://www.gimaonline.com'],
            ['name' => 'BIOSAN','website' => 'https://www.biosan.lv'],
            ['name' => 'Mindray','website' => 'https://www.mindray.com'],
            ['name' => 'ALL TEST','website' => null],
            ['name' => 'plusmed','website' => null],
            ['name' => 'AccuBioMed','website' => null],
            ['name' => 'LABEX','website' => null],
            ['name' => 'WNSCIENCE','website' => null],
            ['name' => 'ABM','website' => 'https://www.abmgood.com'],
        ];
        foreach ($brands as $i => $brand) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($brand['name'])],
                array_merge($brand, ['is_active' => true, 'order' => $i + 1])
            );
        }
    }
}