<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        //$this->loggedUser = Auth::user();
    }

    /**
     * Get a JWT via given credentials.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            
            return response()->json(['error' => 'The provided credentials are incorrect.'], 401 );
        }
    
        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json( [ 'token' => $token , 'user' => $user ] );
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json(['msg' => 'Successfully logged out']);
    }

    
    /**
     * Refresh Token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        Auth::user()->tokens()->delete();

        $token = Auth::user()->createToken(Auth::user()->email)->plainTextToken;

        return response()->json([ 'token' => $token ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json( Auth::user());
    }




    public function unauthorized(){
        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
