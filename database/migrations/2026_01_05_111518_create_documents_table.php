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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->comment('รหัสลูกค้า');
            $table->string('file_name')->comment('ชื่อไฟล์ต้นฉบับ');
            $table->string('file_path')->comment('path ที่เก็บไฟล์');
            $table->unsignedBigInteger('file_size')->comment('ขนาดไฟล์ (bytes)');
            $table->string('file_type', 10)->comment('นามสกุลไฟล์ เช่น pdf, jpg');
            $table->string('mime_type', 100)->comment('MIME type สำหรับ validation');
            $table->string('uploaded_by')->nullable()->comment('ชื่อผู้อัพโหลด');
            $table->timestamps();

            // Index สำหรับค้นหาเอกสารของลูกค้าแต่ละคน
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
