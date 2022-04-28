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
            'cpf' => 15164630129,
            'born_date' => '1992-01-01',
            'area_code' => 11,
            'phone' => 991058256,
            'admin' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('categories')->insert([
            ['name' => 'Bebidas', 'slug' => 'bebidas', 'image' => 'drink.png'],
            ['name' => 'Hamburgueres', 'slug' => 'hamburgueres', 'image' => 'burguer.png'],
            ['name' => 'Combos', 'slug' => 'combos', 'image' => 'combo.png'],
            ['name' => 'Massas', 'slug' => 'massas', 'image' => 'pizza.png'],
            ['name' => 'Doces', 'slug' => 'doces', 'image' => 'cake.png']
        ]);

        DB::table('products')->insert([
            ['name' => 'Coca-cola 350ml', 'description' => 'Latinha de coca-cola 350ml', 'image' => 'cola350ml.jpg', 'price' => '6.00',  'category_id' => '1'],
            ['name' => 'Guaraná 350ml', 'description' => 'Latinha de guaraná 350ml', 'image' => 'guarana350ml.jpeg', 'price' => '6.00',  'category_id' => '1'],
            ['name' => 'Batata frita', 'description' => 'Pacote de batata média', 'image' => 'batata-frita.jpeg', 'price' => '8.00',  'category_id' => '4'],
            ['name' => 'Donut de Chocolate', 'description' => 'Donut feito de chocolate e açucar', 'image' => 'donutchocolate.jpg', 'price' => '15.00', 'category_id' => '5'],
            ['name' => 'Donut de Flocos', 'description' => 'Donut com flocos e cobertura de morango', 'image' => 'donutflocos.jpg', 'price' => '15.00',  'category_id' => '5'],
            ['name' => 'Torta de Chocolate', 'description' => 'Uma torta de chocolate', 'image' => 'tortachocolate.jpg', 'price' => '12.00', 'category_id' => '5'],
            ['name' => 'Torta de Morango', 'description' => 'Uma torta de morango', 'image' => 'tortamorango.jpg', 'price' => '12.00',  'category_id' => '5'],
            ['name' => 'Combo X-tudo + Batata + refrigerante', 'description' => 'Hamburguer X-tudo com batata-frita e refrigerante 350ml', 'image' => 'combo1.jpg', 'price' => '25.00',  'category_id' => '3']
        ]);

        DB::table('payment_types')->insert([
            'id' => '1',
            'name' => 'Pagseguro',
        ]);

        DB::table('coupons')->insert([
            ['code' => 'ABC123456', 'type' => 'fixed' , 'value' => 4.00 , 'expire_date' => date('Y-m-d', strtotime('+3 month'))],
           // ['code' => 'ABC123321','type' => 'percent', 'value' => 15, 'expire_date' => date('Y-m-d', strtotime('+3 month'))],
            ['code' => 'ACB123456', 'type' => 'fixed', 'value' => 3.15, 'expire_date' => date('Y-m-d', strtotime('+3 month'))],
        ]);

        DB::table('cities')->insert([
            ['name' => 'São Paulo', 'state' => 'SP'],
            ['name' => 'Guarulhos', 'state' => 'SP'],
            ['name' => 'Rio de Janeiro', 'state' => 'RJ'],
        ]);

        DB::table('districts')->insert([
            ['name' => 'Centro', 'city' => 1, 'available' => true, 'delivery_cost' => 3.00],
            ['name' => 'Pinheiros', 'city' => 1, 'available' => true, 'delivery_cost' => 5.65],
            ['name' => 'Centro', 'city' => 2, 'available' => true, 'delivery_cost' => 4],
            ['name' => 'Centro', 'city' => 3, 'available' => true, 'delivery_cost' => 3.50],
        ]);

        DB::table('orders')->insert([
            ['total' => 50, 'user_id' => 1, 'street' => 'Rua inexistente', 'number' => 101, 'postal_code' => 11111111, 'city' => 'São Paulo', 'state' => 'SP', 'delivery_cost' => 3],
            ['total' => 125, 'user_id' => 1, 'street' => 'Rua inexistente', 'number' => 120, 'postal_code' => 22222222, 'city' => 'São Paulo', 'state' => 'SP', 'delivery_cost' => 3],
            ['total' => 25, 'user_id' => 1, 'street' => 'Rua inexistente', 'number' => 10, 'postal_code' => 22222222, 'city' => 'São Paulo', 'state' => 'SP', 'delivery_cost' => 3]
        ]);

        DB::table('order_items')->insert([
            ['qt' => 2, 'product_id' => 8, 'order_id' => 1],
            ['qt' => 5, 'product_id' => 8, 'order_id' => 2],
            ['qt' => 1, 'product_id' => 8, 'order_id' => 3],
        ]);
    }
}
