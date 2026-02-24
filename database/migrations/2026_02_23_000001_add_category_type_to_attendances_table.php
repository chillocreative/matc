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
        // Step 1: Add column if missing (may exist from partial run)
        if (! Schema::hasColumn('attendances', 'category_type')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->string('category_type')->after('ic_number_hash');
            });
        }

        // Step 2: Drop ALL foreign keys on the table so indexes can be freely dropped
        $this->dropAllForeignKeysOnTable('attendances');

        // Step 3: Drop old unique indexes if they still exist
        if ($this->indexExists('attendance_ic_lock')) {
            DB::statement('ALTER TABLE `attendances` DROP INDEX `attendance_ic_lock`');
        }

        if ($this->indexExists('attendance_member_lock')) {
            DB::statement('ALTER TABLE `attendances` DROP INDEX `attendance_member_lock`');
        }

        // Step 4: Add new unique constraint if missing
        if (! $this->indexExists('attendance_category_lock')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->unique(['meeting_id', 'ic_number_hash', 'category_type'], 'attendance_category_lock');
            });
        }

        // Step 5: Re-add foreign keys
        if (! $this->hasForeignKey('attendances', 'meeting_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->foreign('meeting_id')->references('id')->on('meetings')->cascadeOnDelete();
            });
        }

        if (! $this->hasForeignKey('attendances', 'member_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
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

    private function dropAllForeignKeysOnTable(string $table): void
    {
        $fks = DB::select(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$table]
        );

        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }
    }

    private function indexExists(string $indexName): bool
    {
        return count(DB::select(
            "SHOW INDEX FROM `attendances` WHERE Key_name = ?",
            [$indexName]
        )) > 0;
    }

    private function hasForeignKey(string $table, string $column): bool
    {
        return count(DB::select(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$table, $column]
        )) > 0;
    }
};
