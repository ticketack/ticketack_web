<?php

return [
    'comments' => [
        'title' => 'Kommentare',
        'no_comments' => 'Noch keine Kommentare',
        'add' => 'Kommentar hinzufügen',
        'placeholder' => 'Schreiben Sie Ihren Kommentar hier...',
        'attachments' => 'Anhängen',
        'add_files' => 'Dateien hinzufügen',
        'confirm_delete' => 'Möchten Sie diesen Kommentar wirklich löschen?',
        'max_size' => 'maximale Größe',
        'accepted_formats' => 'Akzeptierte Formate',
        'success' => [
            'added' => 'Kommentar erfolgreich hinzugefügt',
            'deleted' => 'Kommentar erfolgreich gelöscht'
        ],
        'error' => [
            'add' => 'Fehler beim Hinzufügen des Kommentars',
            'delete' => 'Fehler beim Löschen des Kommentars'
        ]
    ],
    'dashboard' => [
        'title' => 'Dashboard',
        'statistics' => 'Statistiken',
        'total_equipment' => 'Gesamtausrüstung',
        'total_users' => 'Gesamtanzahl der Benutzer',
        'equipment_stats' => 'Ausrüstungsstatistiken',
        'most_tickets' => 'Ausrüstung mit den meisten Tickets',
        'least_tickets' => 'Ausrüstung mit den wenigsten Tickets',
        'ticket_count' => 'Ticketanzahl: :count',
        'ticket_stats' => 'Ticket-Statistiken',
        'total_tickets' => 'Gesamtanzahl der Tickets',
        'by_status' => 'Nach Status',
        'by_priority' => 'Nach Priorität',
        'tickets_over_time' => 'Ticketentwicklung der letzten 30 Tage',
        'recent_activity' => 'Letzte Aktivität'
    ],
    'auth' => [
        'login' => [
            'title' => 'Anmelden',
            'email' => 'E-Mail',
            'password' => 'Passwort',
            'remember_me' => 'Angemeldet bleiben',
            'forgot_password' => 'Passwort vergessen?',
            'submit' => 'Anmelden'
        ],
        'confirm_password' => [
            'title' => 'Passwort bestätigen',
            'text' => 'Dieser Bereich ist gesichert. Bitte bestätigen Sie Ihr Passwort, bevor Sie fortfahren.',
            'password' => 'Passwort',
            'confirm' => 'Bestätigen'
        ],
        'forgot_password' => [
            'title' => 'Passwort vergessen',
            'text' => 'Passwort vergessen? Kein Problem. Teilen Sie uns einfach Ihre E-Mail-Adresse mit und wir senden Ihnen einen Link zum Zurücksetzen des Passworts.',
            'email' => 'E-Mail',
            'reset_link' => 'Link senden'
        ],
        'reset_password' => [
            'title' => 'Passwort zurücksetzen',
            'email' => 'E-Mail',
            'password' => 'Passwort',
            'confirm_password' => 'Passwort bestätigen',
            'reset' => 'Zurücksetzen'
        ],
        'register' => [
            'title' => 'Registrieren',
            'name' => 'Name',
            'email' => 'E-Mail',
            'password' => 'Passwort',
            'confirm_password' => 'Passwort bestätigen',
            'already_registered' => 'Bereits registriert?',
            'register' => 'Registrieren'
        ],
        'verify_email' => [
            'title' => 'E-Mail-Adresse bestätigen',
            'text' => 'Danke für die Registrierung! Bevor Sie beginnen, könnten Sie bitte Ihre E-Mail-Adresse bestätigen, indem Sie auf den Link klicken, den wir Ihnen gerade gesendet haben? Falls Sie keine E-Mail erhalten haben, senden wir Ihnen gerne eine neue.',
            'new_verification' => 'Ein neuer Bestätigungslink wurde gesendet',
            'resend' => 'Bestätigungs-E-Mail erneut senden',
            'logout' => 'Abmelden'
        ]
    ],
    'profile' => [
        'title' => 'Profil',
        'api_token' => [
            'title' => 'API-Token',
            'description' => 'Ihr API-Token für die API-Authentifizierung.',
            'copy' => 'Kopieren',
            'copied' => 'Kopiert!'
        ],
        'update_info' => [
            'title' => 'Profilinformationen',
            'text' => 'Aktualisieren Sie Ihre Profilinformationen und E-Mail-Adresse.',
            'name' => 'Name',
            'email' => 'E-Mail',
            'save' => 'Speichern',
            'saved' => 'Gespeichert.'
        ],
        'update_password' => [
            'title' => 'Passwort aktualisieren',
            'text' => 'Stellen Sie sicher, dass Ihr Konto ein langes, zufälliges Passwort verwendet, um sicher zu bleiben.',
            'current_password' => 'Aktuelles Passwort',
            'new_password' => 'Neues Passwort',
            'confirm_password' => 'Passwort bestätigen',
            'save' => 'Speichern'
        ],
        'delete_account' => [
            'title' => 'Konto löschen',
            'text' => 'Sobald Ihr Konto gelöscht ist, werden alle Ressourcen und Daten dauerhaft gelöscht.',
            'button' => 'Konto löschen',
            'confirm_title' => 'Sind Sie sicher, dass Sie Ihr Konto löschen möchten?',
            'confirm_text' => 'Sobald Ihr Konto gelöscht ist, werden alle Ressourcen und Daten dauerhaft gelöscht. Bitte geben Sie Ihr Passwort ein, um zu bestätigen, dass Sie Ihr Konto dauerhaft löschen möchten.',
            'password' => 'Passwort',
            'cancel' => 'Abbrechen',
            'confirm' => 'Konto löschen'
        ]
    ],
    'equipment' => [
        'title' => 'Ausrüstung',
        'create' => [
            'title' => 'Ausrüstung erstellen',
            'designation' => 'Bezeichnung',
            'marque' => 'Marke',
            'modele' => 'Modell',
            'image' => 'Bild',
            'icone' => 'Symbol',
            'save' => 'Speichern',
            'cancel' => 'Abbrechen'
        ],
        'edit' => [
            'title' => 'Ausrüstung bearbeiten',
            'designation' => 'Bezeichnung',
            'marque' => 'Marke',
            'modele' => 'Modell',
            'image' => 'Bild',
            'icone' => 'Symbol',
            'save' => 'Speichern',
            'cancel' => 'Abbrechen'
        ],
        'delete' => [
            'confirm_title' => 'Ausrüstung löschen',
            'confirm_text' => 'Sind Sie sicher, dass Sie diese Ausrüstung löschen möchten?',
            'cancel' => 'Abbrechen',
            'confirm' => 'Löschen'
        ],
        'index' => [
            'title' => 'Ausrüstungsverwaltung',
            'new_equipment' => 'Neue Ausrüstung',
            'list' => 'Ausrüstungsliste'
        ]
    ],
    'equipements' => [
        'title' => 'Ausrüstung',
        'create' => [
            'title' => 'Ausrüstung erstellen',
            'designation' => 'Bezeichnung',
            'marque' => 'Marke',
            'modele' => 'Modell',
            'image' => 'Bild',
            'icone' => 'Symbol',
            'save' => 'Speichern',
            'cancel' => 'Abbrechen'
        ],
        'edit' => [
            'title' => 'Ausrüstung bearbeiten',
            'designation' => 'Bezeichnung',
            'marque' => 'Marke',
            'modele' => 'Modell',
            'image' => 'Bild',
            'icone' => 'Symbol',
            'save' => 'Speichern',
            'cancel' => 'Abbrechen'
        ],
        'delete' => [
            'confirm_title' => 'Ausrüstung löschen',
            'confirm_text' => 'Sind Sie sicher, dass Sie diese Ausrüstung löschen möchten?',
            'cancel' => 'Abbrechen',
            'confirm' => 'Löschen'
        ],
        'index' => [
            'title' => 'Ausrüstungsverwaltung',
            'new_equipment' => 'Neue Ausrüstung',
            'list' => 'Ausrüstungsliste'
        ]
    ],
    'roles' => [
        'title' => 'Rollen',
        'index' => [
            'title' => 'Rollenverwaltung',
            'new_role' => 'Neue Rolle',
            'name' => 'Name',
            'description' => 'Beschreibung',
            'permissions' => 'Berechtigungen',
            'actions' => 'Aktionen',
            'edit' => 'Bearbeiten',
            'delete' => 'Löschen',
            'confirm_delete' => 'Sind Sie sicher, dass Sie die Rolle ":name" löschen möchten?'
        ],
        'create' => [
            'title' => 'Rolle erstellen',
            'name' => 'Name',
            'description' => 'Beschreibung',
            'permissions' => 'Berechtigungen',
            'save' => 'Speichern',
            'cancel' => 'Abbrechen',
            'create' => 'Erstellen'
        ],
        'edit' => [
            'title' => 'Rolle bearbeiten',
            'name' => 'Name',
            'description' => 'Beschreibung',
            'permissions' => 'Berechtigungen',
            'save' => 'Speichern',
            'cancel' => 'Abbrechen'
        ],
        'show' => [
            'title' => 'Rollendetails',
            'name' => 'Name',
            'description' => 'Beschreibung',
            'no_description' => 'Keine Beschreibung',
            'permissions' => 'Berechtigungen',
            'edit' => 'Bearbeiten',
            'back' => 'Zurück'
        ],
        'delete' => [
            'confirm_title' => 'Rolle löschen',
            'confirm_text' => 'Sind Sie sicher, dass Sie diese Rolle löschen möchten?',
            'cancel' => 'Abbrechen',
            'confirm' => 'Löschen'
        ]
    ],
    'users' => [
        'title' => 'Benutzer',
        'index' => [
            'title' => 'Benutzerverwaltung',
            'new_user' => 'Neuer Benutzer',
            'name' => 'Name',
            'email' => 'E-Mail',
            'roles' => 'Rollen',
            'actions' => 'Aktionen',
            'edit' => 'Bearbeiten',
            'delete' => 'Löschen',
            'confirm_delete' => 'Sind Sie sicher, dass Sie den Benutzer "{name}" löschen möchten?'
        ],
        'create' => [
            'title' => 'Benutzer erstellen',
            'name' => 'Name',
            'email' => 'E-Mail',
            'password' => 'Passwort',
            'confirm_password' => 'Passwort bestätigen',
            'role' => 'Rolle',
            'save' => 'Speichern',
            'cancel' => 'Abbrechen'
        ],
        'edit' => [
            'title' => 'Benutzer bearbeiten',
            'name' => 'Name',
            'email' => 'E-Mail',
            'role' => 'Rolle',
            'save' => 'Speichern',
            'cancel' => 'Abbrechen'
        ],
        'delete' => [
            'confirm_title' => 'Benutzer löschen',
            'confirm_text' => 'Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?',
            'cancel' => 'Abbrechen',
            'confirm' => 'Löschen'
        ]
    ],
    'settings' => [
        'title' => 'Einstellungen',
        'company_name' => 'Firmenname',
        'logo' => 'Logo',
        'language' => 'Sprache',
        'save' => 'Speichern',
        'cancel' => 'Abbrechen',
        'delete_logo' => [
            'title' => 'Logo löschen',
            'text' => 'Sind Sie sicher, dass Sie das Logo löschen möchten?',
            'cancel' => 'Abbrechen',
            'confirm' => 'Löschen'
        ]
    ],
    'tickets' => [
        'visibility' => [
            'public_label' => 'Öffentliches Ticket',
            'tooltip' => 'Ein privates Ticket kann nur vom Autor, der zugewiesenen Person und Administratoren eingesehen werden'
        ],
        'create' => [
            'title' => 'Neues Ticket erstellen',
            'step1' => [
                'title' => 'Schritt 1: Ticket-Informationen',
                'completed' => 'Abgeschlossen'
            ],
            'step2' => [
                'title' => 'Schritt 2: Dokumente',
                'drag_files' => 'Dateien hierher ziehen oder klicken zum Auswählen',
                'max_size' => 'Maximale Dateigröße: 10MB'
            ],
            'fields' => [
                'title' => 'Titel',
                'description' => 'Beschreibung',
                'priority' => 'Priorität',
                'category' => 'Kategorie',
                'due_date' => 'Fälligkeitsdatum (optional)',
                'equipment' => 'Betroffene Ausrüstung'
            ],
            'priority' => [
                'low' => 'Niedrig',
                'medium' => 'Mittel',
                'high' => 'Hoch',
                'critical' => 'Kritisch'
            ],
            'buttons' => [
                'cancel' => 'Abbrechen',
                'next' => 'Weiter',
                'modify' => 'Ändern',
                'finish' => 'Fertig'
            ]
        ]
    ],

    'api_doc' => [
        'title' => 'API-Dokumentation',
        'summary' => 'Inhaltsverzeichnis',
        'introduction' => [
            'title' => 'Einführung',
            'description' => 'Willkommen in unserer API-Dokumentation. Diese API ermöglicht es Ihnen, programmatisch mit unserem System zu interagieren.'
        ],
        'authentication' => [
            'title' => 'Authentifizierung',
            'description' => 'Die API verwendet Bearer Token Authentifizierung. Sie müssen Ihren Token im Authorization Header jeder Anfrage einbinden.',
            'token_header_instruction' => 'Fügen Sie Ihren persönlichen API-Token in den Authorization-Header aller Ihrer API-Anfragen ein:'
        ],
        'endpoints' => [
            'title' => 'Endpunkte',
            'headers' => 'Header',
            'body' => 'Anfragekörper',
            'no_parameters' => 'Dieser Endpunkt akzeptiert keine Parameter.',
            'parameters' => 'Parameter',
            'response_format' => 'Antwortformat',
            'table_headers' => [
                'request_parameters' => 'Anfrageparameter',
                'name' => 'Name',
                'type' => 'Typ',
                'description' => 'Beschreibung',
                'required' => 'Erforderlich'
            ],
            'token_usage' => 'Token-Verwendung',
            'api_token_info' => 'Der API-Token finden Sie in Ihrem Benutzerprofil, das über das Dropdown-Menü oben rechts in der Anwendung zugänglich ist.',
            'your_personal_token' => 'ihr_persönlicher_api_token',
            'example_values' => [
                'name' => 'Max Mustermann',
                'email' => 'benutzer@beispiel.de',
                'password' => 'passwort',
                'workstation_name' => 'Haupt-Arbeitsstation',
                'workstation_brand' => 'HP',
                'workstation_model' => 'Z4 G4',
                'workstation_image' => 'arbeitsstation.jpg'
            ],
            'parameters_description' => [
                'children' => 'Untergeordnete Geräte einbeziehen (yes/no)',
                'children_required' => 'Nein'
            ],
            'form_labels' => [
                'email' => 'E-Mail-Adresse des Benutzers',
                'password' => 'Passwort des Benutzers',
                'device_name' => 'Name des Geräts oder der Anwendung, die den Token verwendet'
            ],
            'auth' => [
                'title' => 'Authentifizierung',
                'login' => [
                    'description' => 'Authentifizieren Sie sich mit Ihrer E-Mail und Ihrem Passwort, um einen Zugriffstoken zu erhalten. Dieser Token wird für alle weiteren API-Anfragen benötigt.',
                    'token_instructions' => 'Um die API zu nutzen, müssen Sie Ihren persönlichen API-Token verwenden, der in Ihrem Benutzerprofil verfügbar ist. Gehen Sie zu Ihrem Profil und kopieren Sie den dort befindlichen API-Token.',
                    'parameters' => [
                        'email' => 'E-Mail-Adresse des Benutzers',
                        'password' => 'Passwort des Benutzers'
                    ]
                ],
                'register' => [
                    'description' => 'Neues Benutzerkonto erstellen'
                ],
                'logout' => [
                    'description' => 'Abmelden und aktuellen Token widerrufen'
                ],
                'user' => [
                    'description' => 'Informationen des angemeldeten Benutzers abrufen'
                ]
            ],
            'equipment' => [
                'title' => 'Ausrüstung',
                'list' => [
                    'description' => 'Ruft die vollständige Liste der Ausrüstungen mit ihrer hierarchischen Struktur ab. Jede Ausrüstung enthält ihre untergeordneten Ausrüstungen im Feld all_children.'
                ],
                'create' => [
                    'description' => 'Neue Ausrüstung erstellen'
                ],
                'show' => [
                    'description' => 'Ruft die Details einer bestimmten Ausrüstung ab. Verwenden Sie den Parameter children, um untergeordnete Ausrüstungen in der Antwort ein- oder auszuschließen.',
                    'parameters' => [
                        'children' => 'Untergeordnete Ausrüstungen einschließen (yes/no)'
                    ]
                ],
                'update' => [
                    'description' => 'Informationen einer bestehenden Ausrüstung aktualisieren'
                ],
                'delete' => [
                    'description' => 'Bestehende Ausrüstung löschen'
                ]
            ],
            'tickets' => [
                'title' => 'Tickets',
                'list' => [
                    'description' => 'Ruft die vollständige Liste der Tickets ab.'
                ],
                'create' => [
                    'description' => 'Neues Ticket erstellen'
                ],
                'show' => [
                    'description' => 'Ruft die Details eines bestimmten Tickets ab.'
                ],
                'update' => [
                    'description' => 'Informationen eines bestehenden Tickets aktualisieren'
                ],
                'delete' => [
                    'description' => 'Bestehendes Ticket löschen'
                ]
            ]
        ],
        'response_codes' => [
            'title' => 'Antwort-Codes',
            'code' => 'Code',
            'meaning' => 'Bedeutung',
            'success' => 'Anfrage erfolgreich',
            'created' => 'Ressource erfolgreich erstellt',
            'bad_request' => 'Ungültige Anfrage',
            'unauthorized' => 'Nicht authentifiziert',
            'forbidden' => 'Nicht autorisiert',
            'not_found' => 'Ressource nicht gefunden',
            'validation_error' => 'Validierungsfehler',
            'server_error' => 'Serverfehler'
        ]
    ]
];
