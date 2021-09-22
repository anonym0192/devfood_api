<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DeliveryAreaController;
use App\Http\Controllers\CheckOutController;
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

/* Public Routes */

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [UserController::class, 'register']);

Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{category}', [ProductController::class, 'getByCategory']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/products/search/{name}', [ProductController::class, 'search']);


Route::get('/coupon/{code}', [CouponController::class, 'useCoupon']);

//Route::get('/deliverycalc', [OrderController::class, 'deliveryCalculate']);

Route::get('/cities', [DeliveryAreaController::class, 'getCities']);
Route::get('/districts', [DeliveryAreaController::class, 'getDistricts']);




/* Protected Routes */

Route::group(['middleware' => ['auth:sanctum']], function(){
   
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh', [AuthController::class, 'refresh']);

    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
      
    Route::post('/product', [ProductController::class, 'store']);       //ADMIN
    Route::put('/product/{id}', [ProductController::class, 'update']);   //ADMIN
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);  //ADMIN

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']); //ADMIN
    Route::post('/order', [OrderController::class, 'store']); 
    Route::put('/order', [OrderController::class, 'update']); //ADMIN
    Route::post('order/notify', [TransactionController::class, 'notifyPayment']);
    Route::delete('/order/{id}', [OrderController::class, 'destroy']);  //ADMIN

    Route::get('/cart', [CartController::class, 'getCartItems']);
    Route::post('/cart', [CartController::class, 'createCartItems']);
    Route::put('/cart', [CartController::class, 'saveCartItems']);
    Route::put('/cart/plus/{id}', [CartController::class, 'addItemQt']);
    Route::put('/cart/minus/{id}', [CartController::class, 'subtractItemQt']);
    Route::delete('/cart/delete/{id}', [CartController::class, 'deleteCartItem']);
    Route::put('/cart/remove/{id}', [CartController::class, 'subtractItemQt']);
    Route::delete('/cart/clean', [CartController::class, 'cleanCart']);

    Route::post('coupon/{code}', [CouponController::class, 'createCoupon']);
    Route::delete('coupon/{code}', [CouponController::class, 'removeCoupon']);

    Route::post('/checkout', [CheckOutController::class, 'generateCheckoutCode']);

    Route::post('/city', [DeliveryAreaController::class, 'addCity']);
    Route::post('/district', [DeliveryAreaController::class, 'addDistrict']);

    Route::put('/district/disable/{id}', [DeliveryAreaController::class, 'disableDistrict']);
    Route::put('/district/enable/{id}', [DeliveryAreaController::class, 'enableDistrict']);

    Route::delete('/city/{id}', [DeliveryAreaController::class, 'removeCity']);
    Route::delete('/district/{id}', [DeliveryAreaController::class, 'removeDistrict']);
    

});

