<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:2,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'authUser']);
        Route::patch('/', [UserController::class, 'update']);
    });
    Route::group(['prefix' => 'recipes'], function () {
        Route::get('/', [RecipeController::class, 'index'])->middleware('role:user');
        Route::get('/all', [RecipeController::class, 'all']);
        Route::post('/create', [RecipeController::class, 'store']);
        Route::patch('/{id}', [RecipeController::class, 'update']);
        Route::delete('/delete/{id}', [RecipeController::class, 'destroy']);
    });
    Route::group(['prefix' => 'ingredients'], function () {
        Route::get('/', [IngredientController::class, 'index'])->middleware('role:user');;
        Route::get('/all', [IngredientController::class, 'all']);
        Route::post('/create', [IngredientController::class, 'store']);
        Route::patch('/{id}', [IngredientController::class, 'update']);
        Route::delete('/delete/{id}', [IngredientController::class, 'destroy']);
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
});