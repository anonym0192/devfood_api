<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItems;
//use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    
    //
     /**
     * Display a listing of the resource.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $response = [];

        $orders_per_page = 10;

        $page = $request->query('page');

        if( !ctype_digit($page) || $page < 1 ){
            $page = 1;
        }

        $offset = ($page - 1) * $orders_per_page;
        

        $orders = Order::where('user_id', Auth::user()->id);

        
        $orders_total =  $orders->count();
        
        $pages = ceil( $orders_total / $orders_per_page );
        
        
        $orders = $orders->offset($offset)->limit($orders_per_page)->orderBy('created_at')->get();
        

        foreach($orders as $order){

            $items = OrderItems::where('order_id', $order['id'])->get();


            $order['status'] = getStatusDescription($order->status);

            
            $productList = [];
        
            foreach($items as $item){
                
                $product = Product::find($item['product_id']);
                if($product){
                    $productList[] = $product;
                }
            }
    
            $order['products'] = $productList;  

        }

        $response['orders'] = $orders;
        $response['total'] = $orders_total;
        $response['current_page'] = $page < 1 ? 1 : intval($page);
        $response['total_pages'] = $pages;
              
        return response()->json( $response ); 


    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = 0)
    {
        //
        $response = [];

        $order = Order::where('id',$id,)->where('user_id', Auth::user()->id)->first();
        
        if(!$order){
            return response()->json(['error' => 'Unauthorized'], 401);
        } 

        $order['user'] =  Auth::user();

        $items = OrderItems::where('order_id', $order['id'])->get();
  
        $productList = [];

        foreach($items as $item){
            
            $product = Product::find($item['product_id']);
            if($product){
                $productList[] = $product;
            }
        }

        $order['products'] = $productList;
        
        return response()->json(['order' => $order]); 

    }

   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|numeric',
            ]); 

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        $status = $request->input('status');


        try{

            
            $this->changeOrderStatus($id, $status);
            return response()->json(['msg' => "Transaction status for order $id changed"], 200);
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 400);
        }
        
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = 0)
    {
        //
        $response = [];
    }

    /**
     * Calculate the delivery for the order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deliveryCalculate(Request $request, $id = 0)
    {
        //
        $response = [];
        
        $request->validate([
            'id_address' => 'required|numeric',
            'street' => 'required|string|min:3|max:200',
            'zipcode' => 'required|numeric|max:500',
            'city' => 'required|string|max:50',
            'state' => 'required|numeric',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
       
        if(!$request->input('products')){
            return response()->json(['error' => 'The products data is empty or contains an error'],400);
        }


        $validator = Validator::make($request->all(), [
                'products.*.id' => 'required|numeric',
                'products.*.qt' => 'required|numeric',
                'deliveryCost' => 'numeric|regex:/^\d+(\.\d{1,2})?$/',
                'cupom' => 'nullable|string|max:200',
                'street' => 'required|string|max:80',
                'complement' => 'nullable|string|max:80',
                'number' => 'required|numeric',
                'postalCode' => 'required|string|max:10',
                'city' => 'nullable|string|max:200',
                'state' => 'nullable|string|min:2|max:2',
              ]);  

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        //id_address, cumpom, products[{id, qt}], payment_data , payment_type, delivery_cost 

        DB::beginTransaction(); 

        try{
            $total = 0;

            $products = $request->input('products');
            
            foreach($products as $item){
                $product = Product::find($item['id']);
                if(!$product){
                    return response()->json(['error' => "Product ". $item['id']." doesn't exist"], 400);
                }
                $total += floatval($product->price) * intval($item['qt']);
            }
            
            /*
                Payment process
            */

            $deliveryCost = $request->input('deliveryCost') ?? 0;
            $total += $deliveryCost;

            $order = new Order;
            $order->user_id = Auth::user()->id;
            $order->total = $total;
            
            $order->street = $request->input('street');
            $order->number = $request->input('number');
            $order->complement = $request->input('complement');
            $order->postal_code = $request->input('postalCode');
            $order->city = $request->input('city');
            $order->state = $request->input('state');
            $order->delivery_cost = $deliveryCost;
            $order->status = 1;
            
            $order->save();

        
            foreach($products as $item ){
                $OrderItems = new OrderItems;
                $OrderItems->product_id = $item['id'];
                $OrderItems->qt = $item['qt'];
                $OrderItems->order_id = $order->id;
                $OrderItems->save();
            }

            DB::commit();

            return response()->json(['msg' => "Order created successfully", 'order' => $order]);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }



        

    }

        /**
     * Notify and change the payment status
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function notifyPayment(Request $request)
    {
        //
        
        \PagSeguro\Library::initialize();
        \PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
        \PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");

       try {
            if (\PagSeguro\Helpers\Xhr::hasPost()) {
                
                $credentials = \PagSeguro\Configuration\Configure::getAccountCredentials();

                $notificationCode = $request->input('notificationCode');

                $pagSeguroNotificationEndpoint = "";

                if(env('APP_ENV') == "local"){
                    $pagSeguroNotificationEndpoint = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/$notificationCode";
                }else{
                    $pagSeguroNotificationEndpoint = "https://ws.pagseguro.uol.com.br/v3/transactions/notifications/$notificationCode";
                }

                $response = Http::withHeaders([
                    'Accept' => 'application/xml',
                    'Content-Type' => 'application/json'
                ])->get($pagSeguroNotificationEndpoint, [
                    'email' => $credentials->getEmail(),
                    'token' => $credentials->getToken()
                ]);
               
    
                $xml = simplexml_load_string($response);

                $reference = $xml->reference;
                $status = $xml->status;

                $this->changeOrderStatus($reference, $status);
                 
                
            } else {
                throw new \InvalidArgumentException();
            }
 
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * Notify and change the payment status
     *
     * @param string $reference
     * @param string $status
     * @return \Illuminate\Http\Response
     */
    private function changeOrderStatus($reference, $status)
    {
        
        $status = formatOrderStatus($status);

        $order = Order::find($reference);

        if($order){
            $order->status = $status;
            $order->save();

            return true;
        }else{
            throw new Exception("Order not found!") ;
        }

    }

}
