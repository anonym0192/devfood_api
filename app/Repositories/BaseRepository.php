<?php 

namespace App\Repositories;

interface BaseRepository{


   public function getById(String $id);

   public function getAll();

   public function create(array $data);

   public function updateById(String $id, array $data);

   public function deleteById(String $id);

}