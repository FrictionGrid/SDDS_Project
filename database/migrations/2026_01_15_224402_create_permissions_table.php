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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Permission key (e.g., view_dashboard, view_customers)');
            $table->string('name')->comment('Display name');
            $table->text('description')->nullable()->comment('Permission description');
            $table->string('group')->nullable()->comment('Permission group (e.g., Dashboard, Customer Management)');
            $table->boolean('is_active')->default(true)->comment('Active status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
