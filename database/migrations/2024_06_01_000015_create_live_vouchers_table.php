<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('live_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('discount_type', ['percentage', 'amount']);
            $table->decimal('discount_value', 10, 2);
            $table->text('description')->nullable();
            $table->foreignId('live_stream_id')->constrained('live_streams')->onDelete('cascade');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['code', 'active']);
            $table->index(['live_stream_id', 'active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('live_vouchers');
    }
};