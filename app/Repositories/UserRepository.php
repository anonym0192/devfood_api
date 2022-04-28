<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\User;

class UserRepository implements BaseRepository{

    public function getById(String $id){

        return User::find($id);
    }

   public function getAll(){

        return User::all();
   }

   public function create(array $data){

    $user = User::create([
        'name' => $data['name'] ?? '',
        'email' => $data['email'] ?? '',
        'cpf' => $data['cpf'] ?? null,
        'born_date' => $data['born_date'] ?? null,
        'phone' => $data['phone'] ?? null,
        'area_code' => $data['area_code'] ?? null,
        'password' => bcrypt($data['password']),
        
    ]);

    return $user;

   }

   public function updateById(String $id, array $data){

       
       $user = User::find($id);
       
        if($user){
            if( isset($data['name']) && !empty( $data['name']) ){
               $user->name = $data['name']; 
            }
            if( isset($data['email']) && !empty( $data['email']) ){
                $user->email = $data['email'];
            }
            
            if( isset($data['cpf']) && !empty( $data['cpf']) ){
                $user->cpf = $data['cpf'];
            }

            if( isset($data['born_date']) && !empty( $data['born_date']) ){
                $user->born_date = $data['born_date'];
            }

            if( isset($data['area_code']) && !empty( $data['area_code']) ){
                $user->area_code = $data['area_code'];
            }

            if( isset($data['phone']) && !empty( $data['phone']) ){
                $user->phone = $data['phone'];
            }

            
            if( isset($data['password']) && !empty( $data['password']) ){
                $user->password = bcrypt($data['password']);
            }
            
            
            $user->save();

            return $user;
        }else{

            throw new \Exception("User $id was not found");
        }


   }

   public function deleteById(String $id){

    $user = User::find($id);

    if($user){

        $user->delete();

        return $user;
    
    }else{

        throw new \Exception("User $id was not found"); 
    }
       
   }

   public function getUserByEmail(String $email){
        return User::where('email', $email )->get();
   }

   public function isEmailAlreadyTaken(String $email){
    
        $emailsFound = User::where('email', $email )->count();

        if($emailsFound > 0) 
            return true;
        else
            return false;
    }

} 