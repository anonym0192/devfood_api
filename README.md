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

-Products and Orders Pagination


## Routes

    
    /login Make Login [POST]
    /register Register a new user [POST]
    
    /categories List all product categories [GET]
    
    /products List all available products [GET]
    /products/{category}  Get products by category [GET]
    /product/{id} Get one product by id [GET]
    /products/search/{name} Search products by name [GET]
    
    /coupon/{code} Get a coupon by its code [GET]




    /me Get Logged User Info [GET]    
    /logout Logout [GET]

    /user/{id} Update user [PUT]
    /user/{id} Remove user [DELETE] 
      

    /product Create new product in store [POST] //ADMIN
    /product/{id} Update product info [PUT] //ADMIN
    /product/{id}  Remove product [DELETE] //ADMIN

    /orders List all orders from a user [GET]
    /order/{id} Get a specific order [GET] //ADMIN
    /order Create a new order [POST] 
    /order Update Order [PUT] //ADMIN
    /order/notify Change order status [POST]
    /order/{id} Remove order [DELETE]  //ADMIN


    /cart Get cart itens [GET]
    /cart Create a new cart [POST]
    /cart Save new itens in cart  [PUT]
    /cart/plus/{id} Add cart item quantity plus 1 [PUT]
    /cart/minus/{id} Reduce cart item quantity minus 1 [PUT]
    /cart/delete/{id} Add cart item quantity plus 1 [DELETE]
    /cart/clean Clean the cart [DELETE]

    /coupon/{code} Create Coupon [POST] //ADMIN
    /coupon/{code} Remove Coupon [DELETE] //ADMIN
    
    /checkout', [OrderController::class, 'generateCheckoutCode']);
    
# Login [/login]


### Make Login [POST]

# Logout [/logout]


### Make Logout [GET]

# User [/register]


### Create User  [POST]

# User [/user/{id}]

### Get Logged User Info  [GET]

# User [/me]

### Update User [PUT]

    
# User [/user{id}]

### Delete User [DELETE]

    
# User [/user{id}]


### List [GET]

# Products [/product/{id}]


### Update [PUT]

# Products [/product/{id}]


### Delete [DELETE]


## 

## Configuration

The following database info must be filled in .env file

    DB_CONNECTION=
    DB_HOST=
    DB_PORT=
    DB_DATABASE=
    DB_USERNAME=
    DB_PASSWORD=
    
The Pagseguro credentials like Pagseguro account e-mail and token is also required in the config file inside pagseguro paste
