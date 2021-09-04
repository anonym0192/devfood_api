<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    //

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

        $coupon = Coupon::where('code', $code)->where('expire_date' ,'>', date('Y-m-d'))->first();

        if(!$coupon){
            return response()->json(['error' => 'Coupon code is not valid'], 400);
        }

        return response()->json(['coupon' => $coupon], 200);

    }

    /**
     * Create a new cupom
     *
     * @param  Request $request  
     * @return \Illuminate\Http\Response
     */
    public function createCoupon(Request $request)
    {

        if(!auth()->user()->admin === 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all() , [
            'code' => "required|string|min:4|max:10|regex:/^[a-zA-Z-0-9']*$/|unique:coupons",
            'value' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'expireDate' => "required|date",
        ]); 

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }

        $coupon = Coupon::create([
                'code' => $request->input('code'),
                'value' => $request->input('value'),
                'expire_date' => $request->input('expireDate'),
            ]);

        $coupon->save();


        return response()->json(['msg' => 'Coupon created successfully!', 'coupon' => $coupon], 201);

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

        if(!auth()->user()->admin === 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $coupon = Coupon::where('code', $code)->get()->first();

        if($coupon){
            $coupon->delete();
            return response()->json(['msg' => 'Coupon '. $code .'deleted successfully!']);
        }else{
            return response()->json(['error' => 'Coupon '. $code .'does not exist!'], 404);
        }
        
    }
}
