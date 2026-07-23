# Guide de déploiement — Transtechno Cameroun
## PlanetHoster "The World" · cPanel · SSH

> Hébergement partagé mutualisé. Pas de démon persistent, mais Redis et SSH inclus.

---

## Table des matières

1. [Pré-requis & outils](#1-pré-requis--outils)
2. [Préparer le build en local](#2-préparer-le-build-en-local)
3. [Créer la base de données MySQL](#3-créer-la-base-de-données-mysql)
4. [Activer Redis](#4-activer-redis)
5. [Configurer le bucket N0C Storage (S3)](#5-configurer-le-bucket-n0c-storage-s3)
6. [Uploader les fichiers sur le serveur](#6-uploader-les-fichiers-sur-le-serveur)
7. [Configurer le fichier `.env`](#7-configurer-le-fichier-env)
8. [Finaliser l'installation côté serveur](#8-finaliser-linstallation-côté-serveur)
9. [Pointer le domaine vers `/public`](#9-pointer-le-domaine-vers-public)
10. [Cron jobs cPanel](#10-cron-jobs-cpanel)
11. [Email SMTP PlanetHoster](#11-email-smtp-planethoster)
12. [SSL Let's Encrypt](#12-ssl-lets-encrypt)
13. [Création du premier admin Filament](#13-création-du-premier-admin-filament)
14. [Checklist de vérification finale](#14-checklist-de-vérification-finale)
15. [Déploiements suivants (mise à jour)](#15-déploiements-suivants-mise-à-jour)
16. [Dépannage](#16-dépannage)

---

## 1. Pré-requis & outils

| Outil | Où l'obtenir |
|---|---|
| FileZilla (FTP/SFTP) | filezilla-project.org |
| PuTTY ou terminal SSH | Inclus sur macOS/Linux ; PuTTY sur Windows |
| Node.js 20+ | nodejs.org |
| Composer 2 | getcomposer.org |
| Accès cPanel PlanetHoster | Email de bienvenue PlanetHoster |

Identifiants SSH dans cPanel → **SSH/Shell Access** → générer/télécharger la clé.

---

## 2. Préparer le build en local

Le serveur mutualisé n'exécute pas Vite. Le build se fait **en local**, et les fichiers compilés sont uploadés.

### 2.1 Compiler les assets React/TypeScript

```bash
npm install
npm run build
```

Résultat : `public/build/` (fichiers JS/CSS compilés + `manifest.json`).

### 2.2 Retirer `public/build` du `.gitignore`

Par défaut Laravel ignore ce dossier. Pour le déploiement via Git :

```bash
# Dans .gitignore, commenter ou supprimer cette ligne :
# /public/build
```

> Si vous uploadez manuellement (FTP/SFTP), cette étape est inutile — uploadez simplement le dossier `public/build/`.

### 2.3 Optimiser Composer pour la production

```bash
composer install --no-dev --optimize-autoloader
```

Cela exclut les dépendances de test et génère un autoloader optimisé.

---

## 3. Créer la base de données MySQL

1. **cPanel → Bases de données MySQL**
2. Créer une base : `transtechno_prod` (le préfixe cPanel est automatiquement ajouté, ex. `u12345_transtechno_prod`)
3. Créer un utilisateur MySQL avec un mot de passe fort
4. **Ajouter l'utilisateur à la base** → cocher "Tous les privilèges"
5. Notez : `DB_HOST=localhost`, `DB_PORT=3306`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

---

## 4. Activer Redis

Dans l'espace client PlanetHoster :

1. **Espace client → Performance → Redis** (ou dans cPanel → Redis)
2. Activer l'instance Redis dédiée pour votre hébergement
3. Récupérer les informations de connexion :
   - Host : `127.0.0.1` (ou `localhost`)
   - Port : `6379` (par défaut)
   - Password : affiché dans le panneau Redis

> Redis est inclus dans le plan The World — pas de surcoût.

---

## 5. Configurer le bucket N0C Storage (S3)

### 5.1 Créer le bucket

1. **cPanel → N0C Storage** (ou espace client PlanetHoster → Stockage)
2. Créer un nouveau bucket, ex. `transtechno-media`
3. Générer une **Access Key** et **Secret Key** depuis le gestionnaire de clés
4. Notez l'**endpoint** S3 affiché (format : `https://s3.n0c.com` ou similaire)

### 5.2 Valeurs `.env` correspondantes

```dotenv
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=votre_access_key_n0c
AWS_SECRET_ACCESS_KEY=votre_secret_key_n0c
AWS_DEFAULT_REGION=us-east-1        # ou la région affichée dans le panneau
AWS_BUCKET=transtechno-media
AWS_ENDPOINT=https://s3.n0c.com     # endpoint exact affiché dans cPanel
AWS_USE_PATH_STYLE_ENDPOINT=true    # obligatoire pour les S3 non-AWS
```

> **URL personnalisée** : si vous souhaitez servir les fichiers depuis `media.transtechno.cm`, configurez le CNAME dans cPanel → Zone DNS puis ajoutez `AWS_URL=https://media.transtechno.cm` dans `.env`.

---

## 6. Structure de déploiement sur le serveur

### Architecture réelle utilisée (séparation sécurisée)

Laravel est déployé **en dehors de `public_html`** pour que les fichiers sensibles (`.env`, `app/`, `database/`, etc.) ne soient jamais accessibles depuis le web.

```
/home/username/
├── laravel/              ← Tout le code Laravel (hors /public)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
│
└── public_html/          ← Document root du domaine (accessible depuis le web)
    ├── index.php         ← Modifié pour pointer vers ~/laravel
    ├── .htaccess         ← Règles Laravel + redirection HTTPS
    ├── favicon.ico
    ├── images/           ← Logo, images statiques
    └── build/            ← Assets Vite compilés (npm run build)
```

### Pourquoi cette structure ?

Le dossier `public_html` est le seul accessible depuis Internet. En mettant Laravel dans `~/laravel/`, le `.env` (mots de passe, clés API) et tout le code source sont protégés — même si la configuration Apache faillit.

### Le `public_html/index.php` modifié

Le fichier `index.php` standard de Laravel pointe vers `vendor/` et `bootstrap/` relatifs. On le remplace par :

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Maintenance mode
if (file_exists($maintenance = __DIR__.'/../laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../laravel/vendor/autoload.php';

$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

### Uploader les fichiers

**Via SFTP (FileZilla) :**
1. Host : `transtechno.cm` | Protocole : SFTP | Port : `22`
2. Uploader tout le projet **sauf** `public/` dans `~/laravel/`
3. Uploader le contenu de `public/` dans `~/public_html/`
4. Remplacer `~/public_html/index.php` par la version modifiée ci-dessus

**Via SSH + Git :**
```bash
# Cloner dans ~/laravel
cd ~ && git clone https://github.com/BelCodeur2005/transtechno-cmr-website.git laravel

# Copier les fichiers public dans public_html
cp -r ~/laravel/public/* ~/public_html/

# Remplacer index.php par la version modifiée (voir ci-dessus)
```

### Fichiers à **ne pas uploader**

| Dossier/fichier | Raison |
|---|---|
| `node_modules/` | Généré en local, inutile en prod |
| `.env` | Créé manuellement sur le serveur (§7) |
| `storage/logs/*.log` | Logs locaux |
| `public/hot` | Fichier Vite dev server |

---

## 7. Configurer le fichier `.env`

Via SSH ou l'éditeur de fichiers cPanel, créer `/home/u12345/laravel/.env` :

```dotenv
APP_NAME="Transtechno Cameroun"
APP_ENV=production
APP_KEY=                            # généré à l'étape 8
APP_DEBUG=false
APP_URL=https://transtechno.cm

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

LOG_CHANNEL=daily
LOG_LEVEL=error

# ── Base de données ────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u12345_transtechno_prod  # votre nom exact
DB_USERNAME=u12345_transtechno       # votre user exact
DB_PASSWORD=VotreMotDePasseMySQL

# ── Session & Cache ────────────────────────────────
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_STORE=redis

# ── Queue ──────────────────────────────────────────
QUEUE_CONNECTION=redis

# ── Redis ──────────────────────────────────────────
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=VotreMotDePasseRedis
REDIS_PORT=6379

# ── Stockage N0C (S3-compatible) ───────────────────
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=votre_access_key_n0c
AWS_SECRET_ACCESS_KEY=votre_secret_key_n0c
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=transtechno-media
AWS_ENDPOINT=https://s3.n0c.com
AWS_USE_PATH_STYLE_ENDPOINT=true

# ── Email SMTP PlanetHoster ────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=mail.transtechno.cm       # ou smtp.planethoster.net
MAIL_PORT=587
MAIL_USERNAME=noreply@transtechno.cm
MAIL_PASSWORD=VotreMotDePasseEmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@transtechno.cm
MAIL_FROM_NAME="Transtechno Cameroun"

# ── Scout (recherche Full-Text MySQL) ─────────────
SCOUT_DRIVER=database

# ── Premier admin (utilisé par le seeder) ─────────
SEED_ADMIN_PASSWORD=MotDePasseAdmin2024!
```

---

## 8. Finaliser l'installation côté serveur

Toutes les commandes suivantes s'exécutent **via SSH** dans `/home/u12345/app/` :

```bash
cd ~/laravel
```

### 8.1 Vérifier la version PHP

```bash
php -v   # doit afficher PHP 8.2.x ou 8.3.x
```

Si la version est incorrecte, changer dans cPanel → **MultiPHP Manager**.

### 8.2 Vérifier les extensions PHP requises

```bash
php -m | grep -E "pdo|pdo_mysql|redis|gd|mbstring|openssl|tokenizer|xml|curl|fileinfo|bcmath|zip"
```

Extensions requises : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `curl`, `fileinfo`, `bcmath`, `gd`.

### 8.3 Générer la clé d'application

```bash
php artisan key:generate
```

### 8.4 Installer les dépendances PHP

**Composer n'est pas installé globalement sur PlanetHoster.** Il faut le télécharger manuellement :

```bash
php -r "copy('https://getcomposer.org/installer', '/tmp/cs.php');" \
  && php /tmp/cs.php --install-dir=/home/drsnkwraku --filename=composer.phar \
  && rm /tmp/cs.php
```

Ensuite installer les dépendances :

```bash
cd ~/laravel && php ~/composer.phar install --no-dev --optimize-autoloader
```

> **Important :** Ne jamais générer le `composer.lock` en local avec une version PHP différente de celle du serveur (PHP 8.2.27). Cela entraîne des conflits de versions lors du `composer install` sur le serveur. Toujours laisser composer résoudre les versions directement sur le serveur.

> **Important :** Ne jamais uploader le dossier `vendor/composer/` depuis Windows vers le serveur Linux — les chemins absolus dans les fichiers autoload sont incompatibles entre OS. Toujours régénérer l'autoload via `composer install` sur le serveur.

### 8.5 Créer le lien symbolique du storage

```bash
php artisan storage:link
```

### 8.6 Exécuter les migrations

```bash
php artisan migrate --force
```

> `--force` est obligatoire en environnement `production`.

### 8.7 (Optionnel) Exécuter les seeders

```bash
php artisan db:seed --force
```

### 8.8 Mettre en cache la configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache       # Filament — icônes
```

### 8.9 Optimiser Filament

```bash
php artisan filament:cache-components
php artisan icons:cache
```

---

## 9. Pointer le domaine vers `public_html`

Avec la structure séparée (§6), `public_html` **est déjà** le document root par défaut sur PlanetHoster. Aucun changement nécessaire dans cPanel.

Le `public_html/index.php` modifié fait le lien vers `~/laravel/` — c'est lui qui joue le rôle du `public/index.php` de Laravel.

### Vérifier le `.htaccess`

Le fichier `public/.htaccess` Laravel standard gère les rewrites. Vérifier qu'`AllowOverride All` est activé (PlanetHoster l'active par défaut).

Si les URLs retournent 404 :

```apache
# Ajouter dans public/.htaccess si absent :
Options -MultiViews -Indexes
RewriteEngine On
RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

---

## 10. Cron jobs cPanel

### 10.1 Queue Worker (traite les emails en arrière-plan)

**cPanel → Cron Jobs** → Ajouter :

```
* * * * *   /usr/local/bin/php /home/u12345/laravel/artisan queue:work --stop-when-empty --tries=3 --timeout=60 >> /dev/null 2>&1
```

> `--stop-when-empty` : le worker s'arrête quand la file est vide, ce qui est compatible avec le déclenchement par cron (pas de démon persistent).

### 10.2 Task Scheduler Laravel

```
* * * * *   /usr/local/bin/php /home/u12345/laravel/artisan schedule:run >> /dev/null 2>&1
```

### Vérifier le chemin PHP

```bash
which php   # ex : /usr/local/bin/php ou /usr/bin/php
```

Utiliser ce chemin exact dans les cron jobs.

---

## 11. Email SMTP PlanetHoster

### Créer le compte email

1. **cPanel → Comptes email** → Créer `noreply@transtechno.cm`
2. Paramètres SMTP :
   - Host : `mail.transtechno.cm`
   - Port entrant : 993 (IMAP SSL) / Port sortant : **587** (STARTTLS) ou 465 (SSL)
   - Username : `noreply@transtechno.cm`
   - Password : le mot de passe créé

### Tester depuis Laravel

```bash
php artisan tinker --execute="Mail::raw('Test', fn(\$m) => \$m->to('votre@email.com')->subject('Test'));"
```

Vérifier dans `storage/logs/laravel.log` si `MAIL_MAILER=log` ou dans la boîte mail si SMTP configuré.

---

## 12. SSL Let's Encrypt

1. **cPanel → SSL/TLS** → **Let's Encrypt SSL**
2. Sélectionner `transtechno.cm` et `www.transtechno.cm`
3. Émettre le certificat (gratuit, auto-renouvelé)
4. Vérifier la redirection HTTP → HTTPS dans `public/.htaccess` :

```apache
# Redirection HTTPS (ajouter avant les règles Laravel)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 13. Création du premier admin Filament

### Via seeder (si configuré)

```bash
php artisan db:seed --class=AdminUserSeeder --force
```

### Manuellement via tinker

```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name'     => 'M. Kamga',
    'email'    => 'admin@transtechno.cm',
    'password' => bcrypt('VotreMotDePasse!'),
]);
$user->assignRole('super_admin');
```

### Générer les permissions Filament Shield

```bash
php artisan shield:generate --all
php artisan shield:super-admin --user=admin@transtechno.cm
```

Accès back-office : `https://transtechno.cm/admin`

---

## 14. Checklist de vérification finale

```
[ ] Page d'accueil charge sans erreur
[ ] Les images du catalogue s'affichent (N0C Storage)
[ ] Formulaire de contact → email reçu dans la boîte
[ ] Formulaire de cotation → ticket créé en BDD, email reçu
[ ] Formulaire SAV → ticket SAV créé, email reçu
[ ] Accès admin /admin → login Filament OK
[ ] Dashboard affiche les KPIs
[ ] Création d'un produit depuis Filament → visible en catalogue
[ ] Upload d'une image → stockée dans N0C, URL accessible
[ ] Logs propres → storage/logs/laravel-YYYY-MM-DD.log
[ ] HTTPS actif, HTTP redirige
[ ] Cron queue worker → emails envoyés correctement
```

---

## 15. Déploiements suivants (mise à jour)

Pour chaque mise à jour du code :

```bash
# 1. En local : compiler les assets
npm run build

# 2. Uploader les fichiers modifiés via FileZilla vers ~/laravel/
#    et les assets compilés vers ~/public_html/build/
#    NE PAS uploader vendor/ ni vendor/composer/ (chemins Windows incompatibles)

# 3. Si composer.json ou composer.lock ont changé :
cd ~/laravel && php ~/composer.phar install --no-dev --optimize-autoloader

# 4. Exécuter les nouvelles migrations
php artisan migrate --force

# 5. Vider et reconstruire les caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components
php artisan icons:cache
```

> `php ~/composer.phar` — le composer.phar est dans `/home/drsnkwraku/composer.phar`.

> Lors d'une mise à jour avec migration critique, activer brièvement le mode maintenance :
> ```bash
> php artisan down --retry=60 --secret=mon-secret-bypass
> # ... exécuter les migrations ...
> php artisan up
> ```

---

## 16. Dépannage

### 500 Internal Server Error

```bash
# Afficher les erreurs PHP temporairement
APP_DEBUG=true php artisan serve

# Vérifier les logs
tail -100 storage/logs/laravel-$(date +%Y-%m-%d).log
```

### Permission denied sur storage/

```bash
chmod -R 755 storage bootstrap/cache
chown -R u12345:u12345 storage bootstrap/cache
```

### Migrations échouent

```bash
# Vérifier la connexion DB
php artisan db:show

# Voir l'état des migrations
php artisan migrate:status
```

### Assets non trouvés (404 sur /build/)

- Vérifier que `public/build/` a été uploadé
- Vérifier `public/build/manifest.json` existe
- S'assurer que `APP_URL` dans `.env` correspond exactement au domaine

### Queue emails en attente

```bash
# Voir les jobs en file
php artisan queue:monitor

# Vider une file bloquée
php artisan queue:clear

# Relancer manuellement
php artisan queue:work --once
```

### Redis connexion refusée

```bash
# Tester la connexion Redis
php artisan tinker --execute="Cache::put('test', 1); echo Cache::get('test');"
```

Vérifier `REDIS_PASSWORD` et `REDIS_HOST` dans `.env` — les valeurs exactes sont dans **cPanel → Redis**.

### N0C Storage : fichiers non accessibles

```bash
# Tester depuis tinker
php artisan tinker --execute="Storage::disk('s3')->put('test.txt', 'hello'); echo Storage::disk('s3')->url('test.txt');"
```

Vérifier `AWS_USE_PATH_STYLE_ENDPOINT=true` et l'endpoint exact dans le panneau N0C.

---

## Informations serveur de référence

| Paramètre | Valeur |
|---|---|
| Hébergement | PlanetHoster The World |
| PHP | 8.2+ (MultiPHP Manager) |
| MySQL | via cPanel (localhost) |
| Redis | Instance dédiée incluse |
| Storage | N0C Storage S3-compatible |
| Domaine | transtechno.cm |
| Email admin | office@transtechno-cmr.com |
| Hotline SAV | +237 674 64 53 54 |
