<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new User and generate a token
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all() , [
                        'name' => "required|string|min:2|max:100|regex:/^[a-zA-Z-'. ]*$/",
                        'email' => "required|min:5|max:80|email|unique:users,email",
                        'cpf' => "required|numeric|min:11",
                        'born_date' => "required|date",
                        'area_code' => 'nullable|digits:2',
                        'phone' => "nullable|string|min:8|max:20",
                        //'cellphone' => "nullable|string|min:8|max:20",
                        'password' => "required|string|confirmed|min:3|max:13",
                       // 'username' => "required|string|unique:users,username|min:2|max:50|regex:/^[a-zA-Z-'0-9 ]*$/" 
                    ]); 

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        try{

            $user = $this->userRepository->create($request->all());

            $token = $user->createToken('email')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);

        }catch(\Exception $e){
            return response(['error' => $e->getMessage()], 400); 
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $validator = Validator::make($request->all() , [
            'name' => "string|min:2|max:100|regex:/^[a-zA-Z-'. ]*$/",
            'email' => "email|min:5|max:80",
            'cpf' => "nullable|numeric|min:11",
            'born_date' => "nullable|date",
            'area_code' => 'nullable|digits:2',
            'phone' => "nullable|string|min:8|max:20",
            //'cellphone' => "nullable|string|min:8|max:20",
            'password' => "nullable|confirmed|min:3|max:13",
            //'username' => "nullable|string|min:2|max:50|regex:/^[a-zA-Z-'0-9 ]*$/" 
        ]);

        if( $validator->fails() ){
            return response()->json(['error' => $validator->errors()], 400);
        }

        if($id != Auth::user()['id']){
            return response()->json(['error' => 'Unauthorized'], 401);
        } 

        $email = $request->input('email');

        if( $email ){

            $emailExists = $this->userRepository->isEmailAlreadyTaken($email);
                     
            if( ( $emailExists ) && ( $email != Auth::user()['email'] ) ){
                return response()->json( ['error' => 'The email has already been taken.'] );
            }
        }

        try{

            $user = $this->userRepository->updateById($id, $request->all());

            $response['user'] = $user;
            $response['msg'] = "User $id was updated successfully!";
            return response()->json($response, 200);


        }catch(\Exception $e){
            return response(['error' => $e->getMessage()], 400); 
        }



    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function destroy($id)
    {
        if(Auth::user()->admin != 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
    
        try{

            $user = $this->userRepository->deleteById($id);

            $response['user'] = $user;
            $response['msg'] = "User $id was deleted successfully!";
            return response()->json($response, 200);


        }catch(\Exception $e){
            return response(['error' => $e->getMessage()], 400); 
        }
    } 
}
