<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
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
    // Routes des tickets
    Route::middleware([\App\Http\Middleware\CheckPermission::class . ':tickets.view'])->group(function () {
        Route::get('/tickets', [\App\Http\Controllers\TicketController::class, 'index'])
            ->name('tickets.index');

        Route::middleware([\App\Http\Middleware\CheckPermission::class . ':tickets.create'])->group(function () {
            Route::get('/tickets/create', [\App\Http\Controllers\TicketController::class, 'create'])
                ->name('tickets.create');
            Route::post('/tickets', [\App\Http\Controllers\TicketController::class, 'store'])
                ->name('tickets.store');
        });

        Route::get('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])
            ->name('tickets.show');

        Route::middleware([\App\Http\Middleware\CheckPermission::class . ':tickets.edit'])->group(function () {
            Route::put('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'update'])
                ->name('tickets.update');
        });
    });

    // Route de la documentation API
    Route::get('/api/documentation', [\App\Http\Controllers\ApiDocumentationController::class, 'index'])
        ->name('api.documentation');
    // Routes du profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes des équipements avec groupe de middleware
    Route::middleware([\App\Http\Middleware\CheckPermission::class . ':equipements.view'])->group(function () {
        Route::get('equipements/search', [EquipementController::class, 'search'])
            ->name('equipements.search');
        Route::get('equipements', [EquipementController::class, 'index'])
            ->name('equipements.index');

        Route::middleware([\App\Http\Middleware\CheckPermission::class . ':equipements.create'])->group(function () {
            Route::get('equipements/create', [EquipementController::class, 'create'])
                ->name('equipements.create');
            Route::post('equipements', [EquipementController::class, 'store'])
                ->name('equipements.store');
            Route::delete('equipements/{equipement}/image', [EquipementController::class, 'deleteImage'])
                ->name('equipements.delete-image');
        });

        Route::middleware([\App\Http\Middleware\CheckPermission::class . ':equipements.edit'])->group(function () {
            Route::get('equipements/{equipement}/edit', [EquipementController::class, 'edit'])
                ->name('equipements.edit');
            Route::put('equipements/{equipement}', [EquipementController::class, 'update'])
                ->name('equipements.update');
        });

        Route::middleware([\App\Http\Middleware\CheckPermission::class . ':equipements.delete'])->group(function () {
            Route::delete('equipements/{equipement}', [EquipementController::class, 'destroy'])
                ->name('equipements.destroy');
        });
    });

    // Routes pour la gestion des rôles (admin uniquement)
    Route::middleware([\App\Http\Middleware\CheckPermission::class . ':roles.view'])->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
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
