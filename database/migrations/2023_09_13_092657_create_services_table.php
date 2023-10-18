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
            $table->string('validity')->enum('daily', 'weekly', 'monthly')->default('daily');
            $table->float('charge', 0, 8)->nullable();
            $table->string('redirect_url')->nullable();
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
