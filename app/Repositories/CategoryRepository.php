<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository{

    public function getAll(){
        
        $categories = Category::all();
        
        foreach($categories as $cat){

            if($cat['image']){
               $cat['image'] = createCategoryImageLink($cat['image']);
            }
            
        }

        return $categories;
    }

    public function getById($id){

        $category = Category::find($id);

        if($category['image']){
            $category['image'] = createCategoryImageLink($category['image']);
        }
        
        return $category;
    }

    public function deleteById($id){

        $category = Category::find($id);

        if($category){
            return $category->delete();
        }else{
            throw new \Exception("The item $id was not found");
        }

    }

    public function create(array $data){
        $category = new Category;
        $category->name = $data['name'] ?? '';
        $category->slug = $data['slug'] ?? '';
        $category->image = $data['image'] ?? '' ;
        

        $category->save();

        return $category;
    }

    public function updateImage($id, $imageName){
    
        
        $category = Category::find($id);

        if(!$category){
            throw new \Exception("Category $id does not exist");   
        }

        if($category->image){
            removeOldImage(getCategoriesDirectoryPath().'/'.$category->image);
        }

        $category->image = $imageName;
        $category->save();
    
        
       }

}