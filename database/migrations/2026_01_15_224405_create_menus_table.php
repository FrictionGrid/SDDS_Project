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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Menu name');
            $table->string('url')->comment('Menu URL/route');
            $table->string('icon')->nullable()->comment('Icon class or name');
            $table->string('permission_key')->comment('Permission key required to view this menu');
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade')->comment('Parent menu for submenu');
            $table->integer('order')->default(0)->comment('Display order');
            $table->boolean('is_active')->default(true)->comment('Active status');
            $table->timestamps();

            // Index for performance
            $table->index('permission_key');
            $table->index('parent_id');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
