<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_payments', function (Blueprint $table) {
            $table->id();
            $table->string('acr_key');
            $table->string('referenceCode');
            $table->string('service_keyword');
            $table->string('mandateId_subscription_num');
            $table->string('consentId');
            $table->json('response');
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
        Schema::dropIfExists('partner_payments');
    }
}
