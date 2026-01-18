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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Role identifier (e.g., ceo, sale)');
            $table->string('display_name')->comment('Display name (e.g., CEO, ฝ่ายขาย)');
            $table->text('description')->nullable()->comment('Role description');
            $table->boolean('is_active')->default(true)->comment('Active status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
