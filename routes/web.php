<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Team\TeamDashboardController;
use App\Http\Controllers\Team\TeamLoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('blog')->name('blog.')->controller(BlogController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{slug}', 'detail')->name('detail');
});

Route::prefix('notification')->name('notification.')->controller(NotificationController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/send', 'send')->name('send');
    Route::post('/mark-all-read', 'mark_all_read')->name('mark-all-read');
});

Route::middleware('guest')->group(function () {
    Route::prefix('login')->name('login.')->controller(LoginController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'login')->name('login');
        Route::get('/two_step_email', 'two_step_email')->name('two_step_email');
        Route::post('/verify_email', 'verify_email')->name('verify_email');
        Route::get('/two_step_mobile', 'two_step_mobile')->name('two_step_mobile');
        Route::post('/verify_mobile', 'verify_mobile')->name('verify_mobile');
        Route::get('/two_step_google2fa', 'two_step_google2fa')->name('two_step_google2fa');
        Route::post('/verify_google2fa', 'verify_google2fa')->name('verify_google2fa');
    });
});

Route::middleware(['auth', 'updateLastActive'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/logout', 'logout')->name('logout');
        Route::post('/update', 'update')->name('update');
        Route::post('/verify-google2fa', 'verify_google2fa')->name('verify-google2fa');
    });
});

Route::prefix('team')->name('team.')->group(function () {
    Route::middleware('guest:team')->group(function () {
        Route::prefix('login')->name('login.')->controller(TeamLoginController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'login')->name('login');
        });
    });

    Route::middleware('auth:team')->group(function () {
        Route::prefix('dashboard')->name('dashboard.')->controller(TeamDashboardController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/logout', 'logout')->name('logout');
        });
    });
});
