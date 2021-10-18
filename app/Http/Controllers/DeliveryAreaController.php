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

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all() , [
            'name' => "required|string|min:4|max:80|regex:/^[a-zA-Z-0-9'\s]*$/",
            'state' => "required|string|min:2|max:2|regex:/^[a-zA-Z]*$/",
        ]); 

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }

        $cityExists = City::where('name', $request->input('name'))->where('state', $request->input('state'))->first();
        if($cityExists){
            return response()->json(['error' => 'City already exists'], 400);
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

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all() , [
            'name' => "required|string|min:4|max:80|regex:/^[a-zA-Z-0-9'\s]*$/",
            'city' => "required|numeric",
            'delivery_cost' => "nullable|regex:/^\d+(\.\d{1,2})?$/",
        ]); 

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }

        $districtExists = District::where('name', $request->input('name'))->where('city', $request->input('city'))->first();
        if($districtExists){
            return response()->json(['error' => 'District already exists'], 400);
        }   

        $district = District::create([
                'name' => $request->input('name'),
                'city' => $request->input('city'),
                'delivery_cost' => $request->input('delivery_cost')
            ]);

        $district->save();


        return response()->json(['msg' => 'District created successfully!', 'district' => $district], 201);


    }

    /**
     * Remove a city
     *
     * @param  Request $request 
     * @param  int $id  
     * @return \Illuminate\Http\Response
     */

    public function removeCity(Request $request, $id)
    {


        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $city = City::find($id);
;
        if($city){

            $city->delete();

            $districts = District::where('city', $id)->delete();

            return response()->json(['msg' => 'City '. $id .' deleted successfully!']);
        }else{
            return response()->json(['error' => 'City '. $id .' does not exist!'], 404);
        }
    }

     /**
     * Remove a District
     *
     * @param  Request $request  
     * @param  int $id 
     * @return \Illuminate\Http\Response
     */

    public function removeDistrict(Request $request, $id)
    {

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $district = District::find($id);
;
        if($district){
            $district->delete();
            return response()->json(['msg' => 'District '. $id .' deleted successfully!']);
        }else{
            return response()->json(['error' => 'District '. $id .' does not exist!'], 404);
        }

    }

    /**
     * Disable a district to make it unavailable
     *
     * @param  Request $request  
     * @param  int $id 
     * @return \Illuminate\Http\Response
     */

    public function disableDistrict(Request $request, $id)
    {

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $district = District::find($id);
;
        if($district){
            $district->available = false;
            $district->save();
            return response()->json(['msg' => 'District '. $id .' was made unavailable successfully!']);
        }else{
            return response()->json(['error' => 'District '. $id .' does not exist!'], 404);
        }

    }

        /**
     * Disable a district to make it unavailable
     *
     * @param  Request $request  
     * @param  int $id 
     * @return \Illuminate\Http\Response
     */

    public function enableDistrict(Request $request, $id)
    {

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $district = District::find($id);
;
        if($district){
            $district->available = true;
            $district->save();
            return response()->json(['msg' => 'District '. $id .' was available successfully!']);
        }else{
            return response()->json(['error' => 'District '. $id .' does not exist!'], 404);
        }

    }

}
