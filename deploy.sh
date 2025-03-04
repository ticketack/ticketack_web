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
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app bash -c "find /var/www/html/storage -type d -exec chmod 775 {} \; && find /var/www/html/storage -type f -exec chmod 664 {} \; && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; && find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \;"

# Corriger les permissions pour les fichiers de build
echo "Correction des permissions pour les fichiers de build..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app bash -c "chmod -R 775 /var/www/html/node_modules || true && chmod 775 /var/www/html && chmod 664 /var/www/html/vite.config.js && chmod -R 775 /var/www/html/public && chown -R laravel:laravel /var/www/html/public"

# Nettoyer le cache npm
echo "Nettoyage du cache npm..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app bash -c "cd /var/www/html && npm cache clean --force"

# Supprimer le dossier node_modules pour une installation propre
echo "Suppression du dossier node_modules..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app bash -c "cd /var/www/html && rm -rf node_modules"

# Installer les dépendances npm
echo "Installation des dépendances npm..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app bash -c "cd /var/www/html && npm install --no-audit"

# Vérifier que les modules FullCalendar sont correctement installés
echo "Vérification des modules FullCalendar..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app bash -c "cd /var/www/html && ls -la node_modules/@fullcalendar || echo 'FullCalendar non trouvé, installation en cours...' && npm install @fullcalendar/core @fullcalendar/daygrid @fullcalendar/interaction @fullcalendar/timegrid @fullcalendar/vue3"

# Nettoyer le cache de Vite
echo "Nettoyage du cache de Vite..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app bash -c "cd /var/www/html && rm -rf node_modules/.vite"

# Compiler les assets
echo "Compilation des assets..."
# Exécuter en tant que root pour éviter les problèmes de permission
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app bash -c "cd /var/www/html && npm run build:prod"

# Restaurer les permissions correctes après la compilation
echo "Restauration des permissions après la compilation..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec -u root app bash -c "chown -R laravel:laravel /var/www/html/public/build && chmod -R 775 /var/www/html/public/build"

# Nettoyer le cache Laravel
echo "Nettoyage du cache Laravel..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app bash -c "cd /var/www/html && php artisan optimize:clear"
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app bash -c "cd /var/www/html && php artisan optimize"

echo "Déploiement terminé !"
