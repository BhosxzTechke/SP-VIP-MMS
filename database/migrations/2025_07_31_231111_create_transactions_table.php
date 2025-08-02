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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // membership_upgrade, product_order, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('PHP');
            $table->string('payment_method')->nullable();
            $table->json('payment_metadata')->nullable(); // custom data like tier, etc.
            $table->string('external_payment_id')->nullable(); // PayMongo ID
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
