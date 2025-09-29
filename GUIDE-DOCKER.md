# 🐳 Guide Complet Docker - Auto Gleam

## 📋 Prérequis

- **Docker Desktop** installé et en cours d'exécution
- **Git** (pour cloner le projet)
- **8 GB RAM minimum** recommandés
- Ports libres : `80`, `3306`, `6379`, `8025`

## 🚀 Démarrage Rapide (One-Click)

```bash
# 1. Rendre les scripts exécutables
chmod +x scripts/*.sh

# 2. Lancer toute la stack
./scripts/dev-up.sh

# 3. Créer un compte admin Filament
./scripts/create-admin.sh
```

**C'est tout !** 🎉

## 📂 Architecture Docker

```
/auto-gleam/
├── docker-compose.yml          # Orchestration des services
├── nginx.conf                  # Reverse proxy
├── frontend/
│   ├── Dockerfile.dev         # Container React/Vite
│   └── ...
├── backend/
│   ├── Dockerfile.dev         # Container Laravel/PHP
│   └── ...
└── scripts/
    ├── dev-up.sh              # Démarrage complet
    ├── dev-down.sh            # Arrêt
    ├── create-admin.sh        # Créer admin
    └── seed.sh                # Re-seeder
```

## 🏗️ Services Docker

| Service   | Port    | Description                    | URL                      |
|-----------|---------|--------------------------------|--------------------------|
| nginx     | 80      | Reverse proxy principal        | http://localhost         |
| frontend  | 3000    | React + Vite (HMR)            | (via nginx /)            |
| backend   | 8000    | Laravel API + Filament        | (via nginx /api, /admin) |
| mysql     | 3306    | Base de données               | localhost:3306           |
| redis     | 6379    | Cache + Sessions              | localhost:6379           |
| mailpit   | 8025    | Interface mail de test        | http://localhost:8025    |

## 🔧 Configuration Automatique

Le script `dev-up.sh` effectue automatiquement :

1. **Migration du code** vers structure monorepo (frontend/ + backend/)
2. **Construction** des images Docker
3. **Attente** de MySQL (healthcheck)
4. **Installation** des dépendances (composer + npm)
5. **Génération** des clés Laravel
6. **Migrations** + seeding de la DB
7. **Liaison** du stockage (`storage:link`)
8. **Ouverture** du navigateur

## 📍 URLs Importantes

- 🌐 **Site visiteur** : http://localhost
- 🔧 **Admin Filament** : http://localhost/admin
- 📧 **Mailpit** (emails) : http://localhost:8025
- 🗄️ **Images** : http://localhost/storage/...

## 🛠️ Commandes Utiles

### Gestion de la Stack

```bash
# Démarrer tout
./scripts/dev-up.sh

# Arrêter tout
./scripts/dev-down.sh

# Arrêter + supprimer volumes (reset complet)
./scripts/dev-down.sh --volumes

# Re-seeder les données
./scripts/seed.sh

# Créer un admin Filament
./scripts/create-admin.sh
```

### Commandes Docker Direct

```bash
# Voir les logs
docker compose logs -f [service]

# Shell dans un container
docker compose exec backend bash
docker compose exec frontend sh

# Rebuilder un service
docker compose up -d --build [service]

# Redémarrer un service
docker compose restart [service]
```

### Commandes Laravel (dans le container)

```bash
# Shell backend
docker compose exec backend bash

# Artisan commands
php artisan migrate
php artisan db:seed
php artisan cache:clear
php artisan config:clear
php artisan route:list
php artisan reverb:start

# Créer un utilisateur Filament
php artisan make:filament-user
```

### Commandes Frontend (dans le container)

```bash
# Shell frontend
docker compose exec frontend sh

# NPM commands
npm install
npm run dev
npm run build
npm run lint
```

## 🔍 Débogage

### Vérifier l'état des services

```bash
docker compose ps
docker compose logs [service]
```

### Services qui ne démarrent pas

```bash
# MySQL
docker compose logs mysql
# Vérifier si le port 3306 est libre

# Redis
docker compose logs redis
# Vérifier si le port 6379 est libre

# Backend
docker compose logs backend
# Vérifier les erreurs PHP/Composer
```

### Problèmes courants

#### Port déjà utilisé
```bash
# Voir qui utilise le port 80
lsof -i :80

# Changer le port dans docker-compose.yml
ports:
  - "8080:80"  # Au lieu de "80:80"
```

#### Erreurs de permissions
```bash
# Réparer les permissions storage
docker compose exec backend chown -R www-data:www-data storage
docker compose exec backend chmod -R 755 storage
```

#### HMR ne fonctionne pas
```bash
# Vérifier que le frontend est accessible
curl http://localhost:3000

# Redémarrer nginx
docker compose restart nginx
```

#### Base de données vide
```bash
# Re-migrer et seeder
docker compose exec backend php artisan migrate:fresh --seed --force
```

## 📊 Monitoring

### Vérifier que tout fonctionne

```bash
# Status de tous les services
docker compose ps

# Healthchecks
docker compose exec mysql mysqladmin ping
docker compose exec redis redis-cli ping

# Test de l'API backend
curl http://localhost/api/v1/categories

# Test du frontend
curl http://localhost
```

### Logs temps réel

```bash
# Tous les services
docker compose logs -f

# Service spécifique
docker compose logs -f backend
docker compose logs -f frontend
docker compose logs -f nginx
```

## 🧹 Nettoyage

### Nettoyage léger

```bash
./scripts/dev-down.sh
docker system prune
```

### Nettoyage complet (ATTENTION: perte des données)

```bash
./scripts/dev-down.sh --volumes
docker system prune -a --volumes
docker volume prune
```

## 🔄 Hot Reload & Développement

- **Frontend** : HMR Vite automatique via Nginx WebSocket proxy
- **Backend** : Rechargement manuel via `docker compose restart backend`
- **Images** : Stockage persistant dans volume Docker
- **DB** : Données persistantes dans volume MySQL

## 🚨 Dépannage d'Urgence

Si rien ne fonctionne :

```bash
# 1. Tout arrêter
docker compose down --volumes --remove-orphans

# 2. Nettoyer
docker system prune -a --volumes

# 3. Relancer
./scripts/dev-up.sh
```

## ⚡ Performance & Optimisation

### Volumes pour performances

Les `node_modules` et `vendor` sont dans des volumes séparés pour éviter les latences de bind mount.

### Cache Docker

```bash
# Réutiliser le cache de build
docker compose build --parallel

# Forcer un rebuild complet
docker compose build --no-cache
```

---

## 📞 Support

En cas de problème persistant :

1. Vérifiez les logs : `docker compose logs`
2. Vérifiez l'état : `docker compose ps`
3. Testez les URLs individuellement
4. Nettoyez et relancez

**Happy coding!** 🚀