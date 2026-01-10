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
        Schema::create('chathistorys', function (Blueprint $table) {
            $table->id();
            // ระบุว่าเป็นแชทครั้งไหน
            $table->uuid('conversation_id')->index();
            // ระบุว่าใครเป็นเจ้าของการสนทนา (nullable ไว้ก่อน สำหรับ guest/testing)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // ใครพูด
            $table->enum('role', ['user', 'assistant', 'system'])->index();
            // ข้อความ
            $table->longText('content');
            // เผื่อขยาย (วันนี้ไม่ต้องใช้)
            // อธิบายว่าคุยเรื่องอะไร
            $table->string('scope_type')->nullable();
            // คุยเรื่อง id ไหน เช่น ลูกค้าคนไหน โปรเจคไหน
            $table->unsignedBigInteger('scope_id')->nullable();
            // เก็บข้อมูลเสริมอื่นๆ
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Compound indexes สำหรับ performance
            $table->index(['conversation_id', 'created_at']);
            $table->index(['user_id', 'conversation_id']);
            $table->index(['scope_type', 'scope_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chathistorys');
    }
};
