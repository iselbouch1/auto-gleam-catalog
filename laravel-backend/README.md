# AutoCatalogue - Backend Laravel 11

Backend complet pour catalogue vitrine d'accessoires automobiles avec admin Filament et API REST publique.

## 🚀 Fonctionnalités

### ✨ Administration (Filament v3)
- **Interface Admin** `/admin` - Gestion complète des catégories et produits
- **Upload d'images** - Multi-upload avec drag & drop, conversions automatiques  
- **Gestion des médias** - Définition de cover, réorganisation, thumbnails
- **Catégories hiérarchiques** - Support parent/enfant avec tri
- **Tags système** - Tags libres pour filtrage avancé
- **Actions bulk** - Publier/masquer en lot, suppression groupée

### 🔌 API REST Publique (`/api/v1`)
- **Lecture seule** - Aucun endpoint d'écriture exposé
- **Cache intelligent** - Système de cache avec invalidation automatique
- **Pagination** - Réponses paginées avec métadonnées complètes
- **Filtrage avancé** - Par catégorie, tags, visibilité, recherche textuelle
- **CORS configuré** - Autorise les requêtes depuis le frontend
- **Rate limiting** - 60 requêtes/minute par IP

### 📷 Gestion d'Images
- **Collections média** - Images organisées par collection
- **Conversions automatiques** - Thumb (400px), Cover (1200px)
- **Optimisation** - Compression et optimisation automatique
- **Propriétés custom** - is_cover, alt text personnalisé
- **Support multi-formats** - JPEG, PNG, WebP

## 🛠 Stack Technique

- **Laravel 11** - Framework PHP 8.3
- **Filament v3** - Interface d'administration moderne
- **Spatie Media Library** - Gestion avancée des médias
- **Spatie Sluggable** - Génération automatique de slugs
- **Spatie Tags** - Système de tags flexible
- **Laravel Sanctum** - Authentification admin
- **MySQL 8.0** - Base de données relationnelle
- **Redis** - Cache et sessions (optionnel)

## 📋 Prérequis

- **PHP 8.3** ou supérieur
- **Composer** 2.x
- **MySQL 8.0** ou **PostgreSQL 13+**
- **Node.js 18+** (pour les assets)
- Extensions PHP : `gd`, `imagick`, `zip`, `mbstring`, `pdo_mysql`

## 🚀 Installation

### 1. Cloner et installer les dépendances

```bash
# Cloner le repository
git clone <votre-repo> autocatalogue-backend
cd autocatalogue-backend

# Installer les dépendances PHP
composer install

# Copier et configurer l'environnement
cp .env.example .env
php artisan key:generate
```

### 2. Configuration de la base de données

Éditez `.env` avec vos paramètres de DB :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=autocatalogue
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migration et données de test

```bash
# Créer les tables
php artisan migrate

# Générer les données de démonstration
php artisan db:seed

# Lier le stockage public
php artisan storage:link
```

### 4. Créer un utilisateur admin

```bash
# Créer un compte administrateur Filament
php artisan make:filament-user

# Suivre les instructions pour créer votre compte
```

### 5. Lancer l'application

```bash
# Serveur de développement
php artisan serve

# L'API sera disponible sur http://localhost:8000
# L'admin sur http://localhost:8000/admin
```

## 🐳 Installation Docker

Alternative avec Docker pour un environnement complet :

```bash
# Construire et lancer les services
docker-compose up -d --build

# Installer les dépendances dans le container
docker-compose exec app composer install

# Migrations et seed
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link

# Créer un admin
docker-compose exec app php artisan make:filament-user
```

**Services disponibles :**
- **Application** : http://localhost:8000
- **Admin Filament** : http://localhost:8000/admin  
- **phpMyAdmin** : http://localhost:8080
- **Base de données** : localhost:3306

## 📝 Configuration

### Variables d'environnement importantes

```env
# Frontend autorisé pour CORS
FRONT_ORIGIN=http://localhost:3000

# Stockage des fichiers (local par défaut)
FILESYSTEM_DISK=public

# Cache (optionnel - file par défaut)
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1

# S3/R2 (optionnel pour production)
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=auto
AWS_BUCKET=your-bucket
AWS_URL=https://your-domain.r2.cloudflarestorage.com
```

### Configuration S3/Cloudflare R2

Pour utiliser un stockage externe en production :

1. Configurer les variables AWS_* dans `.env`
2. Changer `FILESYSTEM_DISK=s3` 
3. Les URLs d'images utiliseront automatiquement le CDN

## 🎯 Utilisation de l'Admin

### Gestion des Catégories

1. Accéder à `/admin/categories`
2. **Créer** : Nom (slug auto-généré), parent optionnel, visibilité, ordre
3. **Hiérarchie** : Catégories parent/enfant supportées
4. **Tri** : Champ `sort_order` pour organiser l'affichage

### Gestion des Produits

1. Accéder à `/admin/products`
2. **Informations** : Nom, slug, descriptions courte/longue
3. **Organisation** : Multi-catégories, tags libres
4. **Statut** : Visible/masqué, coup de cœur
5. **Spécifications** : Propriétés key/value en JSON
6. **Images** :
   - Upload multiple par drag & drop
   - **Définir cover** : Clic sur image → "Définir comme cover"
   - Réorganiser par glisser-déposer
   - Alt text automatique (nom produit)

### Actions Rapides

- **Toggle visibilité** : Bouton "Publier/Masquer" sur chaque ligne
- **Actions bulk** : Sélection multiple → Publier/Masquer/Supprimer
- **Filtres** : Par visibilité, catégorie, tags
- **Recherche** : Dans nom, descriptions

## 🔌 API Endpoints

Base URL : `/api/v1`

### 📂 Catégories

```bash
# Lister toutes les catégories visibles
GET /categories

# Filtrer par visibilité
GET /categories?visible=1

# Catégories enfants d'une catégorie parent
GET /categories?parent=eclairage

# Détail d'une catégorie (avec enfants)
GET /categories/{slug}
```

**Réponse catégorie :**
```json
{
  "id": 1,
  "name": "Éclairage",
  "slug": "eclairage", 
  "parentId": null,
  "isVisible": true,
  "sortOrder": 1,
  "children": [...]
}
```

### 📦 Produits

```bash
# Lister les produits avec pagination
GET /products?page=1&per_page=24

# Recherche textuelle
GET /products?search=led+premium

# Filtrer par catégorie (slug ou ID)
GET /products?category=eclairage

# Filtrer par tags (CSV)
GET /products?tags=LED,Premium,RGB

# Filtrer les coups de cœur uniquement
GET /products?featured=1

# Tri (name_asc, name_desc, recent)
GET /products?sort=recent

# Combinaison de filtres
GET /products?category=eclairage&tags=LED&featured=1&search=premium&sort=name_asc
```

**Réponse produits (paginée) :**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Kit LED Premium",
      "slug": "kit-led-premium",
      "shortDescription": "Éclairage LED...",
      "description": "<p>Description HTML...</p>",
      "isVisible": true,
      "isFeatured": true,
      "sortOrder": 0,
      "categories": [
        {"id": 1, "name": "Éclairage", "slug": "eclairage"}
      ],
      "tags": ["LED", "RGB", "Premium"],
      "images": [
        {
          "id": 1,
          "url": "https://domain.com/storage/1/kit-led.jpg",
          "alt": "Kit LED Premium",
          "isCover": true,
          "width": 1200,
          "height": 800,
          "conversions": {
            "thumb": "https://domain.com/storage/1/conversions/kit-led-thumb.jpg",
            "cover": "https://domain.com/storage/1/conversions/kit-led-cover.jpg"
          }
        }
      ],
      "specs": {
        "Tension": "12V DC",
        "Couleurs": "16 millions",
        "Garantie": "2 ans"
      }
    }
  ],
  "meta": {
    "page": 1,
    "per_page": 24,
    "total": 156,
    "has_next": true,
    "last_page": 7
  }
}
```

### 🔍 Détail Produit

```bash
# Détail complet avec produits associés
GET /products/{slug}
```

**Réponse détail (même structure + produits liés) :**
```json
{
  // ... champs produit
  "relatedProducts": [
    // Produits de même catégorie
  ]
}
```

### 🏃‍♂️ Exemples cURL

```bash
# Lister catégories
curl -X GET "http://localhost:8000/api/v1/categories" \
  -H "Accept: application/json"

# Rechercher produits LED
curl -X GET "http://localhost:8000/api/v1/products?search=led&category=eclairage" \
  -H "Accept: application/json"

# Détail produit
curl -X GET "http://localhost:8000/api/v1/products/kit-led-premium" \
  -H "Accept: application/json"
```

## 🧪 Tests

```bash
# Lancer tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter=ApiProductsTest
php artisan test --filter=ApiCategoriesTest

# Avec couverture
php artisan test --coverage
```

**Tests inclus :**
- **API Products** : Liste, filtres, pagination, recherche, détail
- **API Categories** : Liste, hiérarchie, visibilité, détail
- **Rate limiting** : Limite de requêtes par IP
- **CORS** : Vérification des headers autorisés

## 🗄️ Structure Base de Données

### Categories
```sql
- id (bigint, PK)
- name (varchar)
- slug (varchar, unique)
- parent_id (bigint, nullable, FK)
- is_visible (boolean, default true)
- sort_order (int, default 0)
- timestamps
```

### Products  
```sql
- id (bigint, PK)
- name (varchar)
- slug (varchar, unique)
- short_description (text, nullable)
- description (longtext, nullable)
- is_visible (boolean, default true)
- is_featured (boolean, default false)
- sort_order (int, default 0)
- specs (json, nullable)
- timestamps
```

### Relations
- **product_category** : Table pivot Many-to-Many
- **taggables** : Tags Spatie (Many-to-Many polymorphic)
- **media** : Spatie Media Library (One-to-Many polymorphic)

## ⚡ Performance & Cache

### Système de Cache

Le cache est automatiquement géré avec invalidation :

```php
// Cache par endpoint avec tags
Cache::tags(['products'])->remember($key, 3600, $callback);

// Invalidation automatique via Observers
Cache::tags(['products', "product.{$id}"])->flush();
```

**Tags de cache :**
- `products` : Toutes les listes de produits
- `categories` : Toutes les listes de catégories  
- `product.{id}` : Cache spécifique d'un produit
- `category.{id}` : Cache spécifique d'une catégorie

### Optimisations Images

```php
// Conversions automatiques
'thumb' => 400px max width
'cover' => 1200px max width

// Optimisation avec compression
jpegoptim -m85 --strip-all
pngquant --force
```

## 🔒 Sécurité

### CORS Configuration

```php
// config/cors.php
'allowed_origins' => [env('FRONT_ORIGIN')],
'allowed_methods' => ['GET', 'HEAD', 'OPTIONS'],
```

### Rate Limiting

```php
// 60 requêtes par minute par IP
Route::middleware('throttle:60,1')
```

### Validation Inputs

```php
// FormRequest avec validation stricte
'per_page' => 'sometimes|integer|min:1|max:60',
'search' => 'sometimes|string|max:255',
```

## 🚀 Déploiement Production

### 1. Optimisations Laravel

```bash
# Cache de configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimisation Composer
composer install --optimize-autoloader --no-dev
```

### 2. Variables d'environnement

```env
APP_ENV=production
APP_DEBUG=false
FILESYSTEM_DISK=s3  # Si stockage externe
CACHE_DRIVER=redis  # Performance
```

### 3. Web Server (Nginx)

```nginx
# Servir les médias depuis storage
location /storage {
    alias /var/www/storage/app/public;
    expires 1y;
    add_header Cache-Control "public, immutable";
}

# Headers sécurité
add_header X-Frame-Options "SAMEORIGIN";
add_header X-Content-Type-Options "nosniff";
```

### 4. Monitoring

```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Performance
php artisan horizon  # Si queues Redis
php artisan schedule:run  # Tâches cron
```

## 🤝 Contribution

1. **Fork** le projet
2. **Créer une branche** (`git checkout -b feature/amelioration`)
3. **Commiter** (`git commit -am 'Ajout fonctionnalité'`)
4. **Pousser** (`git push origin feature/amelioration`)
5. **Pull Request**

## 📞 Support

- **Documentation** : Ce README
- **Issues** : GitHub Issues pour bugs/demandes
- **API** : Tester avec Postman/Insomnia
- **Admin** : Interface intuitive à `/admin`

---

**Développé avec ❤️ pour les passionnés d'automobile**

*Backend Laravel 11 • Filament v3 • API REST • Gestion média avancée*