#!/bin/bash

# Domaines à sécuriser
domains=(optimachines.pilot-up.com)
rsa_key_size=4096
data_path="./docker-compose/nginx/certbot"
email="sysadmin@id-ingenierie.com" # Remplacez par votre email

# Créer le répertoire pour certbot
mkdir -p "$data_path/conf/live/$domains"

# Arrêter le conteneur nginx s'il est en cours d'exécution
docker compose -f docker-compose.yml -f docker-compose.prod.yml down

# Supprimer les certificats existants
rm -rf "$data_path/conf/live/$domains"

# Créer des certificats auto-signés temporaires
mkdir -p "$data_path/conf/live/$domains"
docker run --rm -v "$data_path/conf:/etc/letsencrypt" certbot/certbot \
    certonly --webroot \
    -w /var/www/certbot \
    "${domains[@]/#/-d }" \
    --email "$email" \
    --rsa-key-size "$rsa_key_size" \
    --agree-tos \
    --force-renewal

# Redémarrer nginx
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
