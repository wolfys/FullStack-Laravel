<?php

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('registration', [\App\Http\Controllers\AuthController::class, 'registration']);
    Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('refresh', [\App\Http\Controllers\AuthController::class, 'refresh']);
    Route::post('me', [\App\Http\Controllers\AuthController::class, 'me']);
    Route::post('activateEmail',[\App\Http\Controllers\AuthController::class,'getActivateEmail']);
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'front',
], function () {
   Route::get('category',[\App\Http\Controllers\CategoriesController::class,'index']);
    Route::post('category/find',[\App\Http\Controllers\CategoriesController::class,'show']);
    Route::post('product/find',[\App\Http\Controllers\ProductController::class,'show']);
});

Route::group([
    'prefix' => 'front',
    'middleware' => 'jwt.verify'
], function () {
    Route::post('profile', [\App\Http\Controllers\ProfileController::class, 'index']);
});

Route::group([
    'prefix' => 'admin',
    'middleware' => [
        'jwt.verify',
        'admin'
    ]
], function () {
    Route::post('users/all',[\App\Http\Controllers\UsersController::class,'index']);
    Route::post('category/save',[\App\Http\Controllers\CategoriesController::class,'edit']);
});
