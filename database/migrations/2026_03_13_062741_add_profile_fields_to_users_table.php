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
        $table->string('avatar_url')->nullable()->after('avatar');
        $table->string('current_country_code', 10)->nullable()->after('avatar_url');
        $table->string('current_city')->nullable()->after('current_country_code');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['avatar_url', 'current_country_code', 'current_city']);
    });
}
};
