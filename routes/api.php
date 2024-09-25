<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReferentielController;


Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('/auth/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');


Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'index']); // Liste tous les utilisateurs
    Route::post('/users', [UserController::class, 'store']); // Crée un nouvel utilisateur
    Route::get('/users/{user}', [UserController::class, 'show']); // Affiche les détails d'un utilisateur spécifique
    Route::patch('/users/{user}', [UserController::class, 'update']); // Met à jour un utilisateur spécifique
    Route::get('/users?role_id', [UserController::class, 'index']);
    Route::get('/users?status', [UserController::class, 'index']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']); // Supprime un utilisateur spécifique
});


// Route::post('/referentiels', [FirebaseReferentielController::class, 'store']);
// Route::get('/referentiels', [FirebaseReferentielController::class, 'index']);
// Route::get('/referentiels/{id}', [FirebaseReferentielController::class, 'show']);
// Route::delete('/referentiels/{id}', [FirebaseReferentielController::class, 'destroy']);
// Route::get('/referentiels/competence', [FirebaseReferentielController::class, 'filterByCompetence']);
// Route::get('/referentiels/modules', [FirebaseReferentielController::class, 'filterByModules']);
// Route::get('/referentiels/archived', [FirebaseReferentielController::class, 'archivedReferentiels']);

// Route::prefix('v1')->group(function () {
//     Route::get('/referentiels', [FirebaseReferentielController::class, 'index']);
//     Route::post('/referentiels', [FirebaseReferentielController::class, 'store']);
//     Route::patch('/referentiels/{id}', [FirebaseReferentielController::class, 'update']);
//     Route::get('/referentiels/{id}', [FirebaseReferentielController::class, 'show']);
//     Route::delete('/referentiels/{id}', [FirebaseReferentielController::class, 'destroy']);
//     Route::get('/archive/referentiels', [FirebaseReferentielController::class, 'archivedReferentiels']);
//     Route::get('/referentiels/filter/competence', [FirebaseReferentielController::class, 'filterByCompetence']);
//     Route::get('/referentiels/filter/modules', [FirebaseReferentielController::class, 'filterByModules']);
// });

// routes/api.php
Route::prefix('v1')->group(function () {
       Route::get('referentiels', [ReferentielController::class, 'index']);
       Route::post('referentiels', [ReferentielController::class, 'store']);
       Route::get('referentiels/{id}', [ReferentielController::class, 'show']);
       Route::put('referentiels/{id}', [ReferentielController::class, 'update']);
       Route::delete('referentiels/{id}', [ReferentielController::class, 'destroy']);
       Route::get('archive/referentiels', [ReferentielController::class, 'getArchived']);
   });


Route::prefix('v1')->group(function () {
    Route::get('/promotions', [PromotionController::class, 'index']);
    Route::post('/promotions', [PromotionController::class,'store']);
    Route::patch('promotions/{promotion}/referentials', [PromotionController::class, 'addReferential'])->name('promotions.add-referential');
    Route::delete('promotions/{promotion}/referentials/{referential}', [PromotionController::class, 'removeReferential'])->name('promotions.remove-referential');
    Route::patch('promotions/{promotion}/status/{status}', [PromotionController::class, 'changeStatus'])->name('promotions.change-status');
    Route::post('promotions/{promotion}/close', [PromotionController::class, 'close'])->name('promotions.close');
});
