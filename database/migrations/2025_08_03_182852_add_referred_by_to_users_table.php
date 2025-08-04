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
    Schema::table('users', function (Blueprint $table) {
        // Add self-referencing foreign key
        $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code');

        // Add foreign key constraint
        $table->foreign('referred_by')->references('id')->on('users')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['referred_by']);
        $table->dropColumn('referred_by');
    });
}
};
