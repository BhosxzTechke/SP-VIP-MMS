<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
        {
            Schema::create('referral_codes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // owner of the code
                $table->string('code')->unique();
                $table->integer('usage_limit')->nullable(); // max number of times this code can be used
                $table->integer('uses')->default(0); // how many times it was used
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_codes');
    }
};
