<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\District;
use Illuminate\Support\Facades\Validator;

class DeliveryAreaController extends Controller
{
    //

    /**
     * Get all available cities 
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function getCities(){

        $cities = City::all();

        return response()->json(['cities' => $cities]);

    }

    /**
     * Get all available districts
     *
     * @param  Request $request  
     * @return \Illuminate\Http\Response
     */
    public function getDistricts(Request $request ){

        $city = $request->query('city') ?? '';
        
        $districts = District::where('city', $city)->where('available', 1)->get();

        return response()->json(['districts' => $districts]);

    }

    /**
     * Create a new city
     *
     * @param  Request $request  
     * @return \Illuminate\Http\Response
     */

    public function addCity(Request $request)
    {

        if(!auth()->user()->admin === 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all() , [
            'name' => "required|string|min:4|max:10|regex:/^[a-zA-Z-0-9']*$/",
            'state' => "required|string|min:2|max:2|regex:/^[a-zA-Z]*$/",
        ]); 

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }

        $city = City::create([
                'name' => $request->input('name'),
                'state' => $request->input('state')
            ]);

        $city->save();


        return response()->json(['msg' => 'City created successfully!', 'city' => $city], 201);

    }


    /**
     * Create a new district
     *
     * @param  Request $request  
     * @return \Illuminate\Http\Response
     */
    public function addDistrict(Request $request)
    {

        if(!auth()->user()->admin === 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all() , [
            'name' => "required|string|min:4|max:10|regex:/^[a-zA-Z-0-9']*$/",
            'city' => "required|numeric",
            'delivery_cost' => "nullable|regex:/^\d+(\.\d{1,2})?$/",
        ]); 

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }

        $district = District::create([
                'name' => $request->input('name'),
                'city' => $request->input('city'),
                'delivery_cost' => $request->input('delivery_cost')
            ]);

        $district->save();


        return response()->json(['msg' => 'District created successfully!', 'district' => $district], 201);


    }

}
