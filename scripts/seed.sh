#!/bin/bash
set -euo pipefail

echo "🌱 Re-seeding des données..."

# Vérifier que les conteneurs sont en marche
if ! docker compose ps backend | grep -q "Up"; then
    echo "❌ Le conteneur backend n'est pas en marche. Lancez d'abord ./scripts/dev-up.sh"
    exit 1
fi

# Exécuter les seeders
echo "🗄️ Exécution des seeders..."
docker compose exec backend php artisan migrate:fresh --seed --force

# Recréer le lien de stockage
echo "🔗 Recréation du lien de stockage..."
docker compose exec backend php artisan storage:link

echo "✅ Seeding terminé!"