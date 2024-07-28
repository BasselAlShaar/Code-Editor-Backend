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
Route::middleware('auth.user')->group(function () {
    Route::prefix('users')->group(function() {
        Route::get('/', [UserController::class, 'getAllUsers']);
        Route::post('/', [UserController::class, 'createUser']);
        Route::get('{id}', [UserController::class, 'getUser']);
        Route::put('{id}', [UserController::class, 'updateUser']);
        Route::delete('{id}', [UserController::class, 'deleteUser']);
    });

    // Code routes related to users
    Route::prefix('user/{userID}')->group(function () {
        Route::get('codes', [CodeController::class, 'getAllCodes']);
        Route::get('code/{codeID}', [CodeController::class, 'getCode']);
        Route::post('code/', [CodeController::class, 'createCode']);
        Route::put('code/{codeID}', [CodeController::class, 'updateCode']);
        Route::delete('code/{codeID}', [CodeController::class, 'deleteCode']);
    });

    // Message routes related to users
    Route::prefix('user/{userID}')->group(function () {
        Route::get('messages', [MessageController::class, 'getAllMessages']);
        Route::get('message/{messageID}', [MessageController::class, 'getMessage']);
        Route::post('message', [MessageController::class, 'createMessage']);
        Route::put('message/{messageID}', [MessageController::class, 'updateMessage']);
        Route::delete('message/{messageID}', [MessageController::class, 'deleteMessage']);
    });

});