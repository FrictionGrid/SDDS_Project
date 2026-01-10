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
        Schema::create('contacthistory', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->nullable()->index()->comment('อ้างอิง ID จาก Google Sheets');
            $table->string('contact_type', 50)->nullable()->comment('ประเภท: call, email, meeting, line, other');
            $table->string('subject')->nullable()->comment('หัวข้อ');
            $table->text('description')->nullable()->comment('รายละเอียด');
            $table->dateTime('contacted_at')->nullable()->comment('วันเวลาที่ติดต่อ');
            $table->string('contacted_by', 100)->nullable()->comment('ผู้ติดต่อ');
            $table->string('status', 50)->nullable()->comment('สถานะ: completed, pending, follow_up');
            $table->timestamps();

            // Index สำหรับการค้นหา
            $table->index('contacted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacthistory');
    }
};
