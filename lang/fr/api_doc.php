<?php

return [
    'title' => 'Documentation API',
    'summary' => 'Sommaire',
    'introduction' => [
        'title' => 'Introduction',
        'description' => 'Bienvenue dans la documentation de notre API. Cette API vous permet d\'interagir avec notre système de gestion de tickets.'
    ],
    'authentication' => [
        'title' => 'Authentification',
        'description' => 'L\'API utilise l\'authentification par token Bearer.',
        'token_header_instruction' => 'Ajoutez le token dans l\'en-tête Authorization de vos requêtes.'
    ],
    'endpoints' => [
        'title' => 'Points de terminaison',
        'parameters' => 'Paramètres',
        'token_usage' => 'Utilisation du Token',
        'your_personal_token' => 'votre_token_personnel',
        'api_token_info' => 'Vous pouvez obtenir votre token API en vous connectant à votre compte.',
        'auth' => [
            'title' => 'Authentification',
            'login' => [
                'token_header_instruction' => 'Pour vous authentifier, envoyez vos identifiants pour recevoir un token d\'accès.'
            ],
            'register' => [
                'description' => 'Créer un nouveau compte utilisateur.'
            ],
            'logout' => [
                'description' => 'Déconnecter l\'utilisateur et révoquer le token.'
            ],
            'user' => [
                'description' => 'Récupérer les informations de l\'utilisateur connecté.'
            ]
        ],
        'equipment' => [
            'title' => 'Équipements',
            'create' => [
                'description' => 'Créer un nouvel équipement.'
            ],
            'show' => [
                'description' => 'Récupérer les détails d\'un équipement spécifique.'
            ],
            'update' => [
                'description' => 'Mettre à jour les informations d\'un équipement.'
            ],
            'delete' => [
                'description' => 'Supprimer un équipement.'
            ]
        ],
        'tickets' => [
            'title' => 'Tickets',
            'list' => [
                'description' => 'Récupérer la liste des tickets avec possibilité de filtrage.'
            ],
            'create' => [
                'description' => 'Créer un nouveau ticket.'
            ],
            'show' => [
                'description' => 'Récupérer les détails d\'un ticket spécifique.'
            ],
            'update' => [
                'description' => 'Mettre à jour les informations d\'un ticket.'
            ],
            'delete' => [
                'description' => 'Supprimer un ticket.'
            ]
        ],
        'example_values' => [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'motdepasse123'
        ],
        'form_labels' => [
            'email' => 'Adresse email de l\'utilisateur',
            'password' => 'Mot de passe de l\'utilisateur'
        ],
        'table_headers' => [
            'name' => 'Nom',
            'type' => 'Type',
            'description' => 'Description',
            'request_parameters' => 'Paramètres de la requête'
        ]
    ],
    'response_codes' => [
        'title' => 'Codes de réponse',
        'success' => 'Requête réussie',
        'created' => 'Ressource créée avec succès',
        'bad_request' => 'Requête invalide',
        'unauthorized' => 'Non autorisé',
        'forbidden' => 'Accès interdit',
        'not_found' => 'Ressource non trouvée',
        'validation_error' => 'Erreur de validation',
        'server_error' => 'Erreur serveur'
    ]
];
