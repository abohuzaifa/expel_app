<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\WishListController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\Location;

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
// public routes
Route::post("/register", [AuthController::class,"register"])->name('register');
Route::post("/sellerRegister", [AuthController::class,"sellerRegister"])->name('sellerRegister');
Route::post("/login", [AuthController::class,"login"])->name('login');
Route::post("/reset", [AuthController::class,"reset"])->name('reset');
Route::post('resetRequest', [AuthController::class,'resetRequest'])->name('resetRequest');
Route::post('otpVarification', [AuthController::class,'otpVarification'])->name('otpVarification');
Route::post('location', [Location::class,'location'])->name('location');
Route::get('/allCategories', [CategoryController::class,'categories'])->name('allCategories');
Route::get('/getAllShops/{id}', [ShopController::class, 'getAllShops'])->name('getAllShops');
Route::get('/getProduct/{id}', [ProductController::class,'getProduct'])->name('getProduct');
Route::get('/productList/{id}', [ProductController::class, 'productList'])->name('productList');
Route::get('/getAllCategory', [CategoryController::class, 'getAllCategories'])->name('getAllCategories');
Route::post("/searchProduct", [ProductController::class, 'searchProduct'])->name('searchProduct');
Route::get("/getBanners", [BannerController::class, 'getBanners'])->name('getBanners');
Route::get('/getCategory/{id}', [CategoryController::class, 'getCategory'])->name('getCategory');
Route::get('/adminChoiceCategories', [CategoryController::class,'adminChoiceCategories'])->name('adminChoiceCategories');


// protected routes
Route::group(["middleware"=> "auth:sanctum"], function () {
    // User requests
    Route::get("/user", [AuthController::class,"user"])->name('user');
    Route::get("/logout", [AuthController::class,"logout"])->name('logout');
    Route::get('/deleteUser/{id}', [AuthController::class, 'delete'])->name('deleteUser');
    Route::get('/userList/{id}', [AuthController::class,'userList'])->name('userList');
    Route::post('/updateUser', [AuthController::class,'updateUser'])->name('updateUser');
    Route::post('/setLocation', [AuthController::class, 'setLocation'])->name('setLocation');
    Route::post('/updateProfileImage/{id}', [AuthController::class, 'updateProfileImage'])->name('updateProfileImage');
    Route::post('/cardDetail', [AuthController::class, 'cardDetail'])->name('cardDetail');
    Route::post('/cardDetailUpdate/{id}', [AuthController::class, 'cardDetailUpdate'])->name('cardDetailUpdate');
    Route::post('/deleteCardDetails/{id}', [AuthController::class, 'deleteCardDetails'])->name('deleteCardDetails');


    // Categories requests
    Route::post('/createCategory', [CategoryController::class,'create'])->name('createCategory');
    Route::get('/deleteCategory/{id}', [CategoryController::class, 'delete'])->name('deleteCategory');
    Route::post('/updateCategory/{id}', [CategoryController::class, 'update'])->name('updateCategory');
    Route::get('/sellerCategories', [CategoryController::class, 'sellerCategories'])->name('sellerCategories');

    // shop requests
    Route::post('/createShop', [ShopController::class,'create'])->name('createShop');
    Route::post('/updateShop/{id}', [ShopController::class, 'updateShop'])->name('updateShop');
    Route::get('/getShop/{id}', [ShopController::class, 'get'])->name('getShop');
    Route::get('/deleteShop/{id}', [ShopController::class, 'delete'])->name('deleteShop');
    Route::get('/allShops', [ShopController::class,'shops'])->name('allShops');

    //Products requests
    Route::post('/createProduct', [ProductController::class, 'createProduct'])->name('createProduct');
    Route::get('/deleteProduct/{id}', [ProductController::class, 'delete'])->name('deleteProduct');
    Route::post('/updateProduct/{id}', [ProductController::class, 'updateProduct'])->name('updateProduct');
    Route::get('/allProducts/{id}', [ProductController::class,'products'])->name('allProducts');
    Route::get('/sellerProducts', [ProductController::class, 'sellerProducts'])->name('sellerProducts');

    // Cart requests
    Route::post('/cart', [CartController::class,'cart'])->name('cart');
    Route::get('/cartView', [CartController::class, 'cartView'])->name('cartView');
    Route::post('/updateQunatity/{id}', [CartController::class, 'updateQunatity'])->name('updateQunatity');
    Route::get('/removeItem/{id}', [CartController::class, 'removeItem'])->name('removeItem');
    Route::post('/cartOrder', [CartController::class,'cartOrder'])->name('cartOrder');

    // Wish list Apis
    Route::post('/addWishList', [WishListController::class, 'add'])->name('addWishList');
    Route::get('/userWishList', [WishListController::class, 'get'])->name('userWishList');
    Route::get('/removeItemFromWishList/{id}', [WishListController::class, 'removeItem'])->name('removeItemFromWishList');

    //payment method Apis
    Route::post('/createPaymentMethod', [PaymentController::class, 'create'])->name('createPaymentMethod');
    Route::get('/paymentMethodList', [PaymentController::class, 'list'])->name('paymentMethodList');

    //order apis
    Route::post('/createOrder', [OrderController::class, 'create'])->name('createOrder');
    Route::post('/manualOrder', [OrderController::class, 'manualOrder'])->name('manualOrder');
    Route::get('/orderList', [OrderController::class, 'orderList'])->name('orderList');
    Route::get('/getOrder/{id}', [OrderController::class, 'get'])->name('getOrder');
    Route::post('/orderPaymentStatus', [OrderController::class, 'orderPaymentStatus'])->name('orderPaymentStatus');
    Route::get("/orderStatus/{id}", [OrderController::class, 'orderStatus'])->name('orderStatus');
    Route::get("/orderListByUserId", [OrderController::class, 'orderListByUserId'])->name('orderListByUserId');
    Route::get("/notifiOrders", [OrderController::class, 'notifiOrders'])->name('notifiOrders');
    Route::get("/recentOrders", [OrderController::class, 'recentOrders'])->name('recentOrders');
    Route::post("/readNotification/{id}", [OrderController::class, 'readNotification'])->name('readNotification');
    Route::get("/sellerTotalOrders", [OrderController::class, 'sellerTotalOrders'])->name('sellerTotalOrders');
    Route::post("/changeOrderStatus/{status}", [OrderController::class, 'changeOrderStatus'])->name('changeOrderStatus');
    Route::get("/recentOrderItems", [OrderController::class, 'recentOrderItems'])->name('recentOrderItems');
    Route::get("/manualOrderSellers", [OrderController::class, 'manualOrderSellers'])->name('manualOrderSellers');
    Route::post("/manualOrderProcess", [OrderController::class, 'manualOrderProcess'])->name('manualOrderProcess');
    Route::get("/buyerManualOrderNotify", [OrderController::class, 'buyerManualOrderNotify'])->name('buyerManualOrderNotify');
    Route::get("/allOrderProducts", [OrderController::class, 'allOrderProducts'])->name('allOrderProducts');

    // Chat APIS
    Route::post('/sendMessage', [MessageController::class, 'sendMessage'])->name('sendMessage');
    Route::post('/userChat', [MessageController::class, 'chatMessages'])->name('userChat');
    Route::get("/allShopChatNotifications/{id}", [MessageController::class, 'allShopChatNotifications'])->name('allShopChatNotifications');
    Route::get("/allUserChatNotifications/{id}", [MessageController::class, 'allUserChatNotifications'])->name('allUserChatNotifications');
    Route::get("/getFullChatByChatID/{id}", [MessageController::class, 'getFullChatByChatID'])->name('getFullChatByChatID');

    Route::post("/charge_in", [WalletController::class, 'charge_in'])->name('charge_in');
    Route::get("/wallet", [WalletController::class, 'wallet'])->name('wallet');
    Route::post("/walletTransfer", [WalletController::class, 'walletTransfer'])->name('walletTransfer');
    Route::get("/walletHistory", [WalletController::class, 'walletHistory'])->name('walletHistory');
    Route::get("/walletNotification", [WalletController::class, 'walletNotification'])->name('walletNotification');
    Route::get("/walletReadNotify/{flag}", [WalletController::class, 'walletReadNotify'])->name('walletReadNotify');

});
