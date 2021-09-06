<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\District;

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

    }


    /**
     * Create a new district
     *
     * @param  Request $request  
     * @return \Illuminate\Http\Response
     */
    public function addDistrict(Request $request)
    {

    }
}
