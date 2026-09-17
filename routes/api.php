<?php

use App\Http\Controllers\AffectationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChampDemandeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TypeDemandeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/password', [AuthController::class, 'updatePassword']);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Référentiels (CRUD simple, autorisation via les Policies).
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('users', UserController::class);

    Route::apiResource('type-demandes', TypeDemandeController::class)
        ->parameters(['type-demandes' => 'typeDemande']);

    Route::post('/champ-demandes', [ChampDemandeController::class, 'store']);
    Route::put('/champ-demandes/{champDemande}', [ChampDemandeController::class, 'update']);
    Route::delete('/champ-demandes/{champDemande}', [ChampDemandeController::class, 'destroy']);

    // Demandes : CRUD (pas de suppression, seulement l'archivage via le workflow).
    Route::apiResource('demandes', DemandeController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('demandes/{demande}')->group(function () {
        Route::post('/submit', [DemandeController::class, 'submit']);
        Route::post('/affectations', [AffectationController::class, 'store']);
        Route::post('/validations', [ValidationController::class, 'store']);
        Route::post('/transmettre-validation', [WorkflowController::class, 'transmettreEnValidation']);
        Route::post('/demander-complement', [WorkflowController::class, 'demanderComplement']);
        Route::post('/archiver', [WorkflowController::class, 'archiver']);
        Route::post('/documents', [DocumentController::class, 'store']);
    });

    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
});
