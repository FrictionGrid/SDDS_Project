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
        Schema::create('documentchunk', function (Blueprint $table) {
            // ทำให้ข้อความเก็บได้ถูกต้อง
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            // รู้ว่ามาจากเอกสารไหน (reference ไปที่ uuid ของ ai_documents)
            $table->uuid('document_id');
            // ลำดับในเอกสาร
            $table->unsignedInteger('chunk_index');
            // เนื้อหาจริง
            $table->longText('content');
            // embedding สำหรับการค้นหา
            $table->json('embedding');
            // ข้อมูลเสริม
            $table->json('metadata')->nullable();
            $table->timestamps();

            // ความสัมพันธ์กับตาราง documentai (reference ไปที่ uuid แทน id)
            $table->foreign('document_id')
                ->references('uuid')
                ->on('documentai')
                ->cascadeOnDelete();

            //  ป้องกัน chunk_index ซ้ำในเอกสารเดียวกัน
            $table->unique(['document_id', 'chunk_index']);

            // Index สำหรับ performance
            $table->index(['document_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentchunk');
    }
};
