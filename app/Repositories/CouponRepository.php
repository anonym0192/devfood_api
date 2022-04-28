<?php 

namespace App\Repositories;
use App\Models\Coupon;

class CouponRepository implements BaseRepository{

    public function getById(String $id){
        return Coupon::find($id);
    }

   public function getAll(){
        return Coupon::all();
   }

   public function create(array $data){

    $coupon = Coupon::create([
        'code' => $data['code'] ?? '',
        'type' => 'fixed',
        'value' => $data['value'] ?? 0,
        'expire_date' => $data['expire_date'] ?? null
         
    ]);

    return $coupon;

   }

   public function updateById(String $id, array $data){

    $coupon = Coupon::find($id);
       
    if($coupon){
        if( isset($data['code']) && !empty( $data['code']) ){
           $coupon->code = $data['code']; 
        }
        if( isset($data['value']) && !empty( $data['value']) ){
            $coupon->value = $data['value']; 
         }

         if( isset($data['expire_date']) && !empty( $data['expire_date']) ){
            $coupon->expire_date = $data['expire_date']; 
         }
        $coupon->save();

        return $coupon;
    }else{

        throw new \Exception("Coupon $id was not found");
    }

   }

   public function deleteById(String $id){

    $coupon = Coupon::find($id);

    if($coupon){

        $coupon->delete();

        return $coupon;
    
    }else{

        throw new \Exception("Coupon $id was not found"); 
    }
    
   }

   public function getByCouponCode(String $code){
       $coupon =  Coupon::where('code', $code)->where('expire_date' ,'>', date('Y-m-d'))->first();

       if($coupon){
            return $coupon;
       }else{
            throw new \Exception("Coupon do not exist or is expired");
       }
   }

   public function deleteByCouponCode(String $code){

    $coupon = Coupon::where('code', $code)->get()->first();

    if($coupon){
        $coupon->delete();
    }else{
        throw new \Exception('Coupon '. $code .'does not exist!');
    }
}


   
   


}