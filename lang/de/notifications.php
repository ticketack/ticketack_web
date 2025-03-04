<?php

return [
    'preferences' => [
        'title' => 'Benachrichtigungseinstellungen',
        'description' => 'Konfigurieren Sie Ihre Einstellungen für In-App-, E-Mail- und SMS-Benachrichtigungen.',
        'manage' => 'Einstellungen verwalten',
        'view_history' => 'Benachrichtigungsverlauf anzeigen',
        'contact_info' => 'Kontaktinformationen',
        'phone_number' => 'Telefonnummer (für SMS)',
        'phone_format' => 'Empfohlenes internationales Format (z.B. +49123456789)',
        'api_key' => 'SMS-API-Schlüssel (optional)',
        'api_key_description' => 'Wenn leer, wird der Standard-API-Schlüssel verwendet',
        'save' => 'Einstellungen speichern',
        'back_to_profile' => 'Zurück zum Profil',
        'updated' => 'Benachrichtigungseinstellungen erfolgreich aktualisiert',
    ],
    'logs' => [
        'title' => 'Benachrichtigungsverlauf',
        'no_notifications' => 'Keine Benachrichtigungen gefunden',
        'all_channels' => 'Alle Kanäle',
        'all_statuses' => 'Alle Status',
        'unread' => 'Ungelesen',
        'read' => 'Gelesen',
        'mark_all_read' => 'Alle als gelesen markieren',
        'mark_as_read' => 'Als gelesen markieren',
        'already_read' => 'Bereits gelesen',
        'new' => 'Neu',
        'marked_as_read' => 'Benachrichtigung als gelesen markiert',
        'all_marked_as_read' => 'Alle Benachrichtigungen wurden als gelesen markiert',
    ],
    'types' => [
        'ticket_created' => [
            'name' => 'Ticket erstellt',
            'description' => 'Benachrichtigung gesendet, wenn ein neues Ticket erstellt wird',
        ],
        'ticket_time_tracked' => [
            'name' => 'Zeit für Ticket erfasst',
            'description' => 'Benachrichtigung gesendet, wenn Zeit für ein Ticket erfasst wird',
        ],
        'ticket_commented' => [
            'name' => 'Ticket kommentiert',
            'description' => 'Benachrichtigung gesendet, wenn ein Kommentar zu einem Ticket hinzugefügt wird',
        ],
        'ticket_status_changed' => [
            'name' => 'Ticket-Status geändert',
            'description' => 'Benachrichtigung gesendet, wenn der Status eines Tickets geändert wird',
        ],
        'ticket_assigned' => [
            'name' => 'Ticket zugewiesen',
            'description' => 'Benachrichtigung gesendet, wenn ein Ticket einem Benutzer zugewiesen wird',
        ],
        'ticket_scheduled' => [
            'name' => 'Ticket geplant',
            'description' => 'Benachrichtigung gesendet, wenn ein Ticket geplant wird',
        ],
    ],
    'channels' => [
        'in_app' => 'In-App',
        'email' => 'E-Mail',
        'sms' => 'SMS',
    ],
];
