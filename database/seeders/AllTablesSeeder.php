<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AllTablesSeeder extends Seeder
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
            'name' => 'teste',
            'email' => 'teste@gmail.com',
            'password' => Hash::make(666),
            //'username' => 'tester',
            'cpf' => 151646478247,
            'bornDate' => '1992-01-01',
            'areacode' => 11,
            'phone' => 991058256,
            'admin' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('categories')->insert(
            ['name' => 'Bebidas', 'slug' => 'bebidas', 'image' => 'drink.png'],
            ['name' => 'Hamburguer', 'slug' => 'hamburgueres', 'image' => 'burguer.png'],
            ['name' => 'Combos', 'slug' => 'combos', 'image' => 'combo.png'],
            ['name' => 'Massas', 'slug' => 'massas', 'image' => 'pizza.png'],
            ['name' => 'Doces', 'slug' => 'doces', 'image' => 'cake.png']
        );

        DB::table('products')->insert(
            ['name' => 'Donut de Chocolate', 'description' => 'Donut feito de chocolate e açucar', 'image' => 'donutchocolate.jpg', 'price' => '15.00', 'category_id' => '5'],
            ['name' => 'Donut de Flocos', 'description' => 'Donut com flocos e cobertura de morango', 'image' => 'donutflocos.jpg', 'price' => '15.00',  'category_id' => '5'],
            ['name' => 'Torta de Chocolate', 'description' => 'Uma torta de chocolate', 'image' => 'tortachocolate.jpg', 'price' => '12.00', 'category_id' => '5'],
            ['name' => 'Torta de Morango', 'description' => 'Uma torta de morango', 'image' => 'tortamorango.jpg', 'price' => '12.00',  'category_id' => '5'],
            ['name' => 'Combo X-tudo + Batata + refrigerante', 'description' => 'Hamburguer X-tudo com batata-frita e refrigerante 350ml', 'image' => 'combo1.jpg', 'price' => '25.00',  'category_id' => '3']
        );

        DB::table('payment_type')->insert([
            'id' => '1',
            'name' => 'Pagseguro',
        ]);

        DB::table('coupons')->insert(
            ['code' => 'ABC123456', 'type' => 'fixed' , 'value' => 4.00],
           // ['code' => 'ABC123321','type' => 'percent', 'value' => 15],
            ['code' => 'ACB123456', 'type' => 'fixed', 'value' => 3.15],
        );

        DB::table('cities')->insert(
            ['name' => 'São Paulo', 'state' => 'SP'],
            ['name' => 'Guarulhos', 'state' => 'SP'],
        );

        DB::table('districts')->insert(
            ['name' => 'Centro', 'city' => 1, 'available' => true, 'delivery_cost' => 3.00],
            ['name' => 'Pinheiros', 'city' => 1, 'available' => true, 'delivery_cost' => 5.65],
            ['name' => 'Centro', 'city' => 2, 'available' => true],
        );
    }
}
