<?php

return [
    'preferences' => [
        'title' => 'Préférences de notifications',
        'description' => 'Configurez vos préférences pour les notifications in-app, email et SMS.',
        'manage' => 'Gérer les préférences',
        'view_history' => 'Voir l\'historique des notifications',
        'contact_info' => 'Informations de contact',
        'phone_number' => 'Numéro de téléphone (pour les SMS)',
        'phone_format' => 'Format international recommandé (ex: +33612345678)',
        'api_key' => 'Clé API SMS (optionnel)',
        'api_key_description' => 'Si vide, la clé API par défaut sera utilisée',
        'save' => 'Enregistrer les préférences',
        'back_to_profile' => 'Retour au profil',
        'updated' => 'Préférences de notifications mises à jour avec succès',
    ],
    'logs' => [
        'title' => 'Journal des notifications',
        'no_notifications' => 'Aucune notification trouvée',
        'all_channels' => 'Tous les canaux',
        'all_statuses' => 'Tous les statuts',
        'unread' => 'Non lues',
        'read' => 'Lues',
        'mark_all_read' => 'Tout marquer comme lu',
        'mark_as_read' => 'Marquer comme lu',
        'already_read' => 'Déjà lu',
        'new' => 'Nouveau',
        'marked_as_read' => 'Notification marquée comme lue',
        'all_marked_as_read' => 'Toutes les notifications ont été marquées comme lues',
    ],
    'types' => [
        'ticket_created' => [
            'name' => 'Création d\'un ticket',
            'description' => 'Notification envoyée lorsqu\'un nouveau ticket est créé',
        ],
        'ticket_time_tracked' => [
            'name' => 'Pointage sur un ticket',
            'description' => 'Notification envoyée lorsqu\'un pointage est effectué sur un ticket',
        ],
        'ticket_commented' => [
            'name' => 'Commentaire sur un ticket',
            'description' => 'Notification envoyée lorsqu\'un commentaire est ajouté à un ticket',
        ],
        'ticket_status_changed' => [
            'name' => 'Changement de statut d\'un ticket',
            'description' => 'Notification envoyée lorsque le statut d\'un ticket est modifié',
        ],
        'ticket_assigned' => [
            'name' => 'Assignation d\'un ticket',
            'description' => 'Notification envoyée lorsqu\'un ticket est assigné à un utilisateur',
        ],
        'ticket_scheduled' => [
            'name' => 'Planification d\'un ticket',
            'description' => 'Notification envoyée lorsqu\'un ticket est planifié',
        ],
    ],
    'channels' => [
        'in_app' => 'In-App',
        'email' => 'Email',
        'sms' => 'SMS',
    ],
];
