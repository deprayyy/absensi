<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('clock_in_photo')->nullable()->after('clock_in');
            $table->string('clock_out_photo')->nullable()->after('clock_out');
            $table->text('activity_note')->nullable()->after('clock_out_photo');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['clock_in_photo', 'clock_out_photo', 'activity_note']);
        });
    }
};
