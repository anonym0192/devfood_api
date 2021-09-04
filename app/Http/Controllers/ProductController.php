<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $response = [];

        $products_per_page = 2;

        $category = $request->query('category');
        $search = $request->query('search') ?? '';

        $page = $request->query('page');

        if( !ctype_digit($page) || $page < 1 ){
            $page = 1;
        }    

        $offset = ($page - 1) * $products_per_page;
        

        $products = Product::select();

        if($search ){
            $products->where('name', 'LIKE', '%'.$search.'%');
        }

        if($category && $category > 0){
            $products->where('category_id', $category);
        }

        $products_total =  $products->count();
    
        $products = $products->offset($offset)->limit($products_per_page)->get();

        $pages = ceil( $products_total / $products_per_page );

        foreach($products as $prod){

            $prod['image'] = url('uploads/products/'.$prod['image']);
            
            $cat = Category::find($prod['category_id']);
            unset($prod['category_id']);

            if($cat){
                $prod['category'] = $cat;
            }
        }

        $response['products'] = $products;
        $response['total'] = $products_total;
        $response['current_page'] = $page < 1 ? 1 : intval($page);
        $response['total_pages'] = $pages;
        
    
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getByCategory($category)
    {
        //
        $response = [];

        if($category == 0){
            $products = Product::all();
        }else{
            $products = Product::where('category_id', $category)->get();
        }

        foreach($products as $prod){

            $prod['image'] = url('uploads/products/'.$prod['image']);
            
            $cat = Category::find($prod['category_id']);
            unset($prod['category_id']);

            if($cat){
                $prod['category'] = $cat;
            }
        }

        $response['products'] = $products;
    
        return response()->json($response);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        if(!auth()->user()->admin === 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $response = [];

        $validator = Validator::make( $request->all(),[
                'name' => 'required|string|min:3|max:200',
                'description' => 'required|string|max:500',
                'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                'category' => 'required|numeric',
            ]);

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        $product = new Product;
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category');

        $product->save();
        
        $response['msg'] = "Product created successfully";
        $response['product'] = $product;

        return response($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = 0)
    {
        //
        $response = [];

        $product = Product::find($id);

        if($product){

            $cat = Category::find($product['category_id']);
            unset($product['category_id']);
            
            if($cat){
                $product['category'] = $cat;
            }
            
            $response['product'] = $product;
            return response($response, 200);
        }else{
            $response['error'] = "The item was not found"; 
            return response($response, 404);
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

        if(!auth()->user()->admin === 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $response = [];

        $validator = Validator::make( $request->all(),[
            'name' => 'string|min:3|max:200',
            'description' => 'string|max:500',
            'price' => 'regex:/^\d+(\.\d{1,2})?$/',
            'category' => 'numeric',
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        $product = Product::find($id);

        
        if($product){
            
            if($request->input('name')){
                $product->name = $request->input('name');
            }
            if($request->input('description')){
                $product->description = $request->input('description');
            }
            if($request->input('price')){
                $product->price = $request->input('price');
            }
            if($request->input('category')){
                $product->category_id = $request->input('category');
            }

            try{
                $product->save();
                
                $response['product'] = $product;
                $response['msg'] = "The product '$id' was updated successfully";
            }catch(Exception $e){
                $response['error'] = $e->getMessage();
                return response($response,500);
            }
        }else{
            $response['error'] = "The product '$id' was not found";
            return response($response,404);
        }
        
        return response($response,200);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = 0)
    {
        //  

        if(!auth()->user()->admin === 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $product = Product::find($id);

        if($product){
            $product->delete();
            
            return response()->json(['msg' => 'Product '. $id .'deleted successfully!']);
        }else{
            return response()->json(['error' => 'Product '. $id .'does not exist!'], 404);
        }
        
    }

     /**
     * Search for a product item by name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request, $name)
    {
        //

        $response = [];

        $product = Product::where('name', 'LIKE', '%'.$name.'%')->get()->all();

        if($product){
            $response['product'] = $product;
        }
        
        return response($response, 200);
    }



}
