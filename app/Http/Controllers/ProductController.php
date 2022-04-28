<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use App\Repositories\ProductRepository;


class ProductController extends Controller
{

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository){
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $response = [];

        $productsPerPage = 10;

        $category = $request->query('category');
        $search = $request->query('search') ?? '';

        $page = $request->query('page');

        if( !ctype_digit($page) || $page < 1 ){
            $page = 1;
        }    

        $response = $this->productRepository->getAll($category, $search, $page, $productsPerPage);
    
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

        if( !ctype_digit($category) || $category < 1 ){
            $category = 0;
        }    

        $products = $this->productRepository->getByCategory($category);

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

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $response = [];

        $validator = Validator::make( $request->all(),[
                'name' => 'required|string|min:3|max:200',
                'description' => 'required|string|max:500',
                //'image' => '',
                'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                'category' => 'required|numeric',
            ]);

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        try{
            $product = $this->productRepository->create($request->all());

            $response['msg'] = "Product created successfully";
            $response['product'] = $product;
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response($response, 201);
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * */
    public function updateProductImage(Request $request, $id){

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        $allowedTypes =  ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('image');

        if(!$image){
            return response()->json(['error' => 'No image was sent'] , 400);
        }

        if(in_array($image->getClientMimeType(), $allowedTypes ) ){

            $filename = md5(time().rand(0,9999)).'.jpg';
            $destPath = getProductsDirectoryPath(); //public_path('/uploads/products');
            
            Image::make($image)->save($destPath.'/'.$filename);

            try{

                $this->productRepository->updateImage($id, $filename);

            }catch(\Exception $e){
                return response()->json(['error' => $e->getMessage()], 400);
            }

            
            $imageUrl = createProductImageLink($filename);

            return response()->json(['msg' => "Image of Product $id was updated successfully!", 'url' => $imageUrl], 200);

        }else{
            return response()->json(['error' => 'File extension not supported'], 400);
        }

        

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

        try{

            $product = $this->productRepository->getById($id);

            return response()->json(['product' => $product],200);

        }catch(\Exception $e){
            $response['error'] = $e->getMessage(); 
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

        if(auth()->user()->admin !== 1){
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

        try{
            
            $product = $this->productRepository->updateById($id, $request->all());
            
            $response['product'] = $product;
            $response['msg'] = "The product '$id' was updated successfully";
        }catch(\Exception $e){
            $response['error'] = $e->getMessage();
            return response($response, 404);
        }

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

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        
        try{
            $this->productRepository->deleteById($id);
            return response()->json(['msg' => 'Product '. $id .'deleted successfully!']);
           
        }catch(\Exception $e){
            $response['error'] = $e->getMessage();
            return response($response,404);
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

        $product = $this->productRepository->searchByName($name);

       
        $response['product'] = $product;
        
        
        return response($response, 200);
    }



}
