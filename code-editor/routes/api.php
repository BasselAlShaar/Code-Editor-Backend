<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});







// Authentication routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');

// User routes
Route::middleware('auth.user:user')->group(function () {
    Route::prefix('users')->group(function() {
        Route::get('/', [UserController::class, 'getAllUsers']);
        Route::post('/', [UserController::class, 'createUser']);
        Route::get('{id}', [UserController::class, 'getUser']);
        Route::put('{id}', [UserController::class, 'updateUser']);
        Route::delete('{id}', [UserController::class, 'deleteUser']);
    });

    // Code routes related to users
    Route::prefix('codes')->group(function () {
        Route::get('/', [CodeController::class, 'getAllCodes']);
        Route::get('/{codeID}', [CodeController::class, 'getCode']);
        Route::post('/', [CodeController::class, 'createCode']);
        Route::put('/{codeID}', [CodeController::class, 'updateCode']);
        Route::delete('/{codeID}', [CodeController::class, 'deleteCode']);
    });

    // Message routes related to users
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'getAllMessages']);
        Route::get('/{messageID}', [MessageController::class, 'getMessage']);
        Route::post('/', [MessageController::class, 'createMessage']);
        Route::put('/{messageID}', [MessageController::class, 'updateMessage']);
        Route::delete('/{messageID}', [MessageController::class, 'deleteMessage']);
    });

});

//Admin routes
Route::middleware(['auth.user:admin'])->group(function () {
    Route::prefix('admin')->group(function() {
        Route::get('/', function () {
            return "admin route tested";
            
        });
    });
});

