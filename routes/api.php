<?php

use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\UserController;
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

Route::prefix('v1')->middleware(['jwt'])->group(function () {
    Route::prefix('admin')
        ->controller(AdminController::class)->group(function () {
            Route::post('login', 'login')
                ->withoutMiddleware('jwt')
                ->name('v1.admin.login');

            Route::post('create', 'register')
                ->name('v1.admin.registration');

            Route::post('logout', 'logout')
                ->name('v1.admin.logout');

            Route::get('user-listing', 'listUsers')
                ->name('v1.admin.user_listing');
        });

    Route::prefix('user')
        ->controller(UserController::class)->group(function () {
            Route::post('login', 'login')
                ->withoutMiddleware('jwt')
                ->name('v1.user.login');

            Route::post('create', 'register')
                ->withoutMiddleware('jwt')
                ->name('v1.user.registration');

            Route::post('logout', 'logout')
                ->name('v1.user.logout');

            Route::put('edit/{uuid}', 'edit')
                ->name('v1.user.edit');
        });
});

