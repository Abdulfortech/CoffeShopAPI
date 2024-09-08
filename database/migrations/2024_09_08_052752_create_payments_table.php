<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('payments');
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userID');
            $table->unsignedBigInteger('orderID');
            $table->decimal('amount', 10, 2);
            $table->string('gateway')->nullable();
            $table->string('reference')->nullable();
            $table->string('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('orderID')->references('id')->on('orders')->onDelete('no action');
            $table->foreign('userID')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
