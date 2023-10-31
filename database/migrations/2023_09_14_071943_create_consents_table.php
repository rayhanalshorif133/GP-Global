<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained("products")->onDelete("cascade")->onUpdate("cascade");
            $table->string('amount');
            $table->string('msisdn');
            $table->string('currency');
            $table->string('subscriptionPeriod');
            $table->json('urls');
            $table->string('serviceName');
            $table->string('customer_reference')->nullable();
            $table->string('consentId')->nullable();
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
        Schema::dropIfExists('consents');
    }
}
