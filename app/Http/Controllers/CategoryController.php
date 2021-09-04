<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    //

    public function index(){

        $response = [];

        $categories = Category::all();

        
        foreach($categories as $cat){
            $cat['image'] = url('uploads/categories/'.$cat['image']);
        }

        $response['categories'] = $categories ?? [];

        return response()->json($response);
    }

        /**
     * Create a new cupom
     *
     * @param  Request $request  
     * @return \Illuminate\Http\Response
     */
    public function createCategory(Request $request)
    {

      /*  if(!auth()->user()->admin === 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all() , [
            
        ]); 

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }

        $category = category::create([
                
            ]);

        $category->save();


        return response()->json(['msg' => 'category created successfully!', 'category' => $category], 201);
            */
    }
}
