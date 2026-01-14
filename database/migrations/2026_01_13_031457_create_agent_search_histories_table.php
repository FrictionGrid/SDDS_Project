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
        Schema::create('agentsearchhistory', function (Blueprint $table) {
            $table->id();
            // ดู user ไหนสั่ง
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // สั่งว่าอะไร 
            $table->text('command');
            // เอไอประมวลผมยังไง 
            $table->string('query');

            $table->json('target_urls')->nullable();

            $table->string('apify_run_id')->nullable()->index();
            $table->string('apify_dataset_id')->nullable()->index();

            $table->enum('status', ['running', 'completed', 'failed'])->default('running');

            $table->unsignedInteger('total_found')->default(0);
            $table->unsignedInteger('inserted_count')->default(0);

            // 🔥 สำหรับ CRM ในอนาคต
            $table->unsignedBigInteger('customer_batch_id')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agentsearchhistory');
    }
};
