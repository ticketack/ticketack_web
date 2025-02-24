<?php

return [
    'index' => [
        'title' => 'Tickets',
        'filters' => 'Filtres',
        'new_ticket' => 'Nouveau Ticket',
        'private' => 'Privé',
        'columns' => [
            'id' => 'ID',
            'title' => 'Titre',
            'status' => 'Statut',
            'visibility' => 'Visibilité',
            'priority' => 'Priorité',
            'category' => 'Catégorie',
            'equipment' => 'Équipement',
            'assigned_to' => 'Assigné à',
            'author' => 'Auteur',
            'created_at' => 'Créé le'
        ]
    ],
    'create' => [
        'title' => 'Créer un ticket',
        'step1' => [
            'title' => 'Informations du ticket',
            'completed' => 'Informations complétées'
        ],
        'step2' => [
            'title' => 'Documents',
            'drag_files' => 'Documents ajoutés',
            'no_files' => 'Aucun document ajouté',
            'remove_file' => 'Supprimer ce document'
        ],
        'fields' => [
            'title' => 'Titre',
            'description' => 'Description',
            'priority' => 'Priorité',
            'category' => 'Catégorie',
            'equipment' => 'Équipement',
            'visibility' => 'Visibilité',
            'status' => 'Statut',
            'assigned_to' => 'Assigné à',
            'due_date' => 'Date de résolution souhaitée'
        ],
        'visibility' => [
            'public_label' => 'Public'
        ],
        'priority' => [
            'low' => 'Basse',
            'medium' => 'Moyenne',
            'high' => 'Haute',
            'critical' => 'Critique'
        ],
        'buttons' => [
            'cancel' => 'Annuler',
            'next' => 'Suivant',
            'modify' => 'Modifier',
            'finish' => 'Terminer',
            'back' => 'Revenir à l\'étape 1',
            'submit' => 'Envoyer',
            'add_files' => 'Ajouter des fichiers',
            'remove' => 'Supprimer'
        ]
    ],
    'comments' => [
        'title' => 'Commentaires',
        'no_comments' => 'Aucun commentaire pour le moment',
        'add' => 'Ajouter un commentaire',
        'placeholder' => 'Votre commentaire...',
        'submit' => 'Envoyer',
        'attachments' => 'Pièces jointes',
        'add_files' => 'Ajouter des fichiers',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer ce commentaire ?',
        'max_size' => 'Taille maximale',
        'accepted_formats' => 'Formats acceptés',
        'success' => [
            'added' => 'Commentaire ajouté avec succès',
            'deleted' => 'Commentaire supprimé avec succès'
        ],
        'error' => [
            'add' => 'Une erreur est survenue lors de l\'ajout du commentaire',
            'delete' => 'Erreur lors de la suppression du commentaire',
            'file_size' => 'La taille du fichier dépasse la limite de 10 MB'
        ]
    ],
    'documents' => [
        'title' => 'Documents',
        'no_documents' => 'Aucun document pour le moment',
        'add' => 'Ajouter des documents',
        'drag_drop' => 'Glissez et déposez les fichiers ici, ou cliquez pour sélectionner',
        'submit' => 'Télécharger',
        'max_size' => 'Taille maximale : :size',
        'accepted_formats' => 'Formats acceptés : :formats',
        'errors' => [
            'file_type' => 'Type de fichier non autorisé',
            'file_size' => 'La taille du fichier dépasse la limite de 10 MB'
        ]
    ]
];
