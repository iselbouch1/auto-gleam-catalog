# 🚀 Guide d'exécution local (Sans Docker)

## 📋 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **PHP 8.3+** avec extensions :
  - PDO, MySQL, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, gd
- **Composer** (gestionnaire de dépendances PHP)
- **Node.js 18+** et **npm**
- **MySQL 8.0+** ou **MariaDB 10.3+**
- **Redis** (optionnel, mais recommandé pour les websockets)

---

## 📁 Structure du projet

```
projet/
├── backend/          # Laravel + Filament
└── frontend/         # React + Vite
```

---

## ⚙️ Configuration et démarrage

### 1️⃣ Configuration de la base de données

Créez une base de données MySQL :

```bash
mysql -u root -p
CREATE DATABASE auto_gleam CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'auto_gleam'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON auto_gleam.* TO 'auto_gleam'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

### 2️⃣ Installation du Backend (Laravel)

```bash
cd backend

# Copier le fichier d'environnement
cp .env.example .env

# Installer les dépendances PHP
composer install

# Générer la clé d'application
php artisan key:generate

# Configurer votre fichier .env avec vos paramètres
# Modifiez ces lignes :
# DB_DATABASE=auto_gleam
# DB_USERNAME=auto_gleam
# DB_PASSWORD=password

# Exécuter les migrations
php artisan migrate

# Créer les liens symboliques pour le storage
php artisan storage:link

# Seeder les données de test (optionnel)
php artisan db:seed
```

---

### 3️⃣ Créer un utilisateur admin Filament

```bash
php artisan make:filament-user
```

Entrez :
- **Name** : Admin
- **Email** : admin@example.com
- **Password** : password (ou votre mot de passe)

---

### 4️⃣ Installation du Frontend (React)

```bash
cd ../frontend

# Copier le fichier d'environnement
cp .env.example .env

# Installer les dépendances npm
npm install
```

Configurez le fichier `.env` :

```env
VITE_API_BASE_URL=http://localhost:8000/api/v1
VITE_USE_MOCK=false

# WebSockets (optionnel - si vous utilisez Reverb)
VITE_PUSHER_APP_KEY=auto-gleam-app-key
VITE_PUSHER_HOST=localhost
VITE_PUSHER_PORT=8080
VITE_PUSHER_SCHEME=http
```

---

### 5️⃣ Démarrage des serveurs

#### Terminal 1 : Backend Laravel

```bash
cd backend
php artisan serve
```

Le backend sera accessible sur **http://localhost:8000**

#### Terminal 2 : Frontend React

```bash
cd frontend
npm run dev
```

Le frontend sera accessible sur **http://localhost:5173**

#### Terminal 3 : WebSockets Reverb (optionnel)

Si vous voulez le temps réel :

```bash
cd backend
php artisan reverb:start
```

Les WebSockets seront sur **ws://localhost:8080**

---

## 🌐 URLs d'accès

| Service | URL | Identifiants |
|---------|-----|--------------|
| **Site visiteur** | http://localhost:5173 | - |
| **API Backend** | http://localhost:8000/api/v1 | - |
| **Admin Filament** | http://localhost:8000/admin | admin@example.com / password |
| **WebSockets** | ws://localhost:8080 | - |

---

## 🛠️ Commandes utiles

### Backend

```bash
# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Créer un nouvel utilisateur admin
php artisan make:filament-user

# Réinitialiser la base de données
php artisan migrate:fresh --seed

# Vérifier les routes
php artisan route:list

# Mode maintenance
php artisan down
php artisan up
```

### Frontend

```bash
# Lancer en mode développement
npm run dev

# Build pour production
npm run build

# Prévisualiser le build
npm run preview

# Linter le code
npm run lint
```

---

## 🐛 Résolution des problèmes

### Problème : "Connection refused" sur MySQL

**Solution** : Vérifiez que MySQL est démarré :

```bash
# macOS
brew services start mysql

# Linux
sudo systemctl start mysql

# Windows
# Démarrez MySQL depuis les Services
```

### Problème : "Permission denied" sur storage

**Solution** :

```bash
cd backend
chmod -R 775 storage bootstrap/cache
```

### Problème : CORS errors

**Solution** : Vérifiez que `FRONT_ORIGIN` dans `backend/.env` correspond à l'URL du frontend :

```env
FRONT_ORIGIN=http://localhost:5173
```

### Problème : WebSockets ne se connectent pas

**Solution** :
1. Assurez-vous que Reverb est démarré
2. Vérifiez les variables d'environnement dans les deux `.env`
3. Installez Redis si nécessaire :

```bash
# macOS
brew install redis
brew services start redis

# Linux
sudo apt install redis-server
sudo systemctl start redis
```

---

## 📦 Structure des données

### Products (Produits)

Les produits ont :
- Nom, description
- Catégories multiples
- Tags
- Images
- Spécifications (key-value)
- Visibilité et statut "featured"

### Categories

Les catégories ont :
- Nom, slug
- Hiérarchie (parent/enfant)
- Visibilité
- Ordre de tri

---

## 🎨 Personnalisation

### Modifier les seeders

```bash
cd backend
# Éditer : database/seeders/ProductSeeder.php
# Éditer : database/seeders/CategorySeeder.php

# Réexécuter
php artisan migrate:fresh --seed
```

### Ajouter des images

Placez vos images dans `backend/public/images/` et référencez-les dans les seeders.

---

## 🚀 Déploiement

Pour déployer en production, consultez :
- `GUIDE-DOCKER.md` - Pour Docker
- Documentation Laravel : https://laravel.com/docs/deployment
- Documentation Vite : https://vitejs.dev/guide/build.html

---

## 📚 Documentation

- [Laravel](https://laravel.com/docs)
- [Filament](https://filamentphp.com/docs)
- [React](https://react.dev)
- [Vite](https://vitejs.dev)
- [TailwindCSS](https://tailwindcss.com)
- [Reverb](https://reverb.laravel.com)