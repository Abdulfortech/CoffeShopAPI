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
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('userID');
            $table->unsignedBigInteger('coffeeID');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->integer('status');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('userID')->references('id')->on('users')->onDelete('no action');
            $table->foreign('coffeeID')->references('id')->on('coffees')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
