<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'current_country_code')) {
                $table->string('current_country_code', 10)->nullable()->after('avatar_url');
            }
            if (!Schema::hasColumn('users', 'current_city')) {
                $table->string('current_city')->nullable()->after('current_country_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('users', 'avatar_url')           ? 'avatar_url'           : null,
                Schema::hasColumn('users', 'current_country_code') ? 'current_country_code' : null,
                Schema::hasColumn('users', 'current_city')         ? 'current_city'         : null,
            ]));
        });
    }
};