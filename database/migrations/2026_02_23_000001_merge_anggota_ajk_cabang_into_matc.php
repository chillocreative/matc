<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('members')
            ->whereIn('category_type', ['anggota', 'ajk_cabang'])
            ->update(['category_type' => 'matc']);
    }

    public function down(): void
    {
        // Cannot reliably reverse this merge
    }
};
