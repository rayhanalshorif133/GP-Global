<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('msisdn')->nullable();
            $table->string('rmsisdn')->nullable();
            $table->string('tid')->nullable();
            $table->string('opr')->nullable();
            $table->string('channel')->nullable();
            $table->string('status')->nullable();
            $table->string('service')->nullable();
            $table->string('sub_service')->nullable();
            $table->string('subs_date')->nullable();
            $table->string('unsubs_date')->nullable();
            $table->string('charge_status')->nullable();
            $table->string('charge_date')->nullable();
            $table->string('shortcode')->nullable()->default('25656');
            $table->string('entry')->nullable();
            $table->string('last_update')->nullable();
            $table->string('in_msg_id')->nullable();
            $table->string('zoneid')->nullable();
            $table->string('flag')->nullable();
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
        Schema::dropIfExists('subscribers');
    }
}
