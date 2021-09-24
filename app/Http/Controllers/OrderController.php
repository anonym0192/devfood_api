<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItems;
use App\Models\Transaction;

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

            $transaction = Transaction::where('order_id', $order['id'])->first();

            switch($transaction->status){
                case '0':
                case '1': // Aguardando Pgto.
                case '2': // Em análise
                    $order['status'] = 'Aguardando Pgto';
                    break;
                case '3': // Paga
                case '4': // Disponível
                    $order['status'] = 'Aprovado';
                    break;
                case '6': // Devolvida
                case '7': // Cancelada
                    $order['status'] = 'Cancelado';
                    break;
            }

            
            /*if($transaction){
                switch($transaction['type']){
                    case '0':
                        $order['payment_type'] = 'Cartão de Debito';
                        break;
                    case '1':
                        $order['payment_type'] = 'Cartão de Crédito';
                        break;
                }
            } */
            
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
            return response()->json([$validator->$errors], 400);
        }

        $transaction = Transaction::where('order_id', $id)->first();

        if($transaction){

            $transaction['status'] = $request->input('status');
            $transaction->save();

            return response()->json(['msg' => "Transaction status for order $id changed"], 200);
        }else{
            return response()->json(['error' => 'Invalid transaction data'], 400);
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
                'paymentType' => 'required|numeric',
                'deliveryCost' => 'numeric|regex:/^\d+(\.\d{1,2})?$/',
                'cupom' => 'nullable|string|max:200',
                'street' => 'required|string|max:80',
                'complement' => 'nullable|string|max:80',
                'number' => 'required|numeric',
                'postalCode' => 'required|string|max:10',
                'city' => 'nullable|string|max:200',
                'state' => 'nullable|string|min:2|max:2',
                'transactionCode' => 'required|string|max:200'
              ]);  

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        //id_address, cumpom, products[{id, qt}], payment_data , payment_type, delivery_cost 

        $total = 0;

        $products = $request->input('products');
        
        foreach($products as $item){
            $product = Product::find($item['id']);
            if(!$product){
                return response()->json(['error' => "Product $id doesn't exist"], 400);
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

        
        $order->save();

       
        foreach($products as $item ){
            $OrderItems = new OrderItems;
            $OrderItems->product_id = $item['id'];
            $OrderItems->qt = $item['qt'];
            $OrderItems->order_id = $order->id;
            $OrderItems->save();
        }

        $transactionCode = $request->input('transactionCode');
        $paymentType = $request->input('paymentType', 1);

        $this->generateTransaction( $transactionCode, $order->id , 1);



        return response()->json(['msg' => "Order created successfully", 'order' => $order]);

    }

       /**
     * Create a new transaction
     *
     * @param  String  $transactionCode
     * @param  int  $orderId
     * @param  int  $paymentType
     * @return Array
     */
    private function generateTransaction(String $transactionCode, int $orderId, int $paymentType = 1)
    {
        //
        $transaction = new Transaction;

        $transaction->code = $transactionCode;
        $transaction->mode = $paymentType;
        $transaction->type = 1;
        $transaction->status = 1;
        $transaction->user_id = Auth::user()->id;
        $transaction->order_id = $orderId;
        $transaction->save();
          

    }


}
