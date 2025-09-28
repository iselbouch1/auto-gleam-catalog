#!/bin/bash
set -euo pipefail

echo "👤 Création d'un compte admin Filament..."

# Vérifier que les conteneurs sont en marche
if ! docker compose ps backend | grep -q "Up"; then
    echo "❌ Le conteneur backend n'est pas en marche. Lancez d'abord ./scripts/dev-up.sh"
    exit 1
fi

# Créer l'utilisateur admin avec des valeurs par défaut
echo "🔧 Création de l'utilisateur admin..."
docker compose exec backend php artisan make:filament-user \
    --name="Admin" \
    --email="admin@auto-gleam.test" \
    --password="Admin123!"

echo ""
echo "✅ Compte admin créé avec succès!"
echo ""
echo "🔐 Informations de connexion:"
echo "   📧 Email:     admin@auto-gleam.test"
echo "   🔑 Mot de passe: Admin123!"
echo ""
echo "🌐 Connexion admin: http://localhost/admin"
echo ""