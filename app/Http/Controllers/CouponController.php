<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CouponRepository;

class CouponController extends Controller
{
    //

    private CouponRepository $couponRepository;

    public function __construct(CouponRepository $couponRepository){
        $this->couponRepository = $couponRepository;
    }

    /**
     * Use the cupom code in case it is valid
     *
     * @param  Request $request
     * @param  string $code
     * @return \Illuminate\Http\Response
     */
    public function useCoupon(Request $request, $code)
    {
        //

        if(!$code){
            return response()->json(['error' => 'Coupon code is empty'], 400);
        }
        
        try{
            
            $coupon = $this->couponRepository->getByCouponCode($code);
            return response()->json(['coupon' => $coupon], 200);
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 404);
        }

    }

    /**
     * Create a new cupom
     *
     * @param  Request $request  
     * @return \Illuminate\Http\Response
     */
    public function createCoupon(Request $request)
    {

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all() , [
            'code' => "required|string|min:4|max:10|regex:/^[a-zA-Z-0-9']*$/|unique:coupons",
            'value' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'expire_date' => "required|date",
        ]); 

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }

        try{
            
            $coupon = $this->couponRepository->create($request->all());
            return response()->json(['msg' => 'Coupon created successfully!', 'coupon' => $coupon], 201);
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 400);
        }


      

    }

    /**
     * Remove the cupom
     *
     * @param  Request $request
     *  @param  string $code
     * @return \Illuminate\Http\Response
     */
    public function removeCoupon(Request $request, $code)
    {

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try{
            
            $this->couponRepository->deleteByCouponCode($code);
            return response()->json(['msg' => 'Coupon '. $code .'deleted successfully!']);
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 404);
        }
        
    }
}
