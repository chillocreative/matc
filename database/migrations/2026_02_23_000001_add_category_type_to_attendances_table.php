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
            $this->dropAllForeignKeysOnColumn('attendances', 'member_id');
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropUnique('attendance_member_lock');
            });
        }

        if (! $this->hasForeignKeyOnColumn('attendances', 'member_id')) {
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

    private function dropAllForeignKeysOnColumn(string $table, string $column): void
    {
        $database = DB::getDatabaseName();

        $fks = DB::table('information_schema.key_column_usage')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->whereNotNull('referenced_table_name')
            ->pluck('constraint_name');

        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk}`");
        }
    }

    private function hasForeignKeyOnColumn(string $table, string $column): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.key_column_usage')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->whereNotNull('referenced_table_name')
            ->exists();
    }
};
