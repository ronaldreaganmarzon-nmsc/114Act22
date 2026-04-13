<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenericCrudController;
use Illuminate\Support\Facades\Route;

// Root endpoint
Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Bag API',
        'version' => '1.0.0',
        'endpoints' => [
            'auth' => ['/api/register', '/api/login', '/api/logout'],
            'crud' => ['/api/{entity}', '/api/{entity}/{id}'],
        ],
    ]);
});

// Debug endpoint
Route::any('/test-json', function () {
    $body = file_get_contents('php://input');
    return response()->json([
        'method' => $_SERVER['REQUEST_METHOD'],
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'NOT SET',
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? '0',
        'raw_body' => $body,
        'raw_body_hex' => bin2hex(substr($body, 0, 100)),
        'body_length' => strlen($body),
    ]);
});

// Auth routes (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Generic CRUD (exclude auth route names, entity must be letters/underscores only)
    Route::get('/{entity}/{id}', [GenericCrudController::class, 'show'])->where(['entity' => '^[a-zA-Z_][a-zA-Z0-9_]*$', 'id' => '\d+']);
    Route::put('/{entity}/{id}', [GenericCrudController::class, 'update'])->where(['entity' => '^[a-zA-Z_][a-zA-Z0-9_]*$', 'id' => '\d+']);
    Route::delete('/{entity}/{id}', [GenericCrudController::class, 'destroy'])->where(['entity' => '^[a-zA-Z_][a-zA-Z0-9_]*$', 'id' => '\d+']);
    Route::get('/{entity}', [GenericCrudController::class, 'index'])->where('entity', '^[a-zA-Z_][a-zA-Z0-9_]*$');
    Route::post('/{entity}', [GenericCrudController::class, 'store'])->where('entity', '^[a-zA-Z_][a-zA-Z0-9_]*$');
});
