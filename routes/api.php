<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\MaterialController;
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

Route::post('login',[LoginController::class,'login']);


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('v1/users',UserController::class); //->only(['index','show']);
    // Agrega más rutas aquí...
});
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('v1/material',MaterialController::class); //->only(['index','show']);
    // Agrega más rutas aquí...
});

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */
