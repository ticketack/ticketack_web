<?php

return [
    'dashboard' => [
        'title' => 'Dashboard',
        'statistics' => 'Statistics',
        'total_equipment' => 'Total Equipment',
        'latest_products' => 'Latest Products',
        'ticket_stats' => 'Ticket Statistics',
        'total_tickets' => 'Total Tickets',
        'by_status' => 'By Status',
        'by_priority' => 'By Priority'
    ],
    'auth' => [
        'login' => [
            'title' => 'Login',
            'email' => 'Email',
            'password' => 'Password',
            'remember_me' => 'Remember me',
            'forgot_password' => 'Forgot your password?',
            'submit' => 'Log in'
        ],
        'confirm_password' => [
            'title' => 'Confirm Password',
            'text' => 'This is a secure area. Please confirm your password before continuing.',
            'password' => 'Password',
            'confirm' => 'Confirm'
        ],
        'forgot_password' => [
            'title' => 'Forgot Password',
            'text' => 'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.',
            'email' => 'Email',
            'reset_link' => 'Send reset link'
        ],
        'reset_password' => [
            'title' => 'Reset Password',
            'email' => 'Email',
            'password' => 'Password',
            'confirm_password' => 'Confirm Password',
            'reset' => 'Reset'
        ],
        'register' => [
            'title' => 'Register',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'confirm_password' => 'Confirm Password',
            'already_registered' => 'Already registered?',
            'register' => 'Register'
        ],
        'verify_email' => [
            'title' => 'Verify Email',
            'text' => 'Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.',
            'new_verification' => 'A new verification link has been sent',
            'resend' => 'Resend Verification Email',
            'logout' => 'Log Out'
        ]
    ],
    'profile' => [
        'title' => 'Profile',
        'api_token' => [
            'title' => 'API Token',
            'description' => 'Your API token for API authentication.',
            'copy' => 'Copy',
            'copied' => 'Copied!'
        ],
        'update_info' => [
            'title' => 'Profile Information',
            'text' => 'Update your account\'s profile information and email address.',
            'name' => 'Name',
            'email' => 'Email',
            'save' => 'Save',
            'saved' => 'Saved.'
        ],
        'update_password' => [
            'title' => 'Update Password',
            'text' => 'Ensure your account is using a long, random password to stay secure.',
            'current_password' => 'Current Password',
            'new_password' => 'New Password',
            'confirm_password' => 'Confirm Password',
            'save' => 'Save'
        ],
        'delete_account' => [
            'title' => 'Delete Account',
            'text' => 'Once your account is deleted, all of its resources and data will be permanently deleted.',
            'button' => 'Delete Account',
            'confirm_title' => 'Are you sure you want to delete your account?',
            'confirm_text' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
            'password' => 'Password',
            'cancel' => 'Cancel',
            'confirm' => 'Delete Account'
        ]
    ],
    'equipment' => [
        'title' => 'Equipment',
        'create' => [
            'title' => 'Create Equipment',
            'designation' => 'Designation',
            'marque' => 'Brand',
            'modele' => 'Model',
            'image' => 'Image',
            'icone' => 'Icon',
            'save' => 'Save',
            'cancel' => 'Cancel'
        ],
        'edit' => [
            'title' => 'Edit Equipment',
            'designation' => 'Designation',
            'marque' => 'Brand',
            'modele' => 'Model',
            'image' => 'Image',
            'icone' => 'Icon',
            'save' => 'Save',
            'cancel' => 'Cancel'
        ],
        'delete' => [
            'confirm_title' => 'Delete Equipment',
            'confirm_text' => 'Are you sure you want to delete this equipment?',
            'cancel' => 'Cancel',
            'confirm' => 'Delete'
        ],
        'index' => [
            'title' => 'Equipment Management',
            'new_equipment' => 'New Equipment',
            'list' => 'Equipment List'
        ]
    ],
    'equipements' => [
        'title' => 'Equipment',
        'create' => [
            'title' => 'Create Equipment',
            'designation' => 'Designation',
            'marque' => 'Brand',
            'modele' => 'Model',
            'image' => 'Image',
            'icone' => 'Icon',
            'save' => 'Save',
            'cancel' => 'Cancel'
        ],
        'edit' => [
            'title' => 'Edit Equipment',
            'designation' => 'Designation',
            'marque' => 'Brand',
            'modele' => 'Model',
            'image' => 'Image',
            'icone' => 'Icon',
            'save' => 'Save',
            'cancel' => 'Cancel'
        ],
        'delete' => [
            'confirm_title' => 'Delete Equipment',
            'confirm_text' => 'Are you sure you want to delete this equipment?',
            'cancel' => 'Cancel',
            'confirm' => 'Delete'
        ],
        'index' => [
            'title' => 'Equipment Management',
            'new_equipment' => 'New Equipment',
            'list' => 'Equipment List'
        ]
    ],
    'roles' => [
        'title' => 'Roles',
        'index' => [
            'title' => 'Role Management',
            'new_role' => 'New Role',
            'name' => 'Name',
            'description' => 'Description',
            'permissions' => 'Permissions',
            'actions' => 'Actions',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'confirm_delete' => 'Are you sure you want to delete the role ":name"?'
        ],
        'create' => [
            'title' => 'Create Role',
            'name' => 'Name',
            'permissions' => 'Permissions',
            'save' => 'Save',
            'cancel' => 'Cancel'
        ],
        'edit' => [
            'title' => 'Edit Role',
            'name' => 'Name',
            'permissions' => 'Permissions',
            'save' => 'Save',
            'cancel' => 'Cancel'
        ],
        'delete' => [
            'confirm_title' => 'Delete Role',
            'confirm_text' => 'Are you sure you want to delete this role?',
            'cancel' => 'Cancel',
            'confirm' => 'Delete'
        ]
    ],
    'users' => [
        'title' => 'Users',
        'index' => [
            'title' => 'User Management',
            'new_user' => 'New User',
            'name' => 'Name',
            'email' => 'Email',
            'roles' => 'Roles',
            'actions' => 'Actions',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'confirm_delete' => 'Are you sure you want to delete user "{name}"?'
        ],
        'create' => [
            'title' => 'Create User',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'confirm_password' => 'Confirm Password',
            'role' => 'Role',
            'save' => 'Save',
            'cancel' => 'Cancel'
        ],
        'edit' => [
            'title' => 'Edit User',
            'name' => 'Name',
            'email' => 'Email',
            'role' => 'Role',
            'save' => 'Save',
            'cancel' => 'Cancel'
        ],
        'delete' => [
            'confirm_title' => 'Delete User',
            'confirm_text' => 'Are you sure you want to delete this user?',
            'cancel' => 'Cancel',
            'confirm' => 'Delete'
        ]
    ],
    'settings' => [
        'title' => 'Settings',
        'company_name' => 'Company Name',
        'logo' => 'Logo',
        'language' => 'Language',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete_logo' => [
            'title' => 'Delete Logo',
            'text' => 'Are you sure you want to delete the logo?',
            'cancel' => 'Cancel',
            'confirm' => 'Delete'
        ]
    ],
    'api_doc' => [
        'title' => 'API Documentation',
        'summary' => 'Summary',
        'introduction' => [
            'title' => 'Introduction',
            'description' => 'Welcome to our API documentation. This API allows you to interact with our system programmatically.'
        ],
        'authentication' => [
            'title' => 'Authentication',
            'description' => 'The API uses token authentication. You can get your API token from your profile page. Include this token in the Authorization header of your requests.',
            'token_header_instruction' => 'Include your personal API token in the Authorization header of all your API requests:'
        ],
        'endpoints' => [
            'title' => 'Endpoints',
            'headers' => 'Headers',
            'body' => 'Request Body',
            'no_parameters' => 'This endpoint accepts no parameters.',
            'parameters' => 'Parameters',
            'response_format' => 'Response Format',
            'table_headers' => [
                'request_parameters' => 'Request Parameters',
                'name' => 'Name',
                'type' => 'Type',
                'description' => 'Description',
                'required' => 'Required'
            ],
            'token_usage' => 'Token Usage',
            'api_token_info' => 'The API token can be found in your user profile, accessible via the dropdown menu in the top right of the application.',
            'your_personal_token' => 'your_personal_api_token',
            'example_values' => [
                'name' => 'John Doe',
                'email' => 'user@example.com',
                'password' => 'password',
                'workstation_name' => 'Main Workstation',
                'workstation_brand' => 'HP',
                'workstation_model' => 'Z4 G4',
                'workstation_image' => 'workstation.jpg'
            ],
            'parameters_description' => [
                'children' => 'Include child equipment (yes/no)',
                'children_required' => 'No'
            ],
            'form_labels' => [
                'email' => 'User\'s email address',
                'password' => 'User\'s password',
                'device_name' => 'Name of the device or application using the token'
            ],
            'auth' => [
                'title' => 'Authentication',
                'login' => [
                    'description' => 'Authenticate with your email and password to obtain an access token. This token will be required for all other API requests.',
                    'token_instructions' => 'To use the API, you need to use your personal API token available in your user profile. Go to your profile and copy the API token found there.',
                    'parameters' => [
                        'email' => 'User\'s email address',
                        'password' => 'User\'s password'
                    ]
                ],
                'register' => [
                    'description' => 'Create a new user account'
                ],
                'logout' => [
                    'description' => 'Log out and revoke current token'
                ],
                'user' => [
                    'description' => 'Get logged in user information'
                ]
            ],
            'equipment' => [
                'title' => 'Equipment',
                'list' => [
                    'description' => 'Retrieve the complete list of equipment with their hierarchical structure. Each equipment includes its child equipment in the all_children field.'
                ],
                'create' => [
                    'description' => 'Create a new equipment'
                ],
                'show' => [
                    'description' => 'Retrieve the details of a specific equipment. Use the children parameter to include or exclude child equipment in the response.',
                    'parameters' => [
                        'children' => 'Include child equipment (yes/no)'
                    ]
                ],
                'update' => [
                    'description' => 'Update the information of an existing equipment'
                ],
                'delete' => [
                    'description' => 'Delete an existing equipment'
                ]
            ]
        ],
        'response_codes' => [
            'title' => 'Response Codes',
            'code' => 'Code',
            'meaning' => 'Meaning',
            'success' => 'Request successful',
            'created' => 'Resource created successfully',
            'bad_request' => 'Invalid request',
            'unauthorized' => 'Not authenticated',
            'forbidden' => 'Access forbidden',
            'not_found' => 'Resource not found',
            'validation_error' => 'Validation error',
            'server_error' => 'Server error'
        ]
    ]
];
