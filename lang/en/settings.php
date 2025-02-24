<?php

return [
    'title' => 'Settings',
    'company_name' => 'Company Name',
    'logo' => [
        'title' => 'Logo',
        'current' => 'Current Logo',
        'upload' => 'Upload New Logo',
        'delete' => 'Delete Logo',
        'confirm_delete' => 'Are you sure you want to delete the logo?',
        'error_size' => 'File is too large. Maximum allowed size is 10MB.',
        'no_logo' => 'No logo',
        'preview' => 'Logo Preview',
        'requirements' => 'Accepted formats: PNG, JPG, JPEG, TIFF, WEBP. Maximum size: 10MB.',
    ],
    'language' => [
        'title' => 'Language',
        'fr' => 'Français',
        'en' => 'English',
        'de' => 'Deutsch',
        'select' => 'Select a language',
    ],
    'success' => [
        'updated' => 'Settings updated successfully',
        'logo_deleted' => 'Logo deleted successfully',
        'language_updated' => 'Language updated successfully',
    ],
    'error' => [
        'update' => 'An error occurred while updating settings',
        'logo_delete' => 'An error occurred while deleting the logo',
        'logo_update' => 'An error occurred while updating the logo',
        'language_update' => 'An error occurred while updating the language',
    ],
];
