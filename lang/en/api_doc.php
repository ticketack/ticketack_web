<?php

return [
    'title' => 'API Documentation',
    'summary' => 'Summary',
    'introduction' => [
        'title' => 'Introduction',
        'description' => 'Welcome to our API documentation. This API allows you to interact with our ticket management system.'
    ],
    'authentication' => [
        'title' => 'Authentication',
        'description' => 'The API uses Bearer token authentication.',
        'token_header_instruction' => 'Add the token in the Authorization header of your requests.'
    ],
    'endpoints' => [
        'title' => 'Endpoints',
        'parameters' => 'Parameters',
        'token_usage' => 'Token Usage',
        'your_personal_token' => 'your_personal_token',
        'api_token_info' => 'You can obtain your API token by logging into your account.',
        'auth' => [
            'title' => 'Authentication',
            'login' => [
                'token_header_instruction' => 'To authenticate, send your credentials to receive an access token.'
            ],
            'register' => [
                'description' => 'Create a new user account.'
            ],
            'logout' => [
                'description' => 'Log out the user and revoke the token.'
            ],
            'user' => [
                'description' => 'Retrieve the connected user\'s information.'
            ]
        ],
        'equipment' => [
            'title' => 'Equipment',
            'create' => [
                'description' => 'Create new equipment.'
            ],
            'show' => [
                'description' => 'Retrieve details of a specific equipment.'
            ],
            'update' => [
                'description' => 'Update equipment information.'
            ],
            'delete' => [
                'description' => 'Delete equipment.'
            ]
        ],
        'tickets' => [
            'title' => 'Tickets',
            'list' => [
                'description' => 'Retrieve the list of tickets with filtering options.'
            ],
            'create' => [
                'description' => 'Create a new ticket.'
            ],
            'show' => [
                'description' => 'Retrieve details of a specific ticket.'
            ],
            'update' => [
                'description' => 'Update ticket information.'
            ],
            'delete' => [
                'description' => 'Delete ticket.'
            ]
        ],
        'example_values' => [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ],
        'form_labels' => [
            'email' => 'User email address',
            'password' => 'User password'
        ],
        'table_headers' => [
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Description',
            'request_parameters' => 'Request Parameters'
        ]
    ],
    'response_codes' => [
        'title' => 'Response Codes',
        'success' => 'Request successful',
        'created' => 'Resource created successfully',
        'bad_request' => 'Invalid request',
        'unauthorized' => 'Unauthorized',
        'forbidden' => 'Access forbidden',
        'not_found' => 'Resource not found',
        'validation_error' => 'Validation error',
        'server_error' => 'Server error'
    ]
];
