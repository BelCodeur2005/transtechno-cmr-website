<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        // Source (images déjà vétées dans public/images) → destination réelle sur le disque storage.
        $items = [
            ['hero-bg.jpg', 'Microscope de laboratoire en gros plan', 'Laboratoire — microscopie'],
            ['about.jpg', 'Verrerie et matériel de laboratoire', 'Matériel de laboratoire'],
            ['qsn-hero.jpg', 'Rangée de microscopes professionnels', 'Équipement scientifique'],
            ['qsn-heritage.jpg', "Salle d'hospitalisation équipée", 'Mobilier hospitalier'],
            ['legal-aside.jpg', "Représentation d'une molécule d'ADN", 'Biologie moléculaire'],
        ];

        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        foreach ($items as [$file, $alt, $title]) {
            $source = public_path('images/' . $file);
            $dest = 'media/' . $file;

            // Copie le fichier dans storage/app/public/media s'il n'y est pas déjà.
            if (file_exists($source) && ! $disk->exists($dest)) {
                $disk->put($dest, file_get_contents($source));
            }

            Media::firstOrCreate(
                ['path' => $dest],
                ['filename' => $file, 'alt' => $alt, 'title' => $title, 'mime_type' => 'image/jpeg'],
            );
        }

        $this->command?->info('MediaSeeder : ' . count($items) . ' médias copiés dans le stockage.');
    }
}
