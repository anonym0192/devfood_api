<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;


class CheckOutController extends Controller
{
    //

     
    /**
     * Genarate the checkout code for Pagseguro
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateCheckoutCode(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
                        'products.*.id' => 'required|numeric',
                        'products.*.name' => 'required|string',
                        'products.*.price' => 'required|regex:/\d+.\d{2}/',//regex:/^\d+(\.\d{1,2})?$/',
                        'products.*.qt' => 'required|integer',
                        'clientName'  => 'required|string|max:50',
                        'clientCPF' => 'nullable|string|max:20',
                        'cupom' => 'nullable|string|max:200',
                        'street' => 'required|string|max:80',
                        'number' => 'required|numeric',
                        'district' => 'required|string|max:50',
                        'postalCode' => 'required|string|max:10',
                        'city' => 'required|string|max:50',
                        'state' => 'required|string|max:2',
                        'country' => 'required|string|max:50',
                        'deliveryCost' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                        'discount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                        'redirectURL' => 'required|string|max:50',
                        'notificationURL' => 'required|string|max:50'
        ]);

        $products = $request->input('products');


        if(count($request->input('products')) < 1){
            return response()->json(['error' => 'The Shopping Cart is empty'], 400);
        }

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }


        
        $discount = floatval($request->input('discount')); 

        $delivery = floatval($request->input('deliveryCost'));

        
        \PagSeguro\Library::initialize();
        \PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
        \PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");
        
        $payment = new \PagSeguro\Domains\Requests\Payment();

        foreach($products as $prod){
           $payment->addItems()->withParameters(
                $prod['id'],
                $prod['name'],
                $prod['qt'],
                $prod['price'],
            ); 
        }
        

       
        $payment->setCurrency("BRL");
        $payment->setReference("LIBPHP000001");

        $payment->setRedirectUrl(env('APP_URL'). "/nofitication");

        // Set your customer information.
        $payment->setSender()->setName($request->input('clientName'));
       
        $payment->setSender()->setDocument()->withParameters(
            'CPF',
            $request->input('clientCPF')
        );

        $payment->setShipping()->setAddress()->withParameters(
            $request->input('street'),
            $request->input('number'),
            $request->input('district'),
           $request->input('postalCode'),
           $request->input('city'),
           $request->input('state'),
           $request->input('country'),
           $request->input('complement')
        );
        $payment->setShipping()->setCost()->withParameters($delivery);
        $payment->setShipping()->setType()->withParameters(\PagSeguro\Enum\Shipping\Type::NOT_SPECIFIED);


        //Add items by parameter using an array
        //$payment->addParameter()->withArray(['notificationURL', env('APP_URL').'/nofitication']);
        
        
        //$payment->setRedirectUrl(env('APP_URL')."/nofitication");
        $payment->setNotificationUrl(env('APP_URL')."/nofitication");

      
        
        try {
            $onlyCheckoutCode = true;
          
            $result = $payment->register(
                \PagSeguro\Configuration\Configure::getAccountCredentials(),
                $onlyCheckoutCode
             );
            
           
            return response()->json(['code' => $result->getCode()]);
            
                
        } catch (\Exception $e) {

            $xml = simplexml_load_string($e->getMessage());
            $errorMsg = $xml->error->message ?? $e->getMessage();

            return response()->json(['error' => $errorMsg]);
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
                $response = \PagSeguro\Services\Transactions\Notification::check(
                    \PagSeguro\Configuration\Configure::getAccountCredentials()
                );
            } else {
                throw new \InvalidArgumentException($_POST);
            }

            print_r($response);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }
}
