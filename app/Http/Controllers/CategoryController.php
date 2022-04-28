<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class CategoryController extends Controller
{
    //

    public CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository){
        $this->categoryRepository = $categoryRepository;
    }

    public function index(){

        $response = [];

        $categories = $this->categoryRepository->getAll();


        $response['categories'] = $categories ?? [];

        return response()->json($response);
    }

        /**
     * Create a new cupom
     *
     * @param  Request $request  
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if(auth()->user()->admin !== 1){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $response = [];

        $validator = Validator::make( $request->all(),[
                'name' => 'required|string|min:3|max:200',
                'slug' => 'required|unique:categories,slug|string|max:50',
                //'image' => '',
            ]);

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        try{
            $category = $this->categoryRepository->create($request->all());

            $response['msg'] = "Category created successfully";
            $response['category'] = $category;
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response($response, 201);
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
            $this->categoryRepository->deleteById($id);
            return response()->json(['msg' => 'Category '. $id .' deleted successfully!']);
           
        }catch(\Exception $e){
            $response['error'] = $e->getMessage();
            return response($response,404);
        }
        
    }

    /*
    * Display the specified resource.
    * @param Request $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    * */
   public function updateCategoryImage(Request $request, $id){

       if(auth()->user()->admin !== 1){
           return response()->json(['error' => 'Unauthorized'], 401);
       } 


       $allowedTypes =  ['image/jpg', 'image/jpeg', 'image/png'];

       $image = $request->file('image');

       if(!$image){
           return response()->json(['error' => 'No image was sent'] , 400);
       }
       
      
       if(in_array($image->getClientMimeType(), $allowedTypes ) ){

           
           try{

                $newImageFileName = md5(time().rand(0,9999)).'.jpg';
                $destPath = getCategoriesDirectoryPath(); 
            
                Image::make($image)->save($destPath.'/'.$newImageFileName);
    
    
                $this->categoryRepository->updateImage($id, $newImageFileName);
               

           }catch(\Exception $e){
               return response()->json(['error' => $e->getMessage()], 400);
           }

           
           $imageUrl = createCategoryImageLink($newImageFileName);

           return response()->json(['msg' => "Image of Category $id was updated successfully!", 'url' => $imageUrl], 200);

       }else{
           return response()->json(['error' => 'File extension not supported'], 400);
       }

    }


}
