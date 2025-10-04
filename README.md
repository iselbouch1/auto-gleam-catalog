# 🚗 Auto Gleam - Catalogue d'accessoires automobiles

Catalogue moderne d'accessoires automobiles avec backend Laravel + Filament et frontend React.

## 📁 Structure du projet

```
auto-gleam/
├── backend/          # Backend Laravel + Filament CMS
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── routes/
│   └── ...
└── frontend/         # Frontend React + Vite
    ├── src/
    ├── public/
    └── ...
```

## 🚀 Démarrage rapide

### Option 1 : Développement local (Recommandé)

Consultez **[RUN.md](./RUN.md)** pour le guide complet d'installation et d'exécution en local.

```bash
# 1. Backend
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

# 2. Frontend (dans un autre terminal)
cd frontend
npm install
cp .env.example .env
npm run dev
```

### Option 2 : Docker

Consultez **[GUIDE-DOCKER.md](./GUIDE-DOCKER.md)** ou **[RUN-QUICK.md](./RUN-QUICK.md)** pour Docker.

```bash
./scripts/dev-up.sh
```

## 🌐 URLs d'accès

| Service | URL | Identifiants |
|---------|-----|--------------|
| Site visiteur | http://localhost:5173 | - |
| Admin Filament | http://localhost:8000/admin | admin@example.com |
| API | http://localhost:8000/api/v1 | - |

## 🛠️ Technologies

### Backend
- Laravel 11
- Filament 3 (Admin Panel)
- Spatie Media Library
- Laravel Reverb (WebSockets)
- MySQL

### Frontend
- React 18
- TypeScript
- Vite
- TailwindCSS
- React Query
- React Router
- Pusher.js (WebSockets)

## 📚 Documentation

- [RUN.md](./RUN.md) - Guide d'exécution local complet
- [GUIDE-DOCKER.md](./GUIDE-DOCKER.md) - Guide Docker détaillé
- [RUN-QUICK.md](./RUN-QUICK.md) - Démarrage rapide Docker

## ✨ Fonctionnalités

- ✅ Catalogue de produits avec catégories
- ✅ Recherche et filtres avancés
- ✅ Admin panel Filament complet
- ✅ Upload d'images multiple
- ✅ Tags et spécifications produits
- ✅ API RESTful
- ✅ Temps réel avec WebSockets (optionnel)
- ✅ Responsive design

## 🤝 Contribution

Ce projet est un catalogue de démonstration. Vous pouvez l'adapter à vos besoins.

## 📝 Licence

MIT