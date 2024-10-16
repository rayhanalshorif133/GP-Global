<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\DatabaseSeeder;


class CreateProductsTable extends Migration
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
            $table->foreignId("service_id")->constrained("services")->onDelete("cascade")->onUpdate("cascade");
            $table->string('name');
            $table->string('product_key')->unique();           
            $table->string('description')->nullable();           
            $table->timestamps();
        });

        $dbSeeder = new DatabaseSeeder();
        $dbSeeder->run();
        
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
