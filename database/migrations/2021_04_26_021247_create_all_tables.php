<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->text('description', 500);
            $table->string('image', 200)->nullable();
            $table->decimal('price', 5, 2);
            $table->foreignId('category_id');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 20)->unique();
            $table->string('image', 100)->nullable();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('email', 50)->unique();
           // $table->string('username', 50)->unique();
            $table->string('password', 50);
            $table->integer('phone', 20 )->nullable();
            $table->integer('areacode', 2 )->nullable();
            $table->integer('cellphone', 20 )->nullable();
            $table->integer('cpf', 20 );
            $table->date('borndate')->nullable();
            $table->boolean('admin')->default(false);
            $table->rememberToken();
            $table->timestamps();
        
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 5,2);
            $table->foreignId('user_id');
            $table->foreignId('cupom')->nullable();
            $table->string('street',100);
            $table->string('number',80);
            $table->string('complement',80)->nullable();
            $table->string('postal_code',20);
            $table->string('city',80);
            $table->char('state',2);
            $table->integer('status',1)->default(0);
            $table->decimal('delivery_cost', 5,2);
            $table->timestamps();
            $table->foreignId('transactions');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('qt');
            $table->foreignId('product_id');
            $table->foreignId('order_id');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->smallInteger('mode')->default(0);
            $table->smallInteger('type')->default(1);
            $table->string('status,20')->default('pending');
            $table->foreignId('user_id');
            $table->foreignId('order_id')->unique();
            $table->timestamps();
        });

        Schema::create('payment_types', function (Blueprint $table){
            $table->id();
            $table->string('name');
        });

        Schema::create('coupons', function (Blueprint $table){
            $table->id();
            $table->string('code',100)->unique();
            $table->string('type',100)->nullable();
            $table->decimal('value', 5,2)->nullable();
            //$table->decimal('percent_off', 5,2)->nullable();
            $table->date('expire_date');
        });

        Schema::create('cities', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->char('state', 2);
        });

        Schema::create('districts', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->foreignId('city');
            $table->boolean('available');
        });
        


        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('all_tables');
    }
}
