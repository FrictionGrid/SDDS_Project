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
        Schema::create('documentai', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            // ชื่อเรื่องของเอกสาร
            $table->string('title')->nullable();
            // ข้อมูลมาจากไหน
            $table->string('source_type')->default('file');
            // ชื่อต้นทาง
            $table->string('original_name')->nullable();
            // ชนิดของไฟล์
            $table->string('mime_type')->nullable();
            // ข้อมูลเก็บที่ไหน
            $table->string('disk')->default('local');
            // เก็บ path ไฟล์
            $table->string('path')->nullable();
            // ขนาดไฟล์
            $table->unsignedBigInteger('size_bytes')->default(0);
            // สถานะเพื่อ Debug
            $table->enum('status', ['uploaded', 'processing', 'indexed', 'failed'])
                ->default('uploaded');
            // เกี่ยวกับเรื่องอะไรฟ
            $table->string('scope_type')->nullable();
            // ไว้จับกับโปรเจคหรือลุกค้าในอนาคต
            $table->unsignedBigInteger('scope_id')->nullable();
            // เก็บข้อมูลเสริมอื่นๆ
            $table->json('metadata')->nullable();
            // ข้อความแจ้งเตือนเมื่อเกิดข้อผิดพลาด
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Compound indexes สำหรับ performance
            $table->index(['scope_type', 'scope_id']);
            $table->index(['status', 'created_at']);
            $table->index(['source_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentai');
    }
};
