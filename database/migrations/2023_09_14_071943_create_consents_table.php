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
            $table->foreignId("service_id")->constrained("services")->onDelete("cascade")->onUpdate("cascade");
            $table->string('amount');
            $table->string('msisdn');
            $table->string('currency');
            $table->string('subscriptionPeriod');
            $table->string('is_subscription')->default(0);
            $table->json('urls');
            $table->string('api_url');
            $table->string('customer_reference')->nullable();
            $table->string('consentId')->nullable();
            $table->string('result_code')->nullable();
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
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
