<?php
namespace Database\Seeders;
use App\Models\Pole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PoleSeeder extends Seeder
{
    public function run(): void
    {
        $poles = [
            ['name' => 'Monitoring et Soins Critiques','color' => '#004A80'],
            ['name' => 'Hematologie et Laboratoire','color' => '#006099'],
            ['name' => 'Imagerie Medicale','color' => '#3a6090'],
            ['name' => 'Hemodialyse et Traitement des Eaux','color' => '#ba0013'],
            ['name' => 'Reactifs et Biologie Moleculaire','color' => '#006099'],
        ];
        foreach ($poles as $pole) {
            Pole::firstOrCreate(['slug' => Str::slug($pole['name'])],array_merge($pole,['is_active' => true]));
        }
    }
}