#!/bin/bash
set -euo pipefail

echo "🛑 Arrêt de la stack Auto Gleam..."

# Arrêter les conteneurs
docker compose down

# Demander si on veut supprimer les volumes
echo ""
read -p "Voulez-vous aussi supprimer les volumes (données DB/Redis) ? (y/N): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "🗑️  Suppression des volumes..."
    docker compose down -v
    docker volume prune -f
    echo "✅ Volumes supprimés"
else
    echo "💾 Volumes conservés"
fi

echo "✅ Stack arrêtée"