<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CityRepository;

class CityController extends Controller
{


    private CityRepository $cityRepository;

    public function __construct(CityRepository $cityRepository){
        $this->cityRepository = $cityRepository;
    }


    //

     /**
     * Get all available cities 
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function getCities(){

        $cities = $this->cityRepository->getAll();

        return response()->json(['cities' => $cities]);

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

        $cityExists = $this->cityRepository->checkCityExists( $request->input('name'), $request->input('state' ) );
        if($cityExists){
            return response()->json(['error' => 'City already exists'], 400);
        }   

        try{
            
            $city = $this->cityRepository->create($request->all());
            return response()->json(['msg' => 'City created successfully!', 'city' => $city], 201);
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }

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

        try{
            
            $this->cityRepository->deleteById($id);
            return response()->json(['msg' => 'City '. $id .' deleted successfully!']);
            
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 404);
        }

    }


 

   
}
