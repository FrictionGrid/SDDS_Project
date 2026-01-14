<?php

namespace App\Services;

class LayoutClassifierService
{
    protected array $businessKeywords = [
        // เอกสารและธุรกรรม
        'สัญญา','invoice','ใบแจ้งหนี้','ใบสั่งซื้อ','po','quotation','quote',
        'ราคา','เงื่อนไข','กำหนดชำระ','วันชำระ','ยอดเงิน','project','ลูกค้า',
        'contract','payment','due','term','company','เอกสาร',

        // หน้าที่และบทบาท
        'ทำอะไร','หน้าที่','บทบาท','ภารกิจ','งาน','ตำแหน่ง','รับผิดชอบ',
        'responsibility','role','task','duty','job','position','work',

        // รายละเอียดและข้อมูล
        'รายละเอียด','ข้อมูล','สเปค','ข้อกำหนด','คุณสมบัติ',
        'spec','detail','info','information','requirement',

        // ตำแหน่งและบุคคล
        'pm','dev','developer','designer','ux','ui','qa','tester',
        'manager','lead','engineer','programmer','analyst',

        // กระบวนการและวิธีการ
        'กระบวนการ','ขั้นตอน','วิธีการ','แนวทาง','วิธี','บริษัทชื่ออะไร',
        'process','procedure','step','method','approach','way',

        // การติดต่อ
       'ที่อยู่','เบอร์โทร','email','อีเมล','contact','address','phone'
    ];

    /**
     * @return string  agent_email | specific | general
     */
    // อนาคตต้องมีเขียนใหม่ เดี่ยวช้า //
    public function classify(string $message): string
    {
        $msg = mb_strtolower(trim($message));

             // -------------------------
        // AgentSearch Detection
        // -------------------------
        if (
            str_starts_with($msg, 'agentsearch') ||
            str_starts_with($msg, 'agent search') ||
            str_starts_with($msg, 'search:') ||
            str_contains($msg, 'หาเว็บ') ||
            str_contains($msg, 'ค้นหาเว็บ') ||
            str_contains($msg, 'ดึงข้อมูลจากเว็บ') ||
            str_contains($msg, 'เก็บข้อมูลจากเว็บ') ||
            str_contains($msg, 'scrape') ||
            str_contains($msg, 'crawler')
        ) {
            return 'agent_search';
        }


        // Agent email //
        if (
            str_starts_with($msg, 'agentemail') ||
            str_starts_with($msg, 'agent email') ||
            str_starts_with($msg, 'email:') ||
            str_starts_with($msg, 'mail:') ||
            str_contains($msg, 'ส่งอีเมล') ||
            str_contains($msg, 'ส่ง email') ||
            str_contains($msg, 'ส่ง mail')
        ) {
            return 'agent_email';
        }

        // specific question 
        if (preg_match('/(กี่|เมื่อไร|เท่าไร|วันไหน|amount|price|how much|when)/u', $msg)) {
            return 'specific';
        }

       // ดึงคีย์เวิร์ดธมาจับ //
        foreach ($this->businessKeywords as $kw) {
            if (str_contains($msg, mb_strtolower($kw))) {
                return 'specific';
            }
        }

      // อื่นๆ ทั่วไป //
        return 'general';
    }
}
