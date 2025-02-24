<?php

return [
    'title' => 'API-Dokumentation',
    'summary' => 'Zusammenfassung',
    'introduction' => [
        'title' => 'Einführung',
        'description' => 'Willkommen in unserer API-Dokumentation. Diese API ermöglicht es Ihnen, mit unserem Ticket-Management-System zu interagieren.'
    ],
    'authentication' => [
        'title' => 'Authentifizierung',
        'description' => 'Die API verwendet Bearer-Token-Authentifizierung.',
        'token_header_instruction' => 'Fügen Sie das Token im Authorization-Header Ihrer Anfragen hinzu.'
    ],
    'endpoints' => [
        'title' => 'Endpunkte',
        'parameters' => 'Parameter',
        'token_usage' => 'Token-Verwendung',
        'your_personal_token' => 'ihr_persönliches_token',
        'api_token_info' => 'Sie können Ihr API-Token durch Anmeldung in Ihrem Konto erhalten.',
        'auth' => [
            'title' => 'Authentifizierung',
            'login' => [
                'token_header_instruction' => 'Zur Authentifizierung senden Sie Ihre Anmeldedaten, um ein Zugriffstoken zu erhalten.'
            ],
            'register' => [
                'description' => 'Erstellen Sie ein neues Benutzerkonto.'
            ],
            'logout' => [
                'description' => 'Melden Sie den Benutzer ab und widerrufen Sie das Token.'
            ],
            'user' => [
                'description' => 'Rufen Sie die Informationen des angemeldeten Benutzers ab.'
            ]
        ],
        'equipment' => [
            'title' => 'Ausstattung',
            'create' => [
                'description' => 'Neue Ausstattung erstellen.'
            ],
            'show' => [
                'description' => 'Details einer bestimmten Ausstattung abrufen.'
            ],
            'update' => [
                'description' => 'Ausstattungsinformationen aktualisieren.'
            ],
            'delete' => [
                'description' => 'Ausstattung löschen.'
            ]
        ],
        'tickets' => [
            'title' => 'Tickets',
            'list' => [
                'description' => 'Liste der Tickets mit Filtermöglichkeiten abrufen.'
            ],
            'create' => [
                'description' => 'Neues Ticket erstellen.'
            ],
            'show' => [
                'description' => 'Details eines bestimmten Tickets abrufen.'
            ],
            'update' => [
                'description' => 'Ticket-Informationen aktualisieren.'
            ],
            'delete' => [
                'description' => 'Ticket löschen.'
            ]
        ],
        'example_values' => [
            'name' => 'Max Mustermann',
            'email' => 'max@beispiel.de',
            'password' => 'passwort123'
        ],
        'form_labels' => [
            'email' => 'E-Mail-Adresse des Benutzers',
            'password' => 'Benutzerpasswort'
        ],
        'table_headers' => [
            'name' => 'Name',
            'type' => 'Typ',
            'description' => 'Beschreibung',
            'request_parameters' => 'Anfrageparameter'
        ]
    ],
    'response_codes' => [
        'title' => 'Antwort-Codes',
        'success' => 'Anfrage erfolgreich',
        'created' => 'Ressource erfolgreich erstellt',
        'bad_request' => 'Ungültige Anfrage',
        'unauthorized' => 'Nicht autorisiert',
        'forbidden' => 'Zugriff verboten',
        'not_found' => 'Ressource nicht gefunden',
        'validation_error' => 'Validierungsfehler',
        'server_error' => 'Serverfehler'
    ]
];
