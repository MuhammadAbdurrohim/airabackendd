<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->nullable(); // WhatsApp message ID
            $table->string('phone_number');
            $table->text('message');
            $table->string('status')->default('pending'); // pending, sent, delivered, read, failed
            $table->string('direction')->default('outbound'); // inbound or outbound
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->json('metadata')->nullable(); // For additional WhatsApp message data
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};