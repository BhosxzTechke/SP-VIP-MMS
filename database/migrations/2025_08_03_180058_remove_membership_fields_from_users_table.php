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
        $table->dropColumn(['membership_type', 'membership_valid_until']);
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('membership_type', ['free', 'vip', 'platinum'])->default('free');
        $table->date('membership_valid_until')->nullable();
    });
    }


};
