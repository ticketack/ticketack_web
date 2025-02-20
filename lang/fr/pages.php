<?php

return [
    'comments' => [
        'title' => 'Commentaires',
        'no_comments' => 'Aucun commentaire pour le moment',
        'add' => 'Ajouter un commentaire',
        'placeholder' => 'Écrivez votre commentaire ici...',
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
    'dashboard' => [
        'title' => 'Tableau de bord',
        'statistics' => 'Statistiques',
        'total_equipment' => 'Nombre total d\'Équipements',
        'total_users' => 'Nombre total d\'Utilisateurs',
        'equipment_stats' => 'Statistiques d\'Équipements',
        'most_tickets' => 'Équipements avec le plus de tickets',
        'least_tickets' => 'Équipements avec le moins de tickets',
        'ticket_count' => 'Nombre de tickets : :count',
        'ticket_stats' => 'Statistiques des tickets',
        'total_tickets' => 'Nombre total de tickets',
        'by_status' => 'Par statut',
        'by_priority' => 'Par priorité',
        'tickets_over_time' => 'Évolution des tickets sur les 30 derniers jours'
    ],
    'auth' => [
        'login' => [
            'title' => 'Connexion',
            'email' => 'Email',
            'password' => 'Mot de passe',
            'remember_me' => 'Se souvenir de moi',
            'forgot_password' => 'Mot de passe oublié ?',
            'submit' => 'Se connecter'
        ],
        'confirm_password' => [
            'title' => 'Confirmer le mot de passe',
            'text' => 'Cette zone est sécurisée. Veuillez confirmer votre mot de passe avant de continuer.',
            'password' => 'Mot de passe',
            'confirm' => 'Confirmer'
        ],
        'forgot_password' => [
            'title' => 'Mot de passe oublié',
            'text' => 'Vous avez oublié votre mot de passe ? Aucun problème. Indiquez-nous votre adresse e-mail et nous vous enverrons un lien de réinitialisation.',
            'email' => 'Email',
            'reset_link' => 'Envoyer le lien'
        ],
        'reset_password' => [
            'title' => 'Réinitialiser le mot de passe',
            'email' => 'Email',
            'password' => 'Mot de passe',
            'confirm_password' => 'Confirmer le mot de passe',
            'reset' => 'Réinitialiser'
        ],
        'register' => [
            'title' => 'Inscription',
            'name' => 'Nom',
            'email' => 'Email',
            'password' => 'Mot de passe',
            'confirm_password' => 'Confirmer le mot de passe',
            'already_registered' => 'Déjà inscrit ?',
            'register' => 'S\'inscrire'
        ],
        'verify_email' => [
            'title' => 'Vérifier l\'adresse e-mail',
            'text' => 'Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer ? Si vous n\'avez pas reçu l\'e-mail, nous vous en enverrons un autre avec plaisir.',
            'new_verification' => 'Un nouveau lien de vérification a été envoyé',
            'resend' => 'Renvoyer l\'e-mail de vérification',
            'logout' => 'Déconnexion'
        ]
    ],
    'profile' => [
        'title' => 'Profil',
        'api_token' => [
            'title' => 'Token API',
            'description' => 'Votre token API pour l\'authentification des requêtes API.',
            'copy' => 'Copier',
            'copied' => 'Copié !'
        ],
        'update_info' => [
            'title' => 'Informations du profil',
            'text' => 'Mettez à jour les informations de profil et l\'adresse e-mail de votre compte.',
            'name' => 'Nom',
            'email' => 'Email',
            'save' => 'Enregistrer',
            'saved' => 'Enregistré.'
        ],
        'update_password' => [
            'title' => 'Mettre à jour le mot de passe',
            'text' => 'Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.',
            'current_password' => 'Mot de passe actuel',
            'new_password' => 'Nouveau mot de passe',
            'confirm_password' => 'Confirmer le mot de passe',
            'save' => 'Enregistrer'
        ],
        'delete_account' => [
            'title' => 'Supprimer le compte',
            'text' => 'Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées.',
            'button' => 'Supprimer le compte',
            'confirm_title' => 'Êtes-vous sûr de vouloir supprimer votre compte ?',
            'confirm_text' => 'Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Veuillez saisir votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.',
            'password' => 'Mot de passe',
            'cancel' => 'Annuler',
            'confirm' => 'Supprimer le compte'
        ]
    ],
    'equipment' => [
        'title' => 'Équipements',
        'create' => [
            'title' => 'Créer un équipement',
            'designation' => 'Désignation',
            'marque' => 'Marque',
            'modele' => 'Modèle',
            'image' => 'Image',
            'icone' => 'Icône',
            'save' => 'Enregistrer',
            'cancel' => 'Annuler'
        ],
        'edit' => [
            'title' => 'Modifier l\'équipement',
            'designation' => 'Désignation',
            'marque' => 'Marque',
            'modele' => 'Modèle',
            'image' => 'Image',
            'icone' => 'Icône',
            'save' => 'Enregistrer',
            'cancel' => 'Annuler'
        ],
        'delete' => [
            'confirm_title' => 'Supprimer l\'équipement',
            'confirm_text' => 'Êtes-vous sûr de vouloir supprimer cet équipement ?',
            'cancel' => 'Annuler',
            'confirm' => 'Supprimer'
        ]
    ],
    'equipements' => [
        'title' => 'Équipements',
        'index' => [
            'title' => 'Gestion des équipements',
            'new_equipment' => 'Nouvel équipement',
            'list' => 'Liste des équipements'
        ],
        'create' => [
            'title' => 'Créer un équipement'
        ],
        'edit' => [
            'title' => 'Modifier l\'équipement'
        ]
    ],
    'roles' => [
        'title' => 'Rôles',
        'index' => [
            'title' => 'Gestion des rôles',
            'new_role' => 'Nouveau rôle',
            'name' => 'Nom',
            'description' => 'Description',
            'permissions' => 'Permissions',
            'actions' => 'Actions',
            'edit' => 'Modifier',
            'delete' => 'Supprimer',
            'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer le rôle ":name" ?'
        ],
        'create' => [
            'title' => 'Créer un rôle',
            'name' => 'Nom',
            'description' => 'Description',
            'permissions' => 'Permissions',
            'save' => 'Enregistrer',
            'cancel' => 'Annuler',
            'create' => 'Créer'
        ],
        'edit' => [
            'title' => 'Modifier le rôle',
            'name' => 'Nom',
            'description' => 'Description',
            'permissions' => 'Permissions',
            'save' => 'Enregistrer',
            'cancel' => 'Annuler'
        ],
        'show' => [
            'title' => 'Détails du rôle',
            'name' => 'Nom',
            'description' => 'Description',
            'no_description' => 'Aucune description',
            'permissions' => 'Permissions',
            'edit' => 'Modifier',
            'back' => 'Retour'
        ],
        'delete' => [
            'confirm_title' => 'Supprimer le rôle',
            'confirm_text' => 'Êtes-vous sûr de vouloir supprimer ce rôle ?',
            'cancel' => 'Annuler',
            'confirm' => 'Supprimer'
        ]
    ],
    'users' => [
        'title' => 'Utilisateurs',
        'index' => [
            'title' => 'Gestion des utilisateurs',
            'new_user' => 'Nouvel utilisateur',
            'name' => 'Nom',
            'email' => 'Email',
            'roles' => 'Rôles',
            'actions' => 'Actions',
            'edit' => 'Modifier',
            'delete' => 'Supprimer',
            'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer l\'utilisateur "{name}" ?'
        ],
        'create' => [
            'title' => 'Créer un utilisateur',
            'name' => 'Nom',
            'email' => 'Email',
            'password' => 'Mot de passe',
            'confirm_password' => 'Confirmer le mot de passe',
            'role' => 'Rôle',
            'save' => 'Enregistrer',
            'cancel' => 'Annuler'
        ],
        'edit' => [
            'title' => 'Modifier l\'utilisateur',
            'name' => 'Nom',
            'email' => 'Email',
            'role' => 'Rôle',
            'save' => 'Enregistrer',
            'cancel' => 'Annuler'
        ],
        'delete' => [
            'confirm_title' => 'Supprimer l\'utilisateur',
            'confirm_text' => 'Êtes-vous sûr de vouloir supprimer cet utilisateur ?',
            'cancel' => 'Annuler',
            'confirm' => 'Supprimer'
        ]
    ],
    'settings' => [
        'title' => 'Paramètres',
        'company_name' => 'Nom de l\'entreprise',
        'logo' => 'Logo',
        'language' => 'Langue',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'delete_logo' => [
            'title' => 'Supprimer le logo',
            'text' => 'Êtes-vous sûr de vouloir supprimer le logo ?',
            'cancel' => 'Annuler',
            'confirm' => 'Supprimer'
        ]
    ],


    'tickets' => [
        'visibility' => [
            'public_label' => 'Ticket public',
            'tooltip' => 'Un ticket privé ne pourra être consulté que par l\'auteur, la personne à qui il est assigné et les administrateurs'
        ],
        'create' => [
            'title' => 'Créer un nouveau ticket',
            'step1' => [
                'title' => 'Étape 1 : Informations du ticket',
                'completed' => 'Complété'
            ],
            'step2' => [
                'title' => 'Étape 2 : Documents',
                'drag_files' => 'Glissez et déposez des fichiers ici, ou cliquez pour sélectionner',
                'max_size' => 'Taille maximale du fichier : 10MB'
            ],
            'fields' => [
                'title' => 'Titre',
                'description' => 'Description',
                'priority' => 'Priorité',
                'category' => 'Catégorie',
                'due_date' => 'Date d\'échéance (optionnelle)',
                'equipment' => 'Équipement concerné'
            ],
            'priority' => [
                'low' => 'Basse',
                'medium' => 'Moyenne',
                'high' => 'Haute',
                'critical' => 'Critique'
            ],
            'buttons' => [
                'cancel' => 'Annuler',
                'next' => 'Suite',
                'modify' => 'Modifier',
                'finish' => 'Terminer'
            ]
        ]
    ],

    'api_doc' => [
        'title' => 'Documentation API',
        'summary' => 'Sommaire',
        'introduction' => [
            'title' => 'Introduction',
            'description' => 'Bienvenue dans la documentation de notre API. Cette API vous permet d\'interagir avec notre système de manière programmatique.'
        ],
        'authentication' => [
            'title' => 'Authentification',
            'description' => 'L\'API utilise l\'authentification par token. Vous pouvez obtenir votre token API depuis votre page de profil. Incluez ce token dans l\'en-tête Authorization de vos requêtes.'
        ],
        'endpoints' => [
            'title' => 'Points de terminaison',
            'headers' => 'En-têtes',
            'body' => 'Corps de la requête',
            'no_parameters' => 'Cet endpoint n\'accepte aucun paramètre.',
            'parameters' => 'Paramètres',
            'response_format' => 'Format de réponse',
            'table_headers' => [
                'request_parameters' => 'Paramètres de requête',
                'name' => 'Nom',
                'type' => 'Type',
                'description' => 'Description',
                'required' => 'Requis'
            ],
            'token_usage' => 'Utilisation du token',
            'api_token_info' => 'Le token API se trouve dans votre profil utilisateur, accessible via le menu déroulant en haut à droite de l application.',
            'your_personal_token' => 'votre_token_api_personnel',
            'example_values' => [
                'name' => 'John Doe',
                'email' => 'utilisateur@exemple.com',
                'password' => 'mot_de_passe',
                'workstation_name' => 'Station de travail principale',
                'workstation_brand' => 'HP',
                'workstation_model' => 'Z4 G4',
                'workstation_image' => 'poste_travail.jpg'
            ],
            'parameters_description' => [
                'children' => 'Inclure les équipements enfants (yes/no)',
                'children_required' => 'Non'
            ],
            'form_labels' => [
                'email' => 'Adresse email de l\'utilisateur',
                'password' => 'Mot de passe de l\'utilisateur',
                'device_name' => 'Nom de l\'appareil ou de l\'application qui utilise le token'
            ],
            'auth' => [
                'title' => 'Authentification',
                'login' => [
                    'description' => 'Authentifiez-vous avec votre email et mot de passe pour obtenir un token d\'accès. Ce token sera nécessaire pour toutes les autres requêtes API.',
                    'token_instructions' => 'Pour utiliser l\'API, vous devez utiliser votre token API personnel disponible dans votre profil utilisateur. Rendez-vous dans votre profil et copiez le token API qui s\'y trouve.',
                    'parameters' => [
                        'email' => 'Adresse email de l\'utilisateur',
                        'password' => 'Mot de passe de l\'utilisateur'
                    ]
                ],
                'register' => [
                    'description' => 'Créez un nouveau compte utilisateur.'
                ],
                'logout' => [
                    'description' => 'Déconnectez-vous et invalidez le token actuel.'
                ],
                'user' => [
                    'description' => 'Récupérez les informations de l\'utilisateur connecté.'
                ]
            ],
            'equipment' => [
                'title' => 'Équipements',
                'list' => [
                    'description' => 'Récupère la liste complète des équipements avec leur structure hiérarchique. Chaque équipement inclut ses équipements enfants dans le champ all_children.'
                ],
                'create' => [
                    'description' => 'Créez un nouvel équipement'
                ],
                'show' => [
                    'description' => 'Récupère les détails d\'un équipement spécifique. Utilisez le paramètre children=yes/no pour inclure ou non les équipements enfants dans la réponse.',
                    'parameters' => [
                        'children' => 'Inclure les équipements enfants (yes/no)'
                    ]
                ],
                'update' => [
                    'description' => 'Mettez à jour les informations d\'un équipement existant'
                ],
                'delete' => [
                    'description' => 'Supprimez un équipement existant'
                ]
            ],
            'tickets' => [
                'title' => 'Tickets',
                'list' => [
                    'description' => 'Récupère la liste complète des tickets.'
                ],
                'create' => [
                    'description' => 'Créez un nouveau ticket'
                ],
                'show' => [
                    'description' => 'Récupère les détails d\'un ticket spécifique.'
                ],
                'update' => [
                    'description' => 'Mettez à jour les informations d\'un ticket existant'
                ],
                'delete' => [
                    'description' => 'Supprimez un ticket existant'
                ]
            ]
        ],
        'response_codes' => [
            'title' => 'Codes de réponse',
            'code' => 'Code',
            'meaning' => 'Signification',
            'success' => 'Requête réussie',
            'created' => 'Ressource créée avec succès',
            'bad_request' => 'Requête invalide',
            'unauthorized' => 'Non authentifié',
            'forbidden' => 'Accès interdit',
            'not_found' => 'Ressource non trouvée',
            'validation_error' => 'Erreur de validation',
            'server_error' => 'Erreur serveur'
        ]
    ]
];
