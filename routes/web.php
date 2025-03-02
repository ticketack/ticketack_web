<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketDocumentController;
use App\Http\Controllers\TicketPdfController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Foundation\Application;
use Spatie\Permission\Middlewares\RoleOrPermissionMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Routes du panneau solver
    Route::middleware(['role:solver|admin'])->group(function () {
        Route::get('/solver-dashboard', [\App\Http\Controllers\SolverDashboardController::class, 'index'])
            ->name('solver.dashboard');
        Route::post('/schedules', [\App\Http\Controllers\SolverDashboardController::class, 'scheduleTicket'])
            ->name('schedules.store');
        Route::put('/schedules/{schedule}', [\App\Http\Controllers\SolverDashboardController::class, 'updateSchedule'])
            ->name('schedules.update');
    });

    // Routes des planifications pour tous les utilisateurs authentifiés
    Route::put('/schedules/{id}', [ScheduleController::class, 'update'])
        ->name('schedules.update.all');
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])
        ->name('schedules.destroy.all');

    // Routes du planning
    Route::middleware([\App\Http\Middleware\CheckPermission::class . ':planning.view'])->group(function () {
        Route::get('/planning', [PlanningController::class, 'index'])
            ->name('planning.index');
    });

    // Routes des tickets
    Route::middleware([\App\Http\Middleware\CheckPermission::class . ':tickets.view'])->group(function () {
        Route::get('/tickets', [\App\Http\Controllers\TicketController::class, 'index'])
            ->name('tickets.index');
            
        // Route de création de tickets (doit être avant la route avec paramètre)
        Route::middleware([\App\Http\Middleware\CheckPermission::class . ':tickets.create'])->group(function () {
            Route::get('/tickets/create', [\App\Http\Controllers\TicketController::class, 'create'])
                ->name('tickets.create');
            Route::post('/tickets', [\App\Http\Controllers\TicketController::class, 'store'])
                ->name('tickets.store');
        });

        Route::get('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])
            ->name('tickets.show');

        Route::get('/tickets/{ticket}/documents', [\App\Http\Controllers\TicketController::class, 'documents'])
            ->name('tickets.documents');

        // Route de téléchargement (accessible à tous ceux qui peuvent voir les tickets)
        Route::get('/tickets/{ticket}/documents/{document}/download', [TicketDocumentController::class, 'download'])
            ->name('tickets.documents.download');
            
    });

    // Route de recherche d'utilisateurs pour l'assignation
    Route::middleware(['auth', \App\Http\Middleware\CheckPermission::class . ':tickets.assign'])->group(function () {
        Route::get('/users/search', [UserController::class, 'search'])
            ->name('users.search');
    
    });

    // Route PDF (accessible aux admins et ceux ayant la permission)
    Route::get('/tickets/{ticket}/pdf', [TicketPdfController::class, 'generate'])
        ->name('tickets.pdf')
        ->middleware('auth');

    // Routes d'édition de tickets
    Route::middleware([\App\Http\Middleware\CheckPermission::class . ':tickets.edit'])->group(function () {
        Route::put('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'update'])
            ->name('tickets.update');

        // Routes pour l'assignation multiple
        Route::post('/tickets/{ticket}/assign', [\App\Http\Controllers\TicketController::class, 'assign'])
            ->name('tickets.assign');
        Route::delete('/tickets/{ticket}/assign/{user}', [\App\Http\Controllers\TicketController::class, 'unassign'])
            ->name('tickets.unassign');

        // Routes pour les documents
        Route::post('/tickets/{ticket}/documents', [TicketDocumentController::class, 'store'])
            ->name('tickets.documents.store');
        Route::delete('/tickets/{ticket}/documents/{document}', [TicketDocumentController::class, 'destroy'])
            ->name('tickets.documents.destroy');

        // Routes pour les commentaires
        Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])
            ->name('tickets.comments.store');
        Route::delete('/tickets/{ticket}/comments/{comment}', [CommentController::class, 'destroy'])
            ->name('tickets.comments.destroy');
    });

    // Route de la documentation API
    Route::get('/api/documentation', [\App\Http\Controllers\ApiDocumentationController::class, 'index'])
        ->name('api.documentation');
    // Routes du profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes des équipements avec groupe de middleware
    Route::middleware([\App\Http\Middleware\CheckPermission::class . ':equipment.view'])->group(function () {
        Route::get('equipment/search', [EquipmentController::class, 'search'])
            ->name('equipment.search');
        Route::get('equipment', [EquipmentController::class, 'index'])
            ->name('equipment.index');

        Route::middleware([\App\Http\Middleware\CheckPermission::class . ':equipment.create'])->group(function () {
            Route::get('equipment/create', [EquipmentController::class, 'create'])
                ->name('equipment.create');
            Route::post('equipment', [EquipmentController::class, 'store'])
                ->name('equipment.store');
            Route::delete('equipment/{equipment}/image', [EquipmentController::class, 'deleteImage'])
                ->name('equipment.delete-image');
        });

        Route::middleware([\App\Http\Middleware\CheckPermission::class . ':equipment.edit'])->group(function () {
            Route::get('equipment/{equipment}/edit', [EquipmentController::class, 'edit'])
                ->name('equipment.edit');
            Route::put('equipment/{equipment}', [EquipmentController::class, 'update'])
                ->name('equipment.update');
        });

        Route::middleware([\App\Http\Middleware\CheckPermission::class . ':equipment.delete'])->group(function () {
            Route::delete('equipment/{equipment}', [EquipmentController::class, 'destroy'])
                ->name('equipment.destroy');
        });
    });

    // Routes pour la gestion des rôles (admin uniquement)
    Route::group(['middleware' => ['role_or_permission:admin|roles.view']], function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        
        Route::middleware(['role_or_permission:admin|roles.create'])->group(function () {
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        });

        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');

        Route::group(['middleware' => ['role_or_permission:admin|roles.create']], function () {
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        });

        Route::group(['middleware' => ['role_or_permission:admin|roles.edit']], function () {
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        });

        Route::group(['middleware' => ['role_or_permission:admin|roles.delete']], function () {
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });
    });

    // Routes pour la gestion des utilisateurs (admin uniquement)
    Route::middleware([\App\Http\Middleware\CheckPermission::class . ':users.view'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Routes pour la gestion des paramètres (admin uniquement)
    Route::middleware([\App\Http\Middleware\CheckPermission::class . ':settings.view'])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::middleware([\App\Http\Middleware\CheckPermission::class . ':settings.edit'])->group(function () {
            Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
            Route::delete('/settings/logo', [SettingsController::class, 'deleteLogo'])->name('settings.delete-logo');
        });
    });
});

// Route de test pour S3
Route::get('/test-s3', function() {
    try {
        // Test d'écriture
        $content = 'Test file content ' . time();
        $testFile = 'test/test.txt';
        
        // Récupération de la configuration complète
        $config = config('filesystems.disks.s3');
        
        // Création du client S3 manuellement pour tester
        $s3Client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => $config['region'],
            'endpoint' => $config['endpoint'],
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => $config['key'],
                'secret' => $config['secret'],
            ],
        ]);
        
        // Test de listage du bucket
        try {
            $objects = $s3Client->listObjects([
                'Bucket' => $config['bucket']
            ]);
            $listSuccess = true;
            $listError = null;
        } catch (\Exception $e) {
            $listSuccess = false;
            $listError = $e->getMessage();
        }
        
        // Test d'écriture directe via le client S3
        try {
            $putResult = $s3Client->putObject([
                'Bucket' => $config['bucket'],
                'Key'    => $testFile,
                'Body'   => $content
            ]);
            $writeSuccess = true;
            $writeError = null;
        } catch (\Exception $e) {
            $writeSuccess = false;
            $writeError = $e->getMessage();
        }
        
        return response()->json([
            'configuration' => [
                'driver' => $config['driver'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'endpoint' => $config['endpoint'],
                'url' => $config['url'],
                'use_path_style_endpoint' => $config['use_path_style_endpoint'] ?? true,
            ],
            'test_results' => [
                'list_bucket' => [
                    'success' => $listSuccess,
                    'error' => $listError
                ],
                'write_file' => [
                    'success' => $writeSuccess,
                    'error' => $writeError
                ]
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Erreur test S3:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

require __DIR__.'/auth.php';
