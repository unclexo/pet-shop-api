<?php

use App\Http\Controllers\Auth\AdminController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1/admin')
    ->controller(AdminController::class)->group(function () {
        Route::post('login', 'login')->name('v1.admin.login');

        Route::post('create', 'register')
            ->name('v1.admin.registration');
});
