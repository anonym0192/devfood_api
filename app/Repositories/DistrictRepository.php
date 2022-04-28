<?php 

namespace App\Repositories;
use App\Models\District;

class DistrictRepository implements BaseRepository{


    public function getById(String $id){
        return District::find($id);
    }

   public function getAll(){
        return District::all();
   }

   public function create(array $data){

    
        $district = District::create([
            'name' => $data['name'] ?? null,
            'city' => $data['city'] ?? null,
            'available' => $data['available'] ?? true,
            'delivery_cost' => $data['delivery_cost'] ?? 0,  
        ]);
        
        return $district;

   }

   public function updateById(String $id, array $data){

    $district = District::find($id);
       
    if($district){
        if( isset($data['name']) && !empty( $data['name']) ){
           $district->name = $data['name']; 
        }

        if( isset($data['city']) && !empty( $data['city']) ){
            $district->city = $data['city']; 
         }
        if( isset($data['delivery_cost']) && !empty( $data['delivery_cost']) ){
            $district->delivery_cost = $data['delivery_cost']; 
         }
        $district->save();

        return $district;
    }else{

        throw new \Exception("District $id was not found");
    }

   }

   public function deleteById(String $id){

    $district = District::find($id);

    if($district){

        $district->delete();

        return $district;
    
    }else{

        throw new \Exception("District $id was not found"); 
    }
    
   }

   public function enableDistrict(String $id){

        $district = District::find($id);
        
        if($district){
            
            $district->available = true;
            $district->save();

            return $district;
        }else{

            throw new \Exception("District $id was not found");
        }
        
   }

   public function disableDistrict(String $id){

        $district = District::find($id);
        
        if($district){
            
            $district->available = false;
            $district->save();

            return $district;
        }else{

        throw new \Exception("District $id was not found");
    }
    
   }

   public function getAvailableDistrictsByCity(String $city){

        return District::where('city', $city)->where('available', 1)->get();
   }

   public function checkDistrictExists( $name, $city = null ){
        
        $districtExists = District::where('name', $name)->where('city', $city)->first();

        return $districtExists ? true : false;

   }

}