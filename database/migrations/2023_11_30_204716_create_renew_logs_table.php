<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRenewLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('renew_logs', function (Blueprint $table) {
            $table->id();
            $table->string('acr_key')->nullable();
            $table->string('status_code')->nullable();
            $table->string('status')->nullable();
            $table->string('serverReferenceCode')->nullable();
            $table->string('resourceURL')->nullable();
            $table->string('transactionOperationStatus')->nullable();
            $table->string('totalAmountCharged')->nullable();
            $table->string('amount')->nullable();
            $table->string('description')->nullable();
            $table->string('referenceCode')->nullable();
            $table->string('currency')->nullable();
            $table->string('purchaseCategoryCode')->nullable();
            $table->string('service_keyword')->nullable();
            $table->string('operatorId')->nullable();
            $table->string('subscription')->nullable();
            $table->string('consentId')->nullable();
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->string('msisdn')->nullable();
            $table->string('keyword')->nullable();
            $table->string('created')->nullable();
            $table->string('updated')->nullable();
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
        Schema::dropIfExists('renew_logs');
    }
}
