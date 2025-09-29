#!/bin/bash
set -euo pipefail

echo "🚀 Auto Gleam - Démarrage de la stack complète..."

# Vérifier que Docker est en marche
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker n'est pas en marche. Veuillez le démarrer."
    exit 1
fi

# Créer les répertoires s'ils n'existent pas
mkdir -p backend frontend

# Copier le code existant vers la structure monorepo
if [ ! -d "backend/app" ] && [ -d "laravel-backend" ]; then
    echo "📁 Migration du backend vers la structure monorepo..."
    cp -r laravel-backend/* backend/
    # Copier le .env.example seulement si le .env n'existe pas
    if [ ! -f "backend/.env" ]; then
        cp backend/.env.example backend/.env 2>/dev/null || true
    fi
fi

if [ ! -d "frontend/src" ] && [ -f "package.json" ]; then
    echo "📁 Migration du frontend vers la structure monorepo..."
    # Créer frontend/ et y copier les fichiers front
    mkdir -p frontend
    cp package.json frontend/ 2>/dev/null || true
    cp -r src frontend/ 2>/dev/null || true
    cp -r public frontend/ 2>/dev/null || true
    cp components.json frontend/ 2>/dev/null || true
    cp tailwind.config.ts frontend/ 2>/dev/null || true
    cp tsconfig*.json frontend/ 2>/dev/null || true
    cp vite.config.ts frontend/ 2>/dev/null || true
    cp index.html frontend/ 2>/dev/null || true
    cp eslint.config.js frontend/ 2>/dev/null || true
    cp postcss.config.js frontend/ 2>/dev/null || true
    # Copier le .env.example seulement si le .env n'existe pas
    if [ ! -f "frontend/.env" ]; then
        cp frontend/.env.example frontend/.env 2>/dev/null || true
    fi
fi

# Construire et démarrer les services
echo "🐳 Construction et démarrage des conteneurs..."
docker compose up -d --build

# Attendre que MySQL soit prêt
echo "⏳ Attente de MySQL..."
timeout=60
while ! docker compose exec mysql mysqladmin ping -h localhost --silent 2>/dev/null; do
    timeout=$((timeout - 1))
    if [ $timeout -eq 0 ]; then
        echo "❌ Timeout en attendant MySQL"
        exit 1
    fi
    sleep 1
done

echo "✅ MySQL est prêt"

# Attendre que le backend soit prêt
echo "⏳ Attente du backend..."
timeout=60
while ! docker compose exec backend php -v > /dev/null 2>&1; do
    timeout=$((timeout - 1))
    if [ $timeout -eq 0 ]; then
        echo "❌ Timeout en attendant le backend"
        exit 1
    fi
    sleep 1
done

echo "✅ Backend est prêt"

# Les commandes suivantes sont déjà exécutées dans le conteneur backend
# mais on peut les re-exécuter pour s'assurer qu'elles fonctionnent

# Vérifier si les dépendances sont installées
if ! docker compose exec backend composer show > /dev/null 2>&1; then
    echo "📦 Installation des dépendances backend..."
    docker compose exec backend composer install --no-interaction
fi

# Vérifier si la clé est générée
if ! docker compose exec backend php artisan env:get APP_KEY > /dev/null 2>&1; then
    echo "🔑 Génération de la clé d'application..."
    docker compose exec backend php artisan key:generate --force
fi

# Installer les dépendances frontend
echo "📦 Installation des dépendances frontend..."
docker compose exec frontend npm install

echo ""
echo "🎉 Stack Auto Gleam démarrée avec succès!"
echo ""
echo "📍 URLs disponibles:"
echo "   🌐 Site visiteur:    http://localhost"
echo "   🔧 Admin Filament:   http://localhost/admin"
echo "   📧 Mailpit:          http://localhost:8025"
echo ""
echo "🔐 Pour créer un compte admin:"
echo "   ./scripts/create-admin.sh"
echo ""
echo "🛠️ Commandes utiles:"
echo "   ./scripts/dev-down.sh    # Arrêter la stack"
echo "   ./scripts/seed.sh        # Re-seeder les données"
echo ""

# Ouvrir le navigateur (si possible)
if command -v open > /dev/null; then
    echo "🌐 Ouverture du navigateur..."
    open http://localhost
elif command -v xdg-open > /dev/null; then
    echo "🌐 Ouverture du navigateur..."
    xdg-open http://localhost
fi