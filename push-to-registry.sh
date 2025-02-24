#!/bin/bash

# Couleurs pour les messages
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Configuration
REGISTRY="rg.fr-par.scw.cloud/helpstream"
REGISTRY_USER="nologin"
REGISTRY_TOKEN="f1c49646-2dbb-4a68-9d98-6954c604ccba"

# Fonction pour afficher les messages
print_message() {
    echo -e "${BLUE}➡️  $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
    exit 1
}

# Vérifier si Docker est en cours d'exécution
if ! docker info > /dev/null 2>&1; then
    print_error "Docker n'est pas en cours d'exécution. Veuillez démarrer Docker."
fi

# Se connecter au registry
print_message "Connexion au registry Scaleway..."
if ! echo "$REGISTRY_TOKEN" | docker login $REGISTRY -u $REGISTRY_USER --password-stdin; then
    print_error "Échec de la connexion au registry"
fi
print_success "Connecté au registry"

# Tagger et pousser chaque image
push_image() {
    local local_image="$1"
    local registry_tag="$2"
    
    print_message "Traitement de l'image $local_image..."
    
    # Vérifier si l'image locale existe
    if ! docker image inspect "$local_image" >/dev/null 2>&1; then
        print_error "L'image locale $local_image n'existe pas"
    fi
    
    # Tagger l'image
    registry_path="$REGISTRY/$registry_tag"
    if ! docker tag "$local_image" "$registry_path"; then
        print_error "Échec du tag de l'image $local_image"
    fi
    print_success "Image taguée : $registry_path"
    
    # Pousser l'image
    print_message "Envoi de l'image vers le registry..."
    if ! docker push "$registry_path"; then
        print_error "Échec de l'envoi de l'image $registry_path"
    fi
    print_success "Image poussée : $registry_path"
}

# Pousser les images
push_image "laravel-app:latest" "laravel-app:latest"
push_image "nginx:alpine" "nginx:alpine"
push_image "bitnami/mysql:latest" "mysql:latest"

# Nettoyage des images intermédiaires
print_message "Nettoyage des images intermédiaires..."
docker image prune -f

print_success "Toutes les images ont été poussées avec succès !"

# Se déconnecter du registry
print_message "Déconnexion du registry..."
docker logout $REGISTRY
print_success "Déconnecté du registry"
