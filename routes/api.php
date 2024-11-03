<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
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

Route::group(['middleware' => 'api'], function ($router) {
    /*------------------------------------ Alimal Digital Api's ------------------------------------*/
    Route::group(['prefix' => 'app/v1'], function ($router) {

        /*------------------------------------ Start Auth Api's ------------------------------------*/
        Route::group(['prefix' => 'auth'], function ($router) {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', function () {
                return auth()->user();
            });
        });
        /*------------------------------------ End Auth Api's ------------------------------------*/

        /*------------------------------------ Start Archives Api's ------------------------------------*/
        Route::group(['prefix' => 'archives'], function ($router) {
            Route::get('all', [ArchiveController::class, 'index']);
            Route::get('get/{id}', [ArchiveController::class, 'get']);
            Route::post('upload', [ArchiveController::class, 'upload']);
            Route::get('download', [ArchiveController::class, 'download']);
            Route::delete('delete/{id}', [ArchiveController::class, 'destroy']);
        });
        /*------------------------------------ End Archives Api's ------------------------------------*/

        /*------------------------------------ Start users Api's ------------------------------------*/
        Route::group(['prefix' => 'users'], function ($router) {
            Route::get('all', [UserController::class, 'index']);
            Route::get('get/{id}', [UserController::class, 'get']);
            Route::post('add', [UserController::class, 'store']);
            Route::put('update/{id}', [UserController::class, 'update']);
            Route::delete('delete/{id}', [UserController::class, 'destroy']);
        });
        /*------------------------------------ End users Api's ------------------------------------*/

        /*------------------------------------ Start Categories Api's ------------------------------------*/
        Route::group(['prefix' => 'categories'], function ($router) {
            Route::get('all', [CategoryController::class, 'index']);
            Route::get('get/{id}', [CategoryController::class, 'show']);
            Route::post('add', [CategoryController::class, 'store']);
            Route::put('update/{id}', [CategoryController::class, 'update']);
            Route::delete('delete/{id}', [CategoryController::class, 'destroy']);

            Route::get('getArchivesBasedOnCategory/{category_id}', [CategoryController::class, 'getArchivesBasedOnCategory']);
        });
        /*------------------------------------ End Categories Api's ------------------------------------*/
    });
});
