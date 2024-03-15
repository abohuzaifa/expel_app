<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuccessController;
use App\Http\Controllers\LangController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethod;
use App\Http\Controllers\WalletController;

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
// Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('lang/{locale}', [LangController::class, 'setLocale'])->name('setLocale');
Route::get('/success/{id}', [SuccessController::class, 'index'])->name('success');
Route::get('/charge_in/{id}', [SuccessController::class, 'charge_in'])->name('charge_in');
Route::get('/', function () {
    return redirect()->route('home');
});
// routes/web.php


Auth::routes();
Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::resource('roles', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('users', UserController::class);
    Route::get('sellers_list', [UserController::class,'sellers_list'])->name('sellers_list');
    Route::get('sellers_active/{id}', [UserController::class,'sellers_active'])->name('sellers_active');
    Route::get('sellers_inactive/{id}', [UserController::class,'sellers_inactive'])->name('sellers_inactive');
    Route::resource('category', CategoryController::class);
    Route::resource('shop', ShopController::class);
    Route::resource('product', ProductController::class);
    Route::post('category/add_favourit', 'CategoryController@add_favourit');
    Route::resource('notifications', NotificationController::class);

    Route::resource('wallet', WalletController::class);
    Route::resource('banners', BannerController::class);

    Route::get('banner_active/{id}', [BannerController::class,'banner_active'])->name('banner_active');
    Route::get('banner_inactive/{id}', [BannerController::class,'banner_inactive'])->name('banner_inactive');

    Route::resource('orders', OrderController::class);
    Route::resource('payment_method', PaymentMethod::class);
    Route::get('active/{id}', [PaymentMethod::class,'active'])->name('active');
    Route::get('inactive/{id}', [PaymentMethod::class,'inactive'])->name('inactive');
});


