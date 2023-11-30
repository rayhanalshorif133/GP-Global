<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('keyword')->unique();
            $table->string('type')->enum('subscription', 'on-demand')->default('subscription');
            $table->string('validity')->enum('P1D', 'P7D', 'P30D')->default('P1D');
            $table->float('amount', 0, 8)->nullable();
            $table->string('api_url')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('description')->nullable();
            $table->string('reference_code')->nullable();
            $table->string('channel')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
