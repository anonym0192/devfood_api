<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Repositories\DistrictRepository;

class DistrictController extends Controller
{

    private DistrictRepository $districtRepository;

    public function __construct(DistrictRepository $districtRepository){
        $this->districtRepository = $districtRepository;
    }

    //

    /**
     * Get all available districts
     *
     * @param  Request $request  
     * @return \Illuminate\Http\Response
     */
    public function getDistrictsFromCity(Request $request ){

        $city = $request->query('city') ?? '';
        
        try{
            
            $districts = $this->districtRepository->getAvailableDistrictsByCity($city);
            return response()->json(['districts' => $districts]);
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }


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

        $districtExists = $this->districtRepository->checkDistrictExists($request->input('name'), $request->input('city') );
        if($districtExists){
            return response()->json(['error' => 'District already exists'], 400);
        } 

        try{
            
            $district = $this->districtRepository->create($request->all());
            return response()->json(['msg' => 'District created successfully!', 'district' => $district], 201);
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
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

        try{
            
            $this->districtRepository->deleteById($id);
            return response()->json(['msg' => 'District '. $id .' deleted successfully!']);
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 404);
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


        try{
            
            $this->districtRepository->disableDistrict($id);
            return response()->json(['msg' => 'District '. $id .' was made unavailable successfully!']);
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 404);
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

        try{
            
            $this->districtRepository->enableDistrict($id);
            return response()->json(['msg' => 'District '. $id .' was made available successfully!']);
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 404);
        }

    }
}
