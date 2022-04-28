<?php 

namespace App\Repositories;
use App\Models\City;
use App\Repositories\BaseRepository;

class CityRepository implements BaseRepository{


    public function getById(String $id){
        return City::find($id);
    }

   public function getAll(){
        return City::all();
   }

   public function create(array $data){

        $city = City::create([
            'name' => $data['name'] ?? null,
            'state' => $data['state'] ?? null,
            
        ]);

        return $city;

   }

   public function updateById(String $id, array $data){

        $city = City::find($id);
        
        if($city){
            if( isset($data['name']) && !empty( $data['name']) ){
            $city->name = $data['name']; 
            }
            if( isset($data['state']) && !empty( $data['state']) ){
                $city->state = $data['state']; 
            }
            $city->save();

            return $city;
        }else{

            throw new \Exception("City $id was not found");
        }

   }

   public function deleteById(String $id){

        $city = City::find($id);

        if($city){

            $city->delete();

            return $city;
        
        }else{

            throw new \Exception("City $id was not found"); 
        }
    
   }

   public function checkCityExists( $name, $state = null ){
        
        $cityExists = City::where('name', $name)->where('state', $state)->first();

        return $cityExists ? true : false;

}
}