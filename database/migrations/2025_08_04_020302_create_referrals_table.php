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
                $table->id();

                $table->foreignId('referrer_id')->constrained('users');
                $table->foreignId('referred_id')->constrained('users');

                $table->decimal('commission_amount', 10, 2);
                $table->decimal('commission_rate', 5, 2);

                $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
                $table->enum('trigger_event', ['membership_upgrade'])->default('membership_upgrade');

                $table->timestamp('approved_at')->nullable();
                $table->timestamp('paid_at')->nullable();

                $table->text('notes')->nullable();

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
