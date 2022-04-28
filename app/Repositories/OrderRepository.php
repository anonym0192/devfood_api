<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class OrderRepository implements BaseRepository{

    
    public function getById($id){

        $order = Order::find($id);
        
        if(!$order){
            return null;
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

        return $order;

    }

   public function getAll(){

        
        $orders = Order::all();
    
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

        return $orders;
              
   }

   public function getOrdersFromLoggedUser(int $page = 1, int $ordersByPage = 200){

        $response = [];

        $offset = ($page - 1) * $ordersByPage;
        

        $orders = Order::where('user_id', Auth::user()->id);

        
        $ordersTotal =  $orders->count();
        
        $pages = ceil( $ordersTotal / $ordersByPage );
        
        
        $orders = $orders->offset($offset)->limit($ordersByPage)->orderBy('created_at')->get();
        

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
        $response['total'] = $ordersTotal;
        $response['current_page'] = $page < 1 ? 1 : intval($page);
        $response['total_pages'] = $pages;

    return $response;
   }

   public function create(array $data){

        DB::beginTransaction(); 

    
        $total = 0;

        $products = $data['products'];
        
        foreach($products as $item){
            $product = Product::find($item['id']);
            if(!$product){
                throw new \Exception("Product ". $item['id']." doesn't exist");
            }
            $total += floatval($product->price) * intval($item['qt']);
        }
        

        $deliveryCost = $data['deliveryCost'] ?? 0;
        $total += $deliveryCost;

        $order = new Order;
        $order->user_id = Auth::user()->id;
        $order->total = $total;
        
        $order->street = $data['street'] ?? '';
        $order->number = $data['number'] ?? '';
        $order->complement = $data['complement'] ?? '';
        $order->postal_code = $data['postalCode'] ?? '';
        $order->city = $data['city'] ?? '';
        $order->state = $data['state'] ?? '';
        $order->delivery_cost = $deliveryCost ?? 0;
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

        return $order;
    

   }

   public function deleteById($id){

        $deletedOrder = Order::destroy($id);
       
        return $deletedOrder;
   }

    /**
     * Notify and change the payment status
     *
     * @param string $reference
     * @param string $status
     * @return \Illuminate\Http\Response
     */
    public function changeOrderStatus($reference, $status)
    {
        
        $status = formatOrderStatus($status);

        $order = Order::find($reference);

        if($order){
            $order->status = $status;
            $order->save();

            return true;
        }else{
            throw new \Exception("Order not found!") ;
        }

    }

    public function updateById($id, array $data){

    }

    public function cancelOrderById($id){

        $order = Order::find($id);

        if($order){
            $order->status = 3;
            $order->save();

            return true;
           
        }else{
            throw new \Exception("Order not found!") ;
        }
      
        
   }
 
} 