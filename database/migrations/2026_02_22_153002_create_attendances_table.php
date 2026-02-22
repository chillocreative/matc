<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('ic_number_hash', 64);
            $table->string('status')->default('present');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['meeting_id', 'ic_number_hash'], 'attendance_ic_lock');
            $table->unique(['meeting_id', 'member_id'], 'attendance_member_lock');
            $table->index('ic_number_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
