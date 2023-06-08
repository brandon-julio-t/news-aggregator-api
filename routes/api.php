<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserArticlePreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::any('ping', fn () => 'pong');

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('articles/categories', [ArticleController::class, 'categories']);
    Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);


    Route::apiResource('users.user-article-preferences', UserArticlePreferenceController::class)->only(['index', 'update']);
});
