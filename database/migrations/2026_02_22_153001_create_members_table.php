<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('category_type');
            $table->string('name');
            $table->text('ic_number');
            $table->string('ic_number_hash', 64);
            $table->text('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('position_type')->nullable();
            $table->string('position_name')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique('ic_number_hash');
            $table->index('category_type');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
