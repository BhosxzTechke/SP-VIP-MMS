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
        Schema::create('referrals', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('referrer_id'); // The one who referred
            $table->unsignedBigInteger('referred_id'); // The one who was referred

            $table->enum('membership_tier_referred', ['gold', 'platinum', 'diamond'])->nullable(); // VIP tier purchased
            $table->decimal('commission_amount', 10, 2)->nullable(); // ₱ value
            $table->decimal('commission_rate', 5, 2)->nullable(); // e.g., 5.00, 8.00

            $table->string('status')->default('pending'); // e.g., pending, approved, paid

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->text('notes')->nullable(); // any remarks
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
