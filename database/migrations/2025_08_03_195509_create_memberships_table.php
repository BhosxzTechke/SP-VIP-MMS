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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();

            // Link to users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Membership details
            $table->enum('tier', ['gold', 'platinum', 'diamond']);
            $table->integer('amount'); // in centavos: 300000 = ₱3,000.00
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');

            // Payment tracking
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete(); // link to transactions
            $table->string('paymongo_payment_id')->nullable(); // PayMongo/Stripe/etc. ID
            $table->json('payment_details')->nullable(); // optional full JSON response

            // Membership validity
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
