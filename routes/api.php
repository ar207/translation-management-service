<?php

use App\Http\Controllers\Api\AuthenticateController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\TranslationController;
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

Route::post('register', [AuthenticateController::class, 'register']);
Route::post('login', [AuthenticateController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthenticateController::class, 'logout']);

    Route::apiResource('locales', LocaleController::class);
    Route::apiResource('translations', TranslationController::class);

    Route::get('translations/export/json', [TranslationController::class, 'export']);
});
