<?php

return [
    'preferences' => [
        'title' => 'Notification Preferences',
        'description' => 'Configure your preferences for in-app, email, and SMS notifications.',
        'manage' => 'Manage Preferences',
        'view_history' => 'View Notification History',
        'contact_info' => 'Contact Information',
        'phone_number' => 'Phone Number (for SMS)',
        'phone_format' => 'Recommended international format (e.g., +44712345678)',
        'api_key' => 'SMS API Key (optional)',
        'api_key_description' => 'If empty, the default API key will be used',
        'save' => 'Save Preferences',
        'back_to_profile' => 'Back to Profile',
        'updated' => 'Notification preferences updated successfully',
    ],
    'logs' => [
        'title' => 'Notification History',
        'no_notifications' => 'No notifications found',
        'all_channels' => 'All channels',
        'all_statuses' => 'All statuses',
        'unread' => 'Unread',
        'read' => 'Read',
        'mark_all_read' => 'Mark all as read',
        'mark_as_read' => 'Mark as read',
        'already_read' => 'Already read',
        'new' => 'New',
        'marked_as_read' => 'Notification marked as read',
        'all_marked_as_read' => 'All notifications have been marked as read',
    ],
    'types' => [
        'ticket_created' => [
            'name' => 'Ticket Created',
            'description' => 'Notification sent when a new ticket is created',
        ],
        'ticket_time_tracked' => [
            'name' => 'Time Tracked on Ticket',
            'description' => 'Notification sent when time is tracked on a ticket',
        ],
        'ticket_commented' => [
            'name' => 'Ticket Commented',
            'description' => 'Notification sent when a comment is added to a ticket',
        ],
        'ticket_status_changed' => [
            'name' => 'Ticket Status Changed',
            'description' => 'Notification sent when a ticket status is changed',
        ],
        'ticket_assigned' => [
            'name' => 'Ticket Assigned',
            'description' => 'Notification sent when a ticket is assigned to a user',
        ],
        'ticket_scheduled' => [
            'name' => 'Ticket Scheduled',
            'description' => 'Notification sent when a ticket is scheduled',
        ],
    ],
    'channels' => [
        'in_app' => 'In-App',
        'email' => 'Email',
        'sms' => 'SMS',
    ],
];
