<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('attendances', 'category_type')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->string('category_type')->after('ic_number_hash');
            });
        }

        if ($this->indexExists('attendance_ic_lock')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropUnique('attendance_ic_lock');
            });
        }

        if ($this->indexExists('attendance_member_lock')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropForeign(['member_id']);
            });
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropUnique('attendance_member_lock');
            });
        }

        if (! $this->foreignKeyExists('attendances_member_id_foreign')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            });
        }

        if (! $this->indexExists('attendance_category_lock')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->unique(['meeting_id', 'ic_number_hash', 'category_type'], 'attendance_category_lock');
            });
        }
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendance_category_lock');

            $table->unique(['meeting_id', 'ic_number_hash'], 'attendance_ic_lock');
            $table->unique(['meeting_id', 'member_id'], 'attendance_member_lock');

            $table->dropColumn('category_type');
        });
    }

    private function indexExists(string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', 'attendances')
            ->where('index_name', $indexName)
            ->exists();
    }

    private function foreignKeyExists(string $keyName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.table_constraints')
            ->where('table_schema', $database)
            ->where('table_name', 'attendances')
            ->where('constraint_name', $keyName)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }
};
