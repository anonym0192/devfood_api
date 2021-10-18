<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            'cpf' => '11111111111',
            'name' => 'teste',
            'email' => 'teste@gmail.com',
            'password' => Hash::make(666),
            //'username' => 'tester',
            'admin' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
