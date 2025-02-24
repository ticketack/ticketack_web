<?php

return [
    'index' => [
        'title' => 'Tickets',
        'filters' => 'Filters',
        'new_ticket' => 'New Ticket',
        'private' => 'Private',
        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'status' => 'Status',
            'visibility' => 'Visibility',
            'priority' => 'Priority',
            'category' => 'Category',
            'equipment' => 'Equipment',
            'assigned_to' => 'Assigned To',
            'author' => 'Author',
            'created_at' => 'Created At'
        ]
    ],
    'create' => [
        'title' => 'Create Ticket',
        'step1' => [
            'title' => 'Ticket Information',
            'completed' => 'Information Completed'
        ],
        'step2' => [
            'title' => 'Documents',
            'drag_files' => 'Documents Added'
        ],
        'fields' => [
            'title' => 'Title',
            'description' => 'Description',
            'priority' => 'Priority',
            'category' => 'Category',
            'equipment' => 'Equipment'
        ],
        'priority' => [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical'
        ],
        'buttons' => [
            'cancel' => 'Cancel',
            'next' => 'Next',
            'modify' => 'Modify',
            'finish' => 'Finish',
            'back' => 'Back to Step 1'
        ]
    ],
    'comments' => [
        'title' => 'Comments',
        'no_comments' => 'No comments yet',
        'add' => 'Add Comment',
        'placeholder' => 'Your comment...',
        'submit' => 'Send',
        'attachments' => 'Attachments',
        'add_files' => 'Add Files',
        'confirm_delete' => 'Are you sure you want to delete this comment?',
        'max_size' => 'Maximum Size',
        'accepted_formats' => 'Accepted Formats',
        'success' => [
            'added' => 'Comment added successfully',
            'deleted' => 'Comment deleted successfully'
        ],
        'error' => [
            'add' => 'An error occurred while adding the comment',
            'delete' => 'Error deleting comment',
            'file_size' => 'File size exceeds the 10 MB limit'
        ]
    ],
    'documents' => [
        'title' => 'Documents',
        'no_documents' => 'No documents yet',
        'add' => 'Add Documents',
        'drag_drop' => 'Drag and drop files here, or click to select',
        'submit' => 'Upload',
        'errors' => [
            'file_type' => 'File type not allowed',
            'file_size' => 'File size exceeds the 10 MB limit'
        ]
    ]
];
