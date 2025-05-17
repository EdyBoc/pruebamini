<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

});

Route::apiResource('personas', PersonaController::class);
Route::put('/personas/{id}', [PersonaController::class, 'update']);

