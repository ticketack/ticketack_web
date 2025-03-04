#!/bin/bash

echo "Déploiement en production..."

# Nettoyer le cache npm
echo "Nettoyage du cache npm..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app npm cache clean --force

# Supprimer le dossier node_modules pour une installation propre
echo "Suppression du dossier node_modules..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app rm -rf node_modules

# Installer les dépendances npm
echo "Installation des dépendances npm..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app npm install

# Vérifier que les modules FullCalendar sont correctement installés
echo "Vérification des modules FullCalendar..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app bash -c "ls -la node_modules/@fullcalendar"

# Nettoyer le cache de Vite
echo "Nettoyage du cache de Vite..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app rm -rf node_modules/.vite

# Compiler les assets
echo "Compilation des assets..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app npm run build:prod

# Nettoyer le cache Laravel
echo "Nettoyage du cache Laravel..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan optimize:clear
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan optimize

echo "Déploiement terminé !"
