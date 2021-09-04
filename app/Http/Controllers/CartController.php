<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Get products saved in session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCartItems(Request $request)
    {
        
        $cart = Session::get('cart', []);

        return response($cart, 200);
    }

     /**
     * Include items in the cart in a existing session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $products
     * @return \Illuminate\Http\Response
     */
    public function saveCartItems(Request $request)
    {
           
        $items = $request->input('cart');

        if(!$items){
            return response()->json(['error' => 'The cart data empty or contains an error'],400);
        }

       $validator = Validator::make($request->all(), [
            'cart.*.product' => 'required|numeric',
            'cart.*.qt' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        } 

        $cartData = Session::has('cart') ? Session::get('cart') : [];
        $subtotal = $cartData['subtotal'] ?? 0;
        $discount = $cartData['discount'] ?? 0;
        
        //print_r($cartData); return;
        
        //indicate if the item is already included in cart
        $productList = $cartData['products'] ?? [];
        $itensAlreadyInCart = array_column( $productList , 'id' );
        
        foreach($items as $item){
            $product = Product::find($item['product']);
            if(!$product){
                return response()->json(['error' => "Product " . $item['product'] . " doesn't exist"], 400);
            }
            
            //if the item is already in the cart just update its amount
            if( in_array( $item['product'] , $itensAlreadyInCart ) ){

                array_walk( $cartData['products'], function( &$value, $key ) use ($item){

                    if( $value['id'] ==  $item['product']){       
                        $value['qt'] += $item['qt'];
                    }
                }); 
               
            } else {
                $cartData['products'][] = [ 'id' => $product->id, 'name' => $product->name, 'price' => $product->price, 'qt' => $item['qt'] ];
            }    
            $subtotal += floatval($product->price) * intval($item['qt']);
        }

        $cartData['subtotal'] = $subtotal;
        $cartData['discount'] = $discount;
        $cartData['total'] = $subtotal - $discount;
        
        
        Session::put('cart', $cartData);
        Session::save();

        return response(['message' => 'Cart updated successfully', 'cart' => $cartData], 200);
    }


    /**
     * Increase a product Qt in cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int id
     * @return \Illuminate\Http\Response
     */
    public function addItemQt(Request $request, $id)
    {

        $product = Product::find($id);

        if(!$product){
            return response(['error' => "Product $id does't exist"], 400);
        }

       
        if(Session::has('cart')){

            $cartData = Session::get('cart');

            //indicate if the item is already included in cart
            $isItemInCart = in_array( $id, array_column( $cartData['products'] , 'id' ) );

            if($isItemInCart){

                foreach( $cartData['products'] as &$item ){
                    if( $item['id'] == $id ){
                        $item['qt'] += 1;
                    }
                }
            
            }else{
                array_push( $cartData['products'] , ['id' => $product->id , 'name' => $product->name, 'price' => $product->price, 'qt' => 1 ] );
            }

            //recalculate the total
            $cartData['subtotal'] += $product->price;
            $cartData['total'] = $cartData['subtotal'] - $cartData['discount'];
           
            
            Session::put('cart', $cartData);
            
        }else{

           //Create a cart session case it doesn't exist
            Session::put('cart', [
                'products' => [['id' => $product->id , 'name' => $product->name, 'price' => $product->price, 'qt' => 1 ]],
                'discount' => 0,
                'subtotal' => $product->price,
                'total' => $product->price
                ]);
        }

        Session::save();

        return response(['message' => 'Cart updated successfully', 'cart' => Session::get('cart')], 200);
    }

    /**
     * Reduce a product Qt in cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int id
     * @return \Illuminate\Http\Response
     */
    public function subtractItemQt(Request $request, $id)
    {
        $product = Product::find($id);

        if(!$product){
            return response(['error' => "Product $id does't exist"], 400);
        }

        if(Session::has('cart')){

            $cartData = Session::get('cart');

            foreach($cartData['products'] as $index => &$item){
                if($item['id'] == $id){

                    $price = $item['price'];

                    if($item['qt'] > 1){
                        $item['qt'] -= 1;
                        
                    }else{
                        //Remove the product from array
                        unset($cartData['products'][$index]);
                    }
                    
                    $cartData['subtotal'] -= $price;
                    $cartData['total'] = $cartData['subtotal'] - $cartData['discount'];
                }
            }
    
            Session::put('cart', $cartData);
            Session::save();
    
            return response(['message' => 'Cart updated successfully', 'cart' => $cartData], 200);

        }else{
            return response(['error' => 'The cart is empty'], 400);
        }

    }
    
    /**
     * Create a new shopping cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createCartItems(Request $request)
    {
            
        $items = $request->input('cart');

        if(!$items){
            return response()->json(['error' => 'The cart data empty or contains an error'],400);
        }

       $validator = Validator::make($request->all(), [
            'cart.*.product' => 'required|numeric',
            'cart.*.qt' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        } 

        $cartData = [];
        $subtotal = 0;
        $discount = 0;

        foreach($items as $item){
            $product = Product::find($item['product']);
            if(!$product){
                return response()->json(['error' => "Product " . $item['product'] . " doesn't exist"], 400);
            }
             
            $subtotal += floatval($product->price) * intval($item['qt']);
            $cartData['products'][] = ['id' => $product->id, 'name' => $product->name, 'price' => $product->price, 'qt' => $item['qt'] ];
        }

        $cartData['subtotal'] = $subtotal;
        $cartData['discount'] = $discount;
        $cartData['total'] = $subtotal - $discount;

          
        Session::put('cart', $cartData);
        Session::save();

        return response(['message' => 'Cart updated successfully', 'cart' => Session::get('cart',[])], 200);
    }

    /**
     * Save shopping cart products in session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteCartItem(Request $request, $id)
    {

        $cartData = Session::get('cart', []);

        if( !empty($cartData['products']) ){

        
            $product = Product::find($id);
            if(!$product){
                return response()->json(['error' => "Product " . $id . " doesn't exist"], 400);
            }

            $cartData['products']  = array_filter($cartData['products'], function($item) use($id) {

                return $item['id'] != $id;
            });

            

            Session::put('cart', $cartData);

            $this->calculateTotal();

            return response(['message' => 'Cart updated successfully', 'cart' => Session::get('cart',[])], 200);

        }else{
            return response(['error' => 'The cart is empty'], 400);
        }


        
        
    }

    /**
     * Clean cart session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cleanCart(Request $request)
    {
        Session::flush();

        return response(['message' => 'Cart session was cleaned'], 200);
    }

    /**
     * recalculate total and subtotal
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function calculateTotal()
    {

        $cartData = Session::get('cart');
       
        $subtotal = 0;
        $discount = $cartData['discount'] ?? 0;

        foreach($cartData['products'] as $item){
            $subtotal +=  floatval( $item['price'] ) * intval( $item['qt'] ); 
        }

        $cartData['subtotal'] = $subtotal;
        $cartData['total'] = $subtotal - $discount;
        

        Session::put('cart', $cartData);

       
    }
}
