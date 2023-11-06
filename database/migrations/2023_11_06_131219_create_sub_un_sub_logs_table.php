<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubUnSubLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_un_sub_logs', function (Blueprint $table) {
            $table->id();
            $table->string('msisdn')->nullable();
            $table->string('service')->nullable();
            $table->string('status')->nullable();
            $table->string('opt_date')->nullable();
            $table->string('opt_time')->nullable();
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
        Schema::dropIfExists('sub_un_sub_logs');
    }
}
