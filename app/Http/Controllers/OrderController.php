<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    private OrderRepository $orderRepository;
    
    public function __construct(OrderRepository $orderRepository){
        $this->orderRepository = $orderRepository; 
    }

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

        $ordersByPage = 10;

        $page = $request->query('page');

        if( !ctype_digit($page) || $page < 1 ){
            $page = 1;
        }

        $response = $this->orderRepository->getOrdersFromLoggedUser($page, $ordersByPage);

        return response()->json($response);

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

        $order = $this->orderRepository->getById($id);

        if(!$order){
            return response()->json(['error' => 'Order not found'], 404);
        }
    
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

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|numeric',
            ]); 

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        $status = $request->input('status');

        try{
            
            $this->orderRepository->changeOrderStatus($id, $status);
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
            
        
        try{
            
            $order = $this->orderRepository->create($request->only([
                'products', 
                'deliveryCost', 
                'cumpom', 
                'street', 
                'complement', 
                'number',
                'postalCode',
                'city',
                'state']));
            
            return response()->json(['msg' => "Order created successfully", 'order' => $order]);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()] , 404);
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

                $this->orderRepository->changeOrderStatus($reference, $status);
                 
                
            } else {
                throw new \InvalidArgumentException();
            }
 
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }


}
