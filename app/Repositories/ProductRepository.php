<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\Product;
use App\Models\Category;

class ProductRepository implements BaseRepository{

    public function getById(String $id){

        $product = Product::find($id);

        if($product){

            $cat = Category::find($product['category_id']);
            unset($product['category_id']);
            
            if($cat){
                $product['category'] = $cat;
            }
            
            return $product;
        }else{
            throw new \Exception('The item was not found');
        }

    }

   public function getAll(int $category = null, String $search = null, int $page = 1 , int $productsPerPage = 200 ){

        $offset = ($page - 1) * $productsPerPage;

        $products = Product::select();

        if($search ){
            $products->where('name', 'LIKE', '%'.$search.'%');
        }

        if($category && $category > 0){
            $products->where('category_id', $category);
        }

        $productsTotal =  $products->count();

        $products = $products->offset($offset)->limit($productsPerPage)->get();

        $pages = ceil( $productsTotal / $productsPerPage );

        foreach($products as $prod){

            $prod['image'] = createProductImageLink($prod['image']);
            
            $cat = Category::find($prod['category_id']);
            unset($prod['category_id']);

            if($cat){
                $prod['category'] = $cat;
            }
        }

        $data['products'] = $products;
        $data['total'] = $productsTotal;
        $data['current_page'] = $page < 1 ? 1 : intval($page);
        $data['total_pages'] = $pages;

        return $data;

   }

   public function create(array $data){

       
        $product = new Product;
        $product->name = $data['name'] ?? '';
        $product->description = $data['description'] ?? '';
        $product->price = $data['price'] ?? '' ;
        $product->category_id = $data['category'] ?? '';

        $product->save();
        
    
        return $product;
   }

   public function updateById(String $id, array $data){

    $product = Product::find($id);
        
    if($product){
        
        if( isset($data['name']) && !empty($data['name']) ){
            $product->name = $data['name'];
        }
        if( isset($data['description']) && !empty($data['description']) ){
            $product->description = $data['description'];
        }
        if( isset($data['price']) && !empty($data['price']) ){
            $product->price = $data['price'];
        }
        if( isset($data['category']) && !empty($data['category']) ){
            $product->category_id = $data['category'];
        }

        
        $product->save();
        
        return $product;
        
    }else{
        throw new \Exception("The product '$id' was not found");
    }

   }

   public function deleteById(String $id){
        $product = Product::find($id);

        if($product){
            return $product->delete();
        }else{
            throw new \Exception("The item $id was not found");
        }
    }


   public function getByCategory(int $category){
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

        return $products;
   }

   public function searchByName(String $name){

    $product = Product::where('name', 'LIKE', '%'.$name.'%')->get()->all();

    return $product;

   }

   public function updateImage($id, $imageName){
    
        
    $product = Product::find($id);
    $product->image = $imageName;
    $product->save();

    if(!$product){
        throw new \Exception("Product $id does not exist");
        
    }
   }

} 