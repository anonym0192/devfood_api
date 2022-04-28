<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\CityController;
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

Route::get('/cities', [CityController::class, 'getCities']);

Route::get('/districts', [DistrictController::class, 'getDistrictsFromCity']);





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
    Route::post('/image/product/{id}', [ProductController::class, 'updateProductImage']); //ADMIN

    Route::post('/category', [CategoryController::class, 'store']); //ADMIN
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']); //ADMIN
    Route::post('/image/category/{id}', [CategoryController::class, 'updateCategoryImage']); //ADMIN

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']); //ADMIN
    Route::post('/order', [OrderController::class, 'store']); 
   // Route::put('/order/{id}', [OrderController::class, 'update']); //ADMIN
    Route::post('order/notify', [OrderController::class, 'notifyPayment']);
    Route::delete('/order/{id}', [OrderController::class, 'destroy']);  //ADMIN

    Route::get('/cart', [CartController::class, 'getCartItems']);
    Route::post('/cart', [CartController::class, 'createCartItems']);
    Route::put('/cart', [CartController::class, 'saveCartItems']);
    Route::put('/cart/plus/{id}', [CartController::class, 'addItemQt']);
    Route::put('/cart/minus/{id}', [CartController::class, 'subtractItemQt']);
    Route::delete('/cart/delete/{id}', [CartController::class, 'deleteCartItem']);
    Route::put('/cart/remove/{id}', [CartController::class, 'subtractItemQt']);
    Route::delete('/cart/clean', [CartController::class, 'cleanCart']);

    Route::post('coupon', [CouponController::class, 'createCoupon']);
    Route::delete('coupon/{code}', [CouponController::class, 'removeCoupon']);

    Route::post('/checkout', [CheckOutController::class, 'generateCheckoutCode']);

    Route::post('/city', [CityController::class, 'addCity']);
    Route::delete('/city/{id}', [CityController::class, 'removeCity']);
    
    Route::post('/district', [DistrictController::class, 'addDistrict']);
    Route::put('/district/disable/{id}', [DistrictController::class, 'disableDistrict']);
    Route::put('/district/enable/{id}', [DistrictController::class, 'enableDistrict']);
    Route::delete('/district/{id}', [DistrictController::class, 'removeDistrict']);
    
    

});

