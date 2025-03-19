<?php

return [
    'index' => [
        'title' => 'Tickets',
        'search' => 'In Titeln und Beschreibungen suchen...',
        'search_button' => 'Suchen',
        'filters' => 'Filter',
        'new_ticket' => 'Neues Ticket',
        'private' => 'Privat',
        'columns' => [
            'id' => 'ID',
            'title' => 'Titel',
            'status' => 'Status',
            'visibility' => 'Sichtbarkeit',
            'priority' => 'Priorität',
            'category' => 'Kategorie',
            'equipment' => 'Ausrüstung',
            'assigned_to' => 'Zugewiesen an',
            'author' => 'Autor',
            'created_at' => 'Erstellt am',
            'last_action_at' => 'Letzte Aktualisierung'
        ]
    ],
    'create' => [
        'title' => 'Ticket erstellen',
        'step1' => [
            'title' => 'Ticket-Informationen',
            'completed' => 'Informationen abgeschlossen'
        ],
        'step2' => [
            'title' => 'Dokumente',
            'drag_files' => 'Dokumente hinzugefügt',
            'no_files' => 'Keine Dokumente hinzugefügt',
            'remove_file' => 'Dieses Dokument entfernen'
        ],
        'fields' => [
            'title' => 'Titel',
            'description' => 'Beschreibung',
            'priority' => 'Priorität',
            'category' => 'Kategorie',
            'equipment' => 'Ausrüstung',
            'visibility' => 'Sichtbarkeit',
            'status' => 'Status',
            'assigned_to' => 'Zugewiesen an',
            'due_date' => 'Gewünschtes Lösungsdatum'
        ],
        'visibility' => [
            'public_label' => 'Öffentlich'
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
            'finish' => 'Fertigstellen',
            'back' => 'Zurück zu Schritt 1',
            'submit' => 'Absenden',
            'add_files' => 'Dateien hinzufügen',
            'remove' => 'Entfernen'
        ]
    ],
    'comments' => [
        'title' => 'Kommentare',
        'no_comments' => 'Noch keine Kommentare',
        'add' => 'Kommentar hinzufügen',
        'placeholder' => 'Ihr Kommentar...',
        'submit' => 'Senden',
        'attachments' => 'Anhänge',
        'add_files' => 'Dateien hinzufügen',
        'confirm_delete' => 'Sind Sie sicher, dass Sie diesen Kommentar löschen möchten?',
        'max_size' => 'Maximale Größe',
        'accepted_formats' => 'Akzeptierte Formate',
        'success' => [
            'added' => 'Kommentar erfolgreich hinzugefügt',
            'deleted' => 'Kommentar erfolgreich gelöscht'
        ],
        'error' => [
            'add' => 'Beim Hinzufügen des Kommentars ist ein Fehler aufgetreten',
            'delete' => 'Fehler beim Löschen des Kommentars',
            'file_size' => 'Die Dateigröße überschreitet das Limit von 10 MB'
        ]
    ],
    'documents' => [
        'title' => 'Dokumente',
        'no_documents' => 'Noch keine Dokumente',
        'add' => 'Dokumente hinzufügen',
        'drag_drop' => 'Dateien hier ablegen oder zum Auswählen klicken',
        'submit' => 'Hochladen',
        'max_size' => 'Maximale Größe: :size',
        'accepted_formats' => 'Akzeptierte Formate: :formats',
        'errors' => [
            'file_type' => 'Dateityp nicht erlaubt',
            'file_size' => 'Die Dateigröße überschreitet das Limit von 10 MB'
        ]
    ]
];
