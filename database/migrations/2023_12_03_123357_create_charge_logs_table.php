<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_logs', function (Blueprint $table) {
            $table->id();
            $table->string('acr_key')->nullable();
            $table->string('msisdn')->nullable();
            $table->string('keyword')->nullable();
            $table->string('amount')->nullable();
            $table->string('type')->nullable()->comment('subs and renew');
            $table->date('charge_date')->nullable();
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
        Schema::dropIfExists('charge_logs');
    }
}
