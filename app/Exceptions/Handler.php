<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
        //RuntimeException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {

       $this->renderable(function (ValidationException $e, $request) {
            //
            return response()->json(['error' => $e->errors()] , 400);
            
        }); 

      /*  $this->renderable(function (\Exception $e, $request) {
            //
            return response()->json(['error' => 'Internal server error'] , 500);
            
        }); 
        */
        
    }
}
