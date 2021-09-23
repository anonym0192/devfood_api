<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * Register a new User and generate a token
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all() , [
                        'name' => "required|string|min:2|max:100|regex:/^[a-zA-Z-' ]*$/",
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
        
        $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'cpf' => $request->input('cpf'),
                    'born_date' => $request->input('born_date'),
                    'phone' => $request->input('phone'),
                    //'cellphone' => $request->input('cellphone'),
                    'area_code' => $request->input('area_code'),
                    'password' => bcrypt($request->input('password')),
                    //'username' => $request->input('username')
                ]);

        $token = $user->createToken('email')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
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
            'name' => "string|min:2|max:100|regex:/^[a-zA-Z-' ]*$/",
            'email' => "email|min:5|max:80",
            'cpf' => "nullable|require|numeric|min:11",
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

        $email = $request->input('email');
        

        $user = User::find($id);

        if( $email ){
            $emailExists = User::where('email', $email )->count();
                     
            if( ( $emailExists > 0 ) && ( $email != auth()->user()['email'] ) ){
                return response()->json( ['error' => 'The email has already been taken.'] );
            }
        }
        
        /*if(   $request->input('username') ){
            $usernameExists = User::where('username',  $username )->count();
            if( ( $usernameExists > 0 ) && ( $username != auth()->user()['username'] ) ){
                return response()->json( ['error' => 'The username has already been taken.'] );
            }
        } */
 
       
        if($user){
            if($request->input('name')){
                $user->name = $request->input('name');
            }
            if($request->input('email')){
                $user->email = $request->input('email');
            }
            
            if($request->input('cpf')){
                $user->cpf = $request->input('cpf');
            }

            if($request->input('born_date')){
                $user->born_date = $request->input('born_date');
            }

            if($request->input('area_code')){
                $user->area_code = $request->input('area_code');
            }

            if($request->input('phone')){
                $user->phone = $request->input('phone');
            }

            
            if($request->input('password')){
                $user->password = bcrypt($request->input('password'));
            }
            
            
            $user->save();

            $response['user'] = $user;
            $response['msg'] = "User $id was updated successfully!";
            return response($response, 200);
        }else{
            $response['error'] = "User $id does not exist";
            return response($response, 404);
        }


    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*
    public function destroy($id)
    {
        //
        $user = User::find($id);
        if($user){
            $user->delete();
            $response['msg'] = "User $id deleted successfully!";
            return response($response, 200);
        }else{
            $response['error'] = "User $id does not exist";
            return response($response, 404);
        }
    } */
}
