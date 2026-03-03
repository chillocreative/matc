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
        Schema::table('meetings', function (Blueprint $table) {
            $table->boolean('suggestion_enabled')->default(false)->after('status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->text('suggestion')->nullable()->after('absence_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('suggestion_enabled');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('suggestion');
        });
    }
};
