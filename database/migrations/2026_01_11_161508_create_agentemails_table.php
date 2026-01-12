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
        Schema::create('agentemails', function (Blueprint $table) {

            $table->id();
            // สำหรับเเต่ละ user
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->index();

            // คำสั่งที่ user พิมพ์มา 
            $table->text('user_command');

            // ป้ายกลุ่ม
            $table->string('recipient_label', 120)->nullable()->index();

            // email ผู้รับ
            $table->string('to_email', 255)->index();

            // id อ้างอิงลูกค้าจาก Sheet
            $table->string('customer_ref_id', 120)->nullable()->index();

            // เนื้อหาอีเมล
            $table->string('subject', 255)->nullable();
            $table->longText('body')->nullable();

            // สถานะงาน
            $table->enum('status', ['draft', 'sent', 'cancelled'])
                ->default('draft')
                ->index();

            // เวลาเมื่อส่งสำเร็จ
            $table->timestamp('sent_at')->nullable()->index();

            $table->timestamps();

            // Performance indexes (เลือกใช้ตามการ query จริง)
            $table->index(['user_id', 'status', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agentemails');
    }
};
