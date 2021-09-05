# Devfood api
Backend version of a REST API using Laravel, the payment of item is made using a Pagseguro integrated LIB 

## Features
-Create a new User , Delete and Update its profile info

-A User who is admin can Register a new Product

-Login Authentication using a token

-List Products

-Search Products by name and by category

-Save, Update and Delete Cart Itens

-A User who is admin can Create and Delete Coupons

-Generate Checkout code using Pagseguro LIB

-List Orders

-Admin  can Create , Update and Delete new orders


## Routes

 Route::get('/me', [AuthController::class, 'me']);
    Route::get('/logout', [AuthController::class, 'logout']);
    //Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
      

    Route::post('/product', [ProductController::class, 'store']);       //ADMIN
    Route::put('/product/{id}', [ProductController::class, 'update']);   //ADMIN
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);  //ADMIN

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']); //ADMIN
    Route::post('/order', [OrderController::class, 'store']); 
    Route::put('/order', [OrderController::class, 'update']); //ADMIN
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

    Route::post('coupon/{code}', [CouponController::class, 'createCoupon']);
    Route::delete('coupon/{code}', [CouponController::class, 'removeCoupon']);

## 

## Configuration

For this project to work you need to fill the .env file using your local info. You need the MongoDB running in your local machine, don't forget to include the path of your Mongo database collection in the "DATABASE" field.
