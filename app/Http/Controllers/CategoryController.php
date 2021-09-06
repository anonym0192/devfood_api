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

    }
}
