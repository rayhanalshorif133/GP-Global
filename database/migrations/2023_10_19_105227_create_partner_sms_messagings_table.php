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
            $table->string('service_keyword');
            $table->string('acr_key');
            $table->string('senderName');
            $table->string('messageType')->default('ARN');
            $table->longText('message');
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
