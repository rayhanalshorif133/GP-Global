<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerSmsMessagingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_sms_messagings', function (Blueprint $table) {
            $table->id();
            $table->string('senderNumber');
            $table->string('keyword');
            $table->string('acr_key');
            $table->string('senderName')->nullable();
            $table->string('messageType')->default('ARN');
            $table->longText('message')->nullable();
            $table->string('status')->commit('1 means success and 0 means failure');
            $table->json('payload');
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
        Schema::dropIfExists('partner_sms_messagings');
    }
}
