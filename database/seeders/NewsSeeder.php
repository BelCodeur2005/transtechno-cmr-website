<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Innovation Médicale' => 'innovation-medicale',
            'Partenariats' => 'partenariats',
            'Événements' => 'evenements',
            'Conseils Techniques' => 'conseils-techniques',
            'Communiqués' => 'communiques',
        ];

        $catModels = [];
        foreach ($categories as $name => $slug) {
            $catModels[$slug] = NewsCategory::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'is_active' => true],
            );
        }

        $tags = collect(['Hémodialyse', 'Laboratoire', 'BIOBASE', 'Formation', 'Minsanté', 'Imagerie'])
            ->mapWithKeys(fn ($t) => [$t => NewsTag::firstOrCreate(['slug' => Str::slug($t)], ['name' => $t])]);

        $author = User::first();

        $articles = [
            [
                'title' => 'Nouvel investissement dans la production locale de Dialysats pour les hôpitaux de Douala',
                'category' => 'innovation-medicale',
                'image' => '/images/hero-bg.jpg',
                'date' => '2026-05-15',
                'tags' => ['Hémodialyse'],
                'excerpt' => "Afin de répondre à la demande croissante et garantir une qualité optimale, Transtechno Cameroun annonce un investissement majeur dans une nouvelle unité de production locale, respectant les normes ISO les plus strictes.",
                'content' => "<p>Transtechno Cameroun franchit une étape clé dans le renforcement de l'autonomie sanitaire du pays avec le lancement d'une unité de production locale de dialysats.</p><h2>Une réponse à un besoin critique</h2><p>La demande en traitements d'hémodialyse ne cesse de croître. Cette nouvelle capacité de production permettra de réduire les délais d'approvisionnement et de garantir une qualité constante, conforme aux normes OMS.</p><ul><li>Production conforme aux normes ISO</li><li>Réduction des délais pour les hôpitaux de Douala</li><li>Accompagnement technique complet</li></ul>",
            ],
            [
                'title' => 'Déploiement réussi de nouveaux analyseurs BIOBASE',
                'category' => 'partenariats',
                'image' => '/images/about.jpg',
                'date' => '2026-05-10',
                'tags' => ['BIOBASE', 'Laboratoire'],
                'excerpt' => "Installation réussie et formation du personnel technique sur la nouvelle gamme d'analyseurs biochimiques dans deux cliniques partenaires.",
                'content' => "<p>Nos équipes ont finalisé l'installation de la dernière génération d'analyseurs biochimiques BIOBASE dans deux cliniques partenaires de la région du Littoral.</p><h2>Mise en service et formation</h2><p>Au-delà de l'installation, nos ingénieurs biomédicaux ont assuré une formation complète du personnel de laboratoire pour une exploitation optimale des équipements.</p>",
            ],
            [
                'title' => 'Focus produit : le photomètre HiPo MPP-96 par Biosan',
                'category' => 'conseils-techniques',
                'image' => '/images/qsn-hero.jpg',
                'date' => '2026-05-02',
                'tags' => ['Laboratoire'],
                'excerpt' => "Découvrez les caractéristiques techniques avancées du photomètre de microplaques HiPo MPP-96, idéal pour les laboratoires à haut rendement.",
                'content' => "<p>Le HiPo MPP-96 est un photomètre pour microplaques 96 puits conçu pour les analyses ELISA et microbiologie.</p><h2>Points forts</h2><ul><li>Plage de mesures : 0–4.3 OD, résolution 0.0001 DO</li><li>Source LED, 8 photodiodes silicone</li><li>Logiciel QuantAssay inclus</li></ul><p>Un outil de référence pour les laboratoires à haut rendement.</p>",
            ],
            [
                'title' => 'Séminaire de formation : maintenance préventive des équipements d\'imagerie',
                'category' => 'evenements',
                'image' => '/images/qsn-heritage.jpg',
                'date' => '2026-04-20',
                'tags' => ['Formation', 'Imagerie'],
                'excerpt' => "Retour sur notre dernière session de formation dédiée aux ingénieurs biomédicaux sur les protocoles de maintenance des équipements d'imagerie.",
                'content' => "<p>Transtechno Cameroun a réuni les ingénieurs biomédicaux de plusieurs structures de santé pour une session de formation dédiée à la maintenance préventive.</p><h2>Au programme</h2><p>Protocoles de contrôle, diagnostic à distance, et bonnes pratiques pour prolonger la durée de vie des équipements d'imagerie.</p>",
            ],
            [
                'title' => 'Transtechno Cameroun confirme son agrément Minsanté (Arrêté N° 0683)',
                'category' => 'communiques',
                'image' => '/images/legal-aside.jpg',
                'date' => '2026-04-05',
                'tags' => ['Minsanté'],
                'excerpt' => "Notre statut de distributeur agréé de dispositifs médicaux et d'équipements de laboratoire est confirmé, gage de conformité et de confiance.",
                'content' => "<p>Transtechno Cameroun confirme son agrément auprès du Ministère de la Santé Publique, sous l'arrêté N° 0683.</p><p>Cet agrément atteste de la conformité de nos opérations de distribution, d'installation et de maintenance aux normes en vigueur.</p>",
            ],
            [
                'title' => 'Partenariat renforcé avec GIMA pour l\'équipement du bloc opératoire',
                'category' => 'partenariats',
                'image' => '/images/about.jpg',
                'date' => '2026-03-28',
                'tags' => [],
                'excerpt' => "Une collaboration élargie pour proposer une gamme complète d'équipements de bloc opératoire aux établissements camerounais.",
                'content' => "<p>Transtechno Cameroun renforce son partenariat avec GIMA afin d'élargir son offre dédiée au bloc opératoire : tables d'opération, éclairage scialytique, appareils d'anesthésie et bistouris électriques.</p>",
            ],
        ];

        foreach ($articles as $a) {
            $news = News::updateOrCreate(
                ['slug' => Str::slug($a['title'])],
                [
                    'title' => $a['title'],
                    'excerpt' => $a['excerpt'],
                    'content' => $a['content'],
                    'cover_image_path' => $a['image'],
                    'author_id' => $author?->id,
                    'news_category_id' => $catModels[$a['category']]->id,
                    'status' => 'publié',
                    'published_at' => Carbon::parse($a['date']),
                ],
            );

            $tagIds = collect($a['tags'])->map(fn ($t) => $tags[$t]->id)->all();
            $news->tags()->sync($tagIds);
        }

        $this->command?->info('NewsSeeder : ' . count($articles) . ' articles, ' . count($categories) . ' catégories.');
    }
}
