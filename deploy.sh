#!/bin/bash

echo "Déploiement en production..."

# Vérifier que les conteneurs sont en cours d'exécution
echo "Vérification des conteneurs..."
if ! docker compose -f docker-compose.yml -f docker-compose.prod.yml ps | grep -q "app.*running"; then
    echo "Le conteneur app n'est pas en cours d'exécution. Démarrage des conteneurs..."
    docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
    sleep 10  # Attendre que les conteneurs soient prêts
fi

# Vérifier le répertoire de travail dans le conteneur
echo "Vérification du répertoire de travail..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app bash -c "pwd && ls -la"

# Corriger les permissions uniquement pour les fichiers importants
echo "Correction des permissions des fichiers importants..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app bash -c "find /var/www/html/storage -type d -exec chmod 775 {} \; && find /var/www/html/storage -type f -exec chmod 664 {} \; && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; && find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \;"

# Corriger les permissions pour les fichiers de build
echo "Correction des permissions pour les fichiers de build..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app bash -c "chmod -R 775 /var/www/html/node_modules || true && chmod 775 /var/www/html && chmod 664 /var/www/html/vite.config.js && chmod -R 775 /var/www/html/public && chown -R laravel:laravel /var/www/html/public"

# Nettoyer le cache npm
echo "Nettoyage du cache npm..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app npm cache clean --force

# Supprimer le dossier node_modules pour une installation propre
echo "Suppression du dossier node_modules..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app rm -rf node_modules

# Installer les dépendances npm
echo "Installation des dépendances npm..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app npm install

# Nettoyer le cache de Vite
echo "Nettoyage du cache de Vite..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app rm -rf node_modules/.vite

# Compiler les assets
echo "Compilation des assets..."
# Exécuter en tant que root pour éviter les problèmes de permission
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app npm run build:prod

# Restaurer les permissions correctes après la compilation
echo "Restauration des permissions après la compilation..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app bash -c "chown -R laravel:laravel /var/www/html/public/build && chmod -R 775 /var/www/html/public/build"

# Nettoyer le cache Laravel
echo "Nettoyage du cache Laravel..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan optimize:clear
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan optimize

# Exécuter les migrations
echo "Exécution des migrations..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan migrate --force

# Définir les bonnes permissions pour les fichiers du projet
docker compose -f docker-compose.prod.yml exec -u root app bash -c 'chown -R www-data:www-data /var/www/html'

# Définir les bonnes permissions pour les fichiers du projet
echo "Configuration du safe.directory pour git..."

# Permissions spéciales pour les dossiers qui nécessitent des droits d'écriture
docker compose -f docker-compose.prod.yml exec -u root app bash -c 'chmod -R 775 /var/www/html/storage'
docker compose -f docker-compose.prod.yml exec -u root app bash -c 'chmod -R 775 /var/www/html/bootstrap/cache'

# Installer les dépendances Composer
echo "Installation des dépendances Composer..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app composer install --no-dev --optimize-autoloader

echo "Déploiement terminé !"
