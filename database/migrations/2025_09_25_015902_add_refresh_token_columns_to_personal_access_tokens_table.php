<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_access_tokens', 'refresh_token')) {
                $table->string('refresh_token', 64)->nullable()->after('token');
            }
            if (!Schema::hasColumn('personal_access_tokens', 'refresh_token_expires_at')) {
                $table->timestamp('refresh_token_expires_at')->nullable()->after('refresh_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn(['refresh_token', 'refresh_token_expires_at']);
        });
    }
};
