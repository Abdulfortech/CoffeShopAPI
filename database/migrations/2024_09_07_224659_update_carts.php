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
        //
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('userID');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('userID')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('orderID');
            $table->foreign('orderID')->references('id')->on('orders')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
