# Transtechno Cameroun — Site Web

Site B2B de vente d'équipements médicaux, scientifiques et de laboratoire pour Transtechno Cameroun, fournisseur basé à Douala depuis 2016, agréé par le ministère de la Santé publique.

Projet client réalisé de la conception au déploiement : site public avec catalogue et demandes de devis, back-office complet pour l'équipe commerciale, le SAV et la rédaction.

## Stack technique

| Couche | Technologie |
|---|---|
| Framework | Laravel 12 |
| Frontend | Inertia.js v2 + React (TypeScript) + Vite |
| Admin | Filament PHP v3 |
| Base de données | MySQL |
| Stockage fichiers | N0C Storage (S3-compatible, inclus PlanetHoster) |
| Cache / Queue | Redis (instance dédiée PlanetHoster) |
| Recherche | MySQL Full-Text Search via Laravel Scout |
| PDF | barryvdh/laravel-dompdf |
| Email | SMTP PlanetHoster |
| Hébergement | PlanetHoster — Plan "The World" (cPanel, SSH) |

## Prérequis

- PHP 8.2+
- Composer 2+
- Node.js 22+ / npm 11+
- MySQL 8+

## Installation locale

```bash
# 1. Dépendances PHP
composer install

# 2. Variables d'environnement
cp .env.example .env
php artisan key:generate

# 3. Base de données
# Configurer DB_* dans .env puis :
php artisan migrate --seed

# 4. Lien de stockage public
php artisan storage:link

# 5. Dépendances JS + build
npm install
npm run dev
```

## Seeds utiles

```bash
# Données de démo complètes (rôles, utilisateur admin, partenaires, catégories)
php artisan db:seed

# Logos des partenaires seulement
php artisan db:seed --class=BrandSeeder

# Permissions Filament Shield (à relancer après ajout de ressources)
php artisan shield:generate --all
```

## Compte administrateur (local)

Le seeder crée un compte super-administrateur hors production. Son mot de passe est lu dans `.env` et le seeder s'arrête s'il n'est pas défini :

```env
SEED_ADMIN_PASSWORD=un_mot_de_passe_solide
```

Le back-office est accessible sur `http://localhost/admin`.

## Structure des dossiers clés

```
app/
  Filament/Resources/   — Ressources admin (CRUD Filament)
  Http/Controllers/     — Contrôleurs publics (Inertia)
  Models/               — Modèles Eloquent

resources/
  js/
    Pages/Public/       — Pages publiques React (Inertia)
    Pages/Admin/        — Pages admin React si besoin
    Layouts/            — PublicLayout, AuthLayout
  views/
    livewire/           — Composants Livewire (ex. médiathèque)

database/
  migrations/           — Schéma de la base
  seeders/              — Données initiales

public/
  partner-logos/        — Logos partenaires (source pour BrandSeeder)
  images/               — Logo, favicon, images statiques

docs/
  guide-admin.md        — Guide complet d'utilisation du back-office
  roles-permissions.md  — Documentation des rôles et permissions
```

## Variables d'environnement importantes

```env
# Application
APP_URL=https://transtechno.cm

# Base de données
DB_CONNECTION=mysql
DB_DATABASE=transtechno

# Stockage S3 (N0C Storage PlanetHoster)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_ENDPOINT=
AWS_URL=

# Redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=
REDIS_PASSWORD=
REDIS_PORT=6379

# Email SMTP
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=contact@transtechno.cm
MAIL_FROM_NAME="Transtechno Cameroun"
```

## Déploiement sur PlanetHoster

```bash
# Sur le serveur via SSH

# 1. Copier les fichiers (sans node_modules/ ni vendor/)
# 2. Installer les dépendances PHP en production
composer install --no-dev --optimize-autoloader

# 3. Build des assets JS (à faire en local, puis uploader public/build/)
npm run build

# 4. Migrations + optimisation
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Permissions de stockage
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

**Cron cPanel** — ajouter cette tâche (toutes les minutes) :
```
* * * * * php /home/USER/public_html/artisan schedule:run >> /dev/null 2>&1
* * * * * php /home/USER/public_html/artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

## Modules fonctionnels

- **Catalogue produits** — équipements médicaux + réactifs ABM, 33 catégories, filtres
- **Fiche produit** — galerie, spécifications techniques, demande de devis
- **Système de devis** — panier → formulaire → récapitulatif → traitement admin
- **Actualités** — articles avec éditeur riche, catégories, tags, SEO complet
- **Services & SAV** — pôle hémodialyse, tickets d'intervention
- **Back-office Filament** — gestion cotations, catalogue, actualités, médiathèque, rôles
- **Sitemap XML** — `/sitemap.xml` (articles, produits, catégories, pages statiques)

## Documentation

- [Guide d'utilisation du back-office](docs/guide-admin.md)
- [Rôles et permissions](docs/roles-permissions.md)
- [Déploiement](DEPLOY.md) et [modules](MODULES.md)
