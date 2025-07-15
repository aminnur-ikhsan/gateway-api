<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MainController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignOutController;

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

Route::get('/', [MainController::class, 'index']);
Route::post('/registration', [RegistrationController::class, 'index']);
Route::post('/sign-in', [SignInController::class, 'index']);
Route::get('/sign-out', [SignOutController::class, 'index']);
Route::middleware('auth:sanctum')->get('/validate-token', [SignInController::class, 'validateToken']);
Route::middleware('auth:sanctum')->get('/active-tokens', [SignInController::class, 'getActiveTokens']);

Route::group(['middleware' => 'provider'], function () {
    Route::post('/provider/registration', [RegistrationController::class, 'indexProvider']);
    Route::post('/provider/sign-in', [SignInController::class, 'indexProvider']);
    Route::get('/provider/validate-token', [SignInController::class, 'validateTokenProvider']);
    Route::get('/provider/sign-out', [SignOutController::class, 'indexProvider']);
});
