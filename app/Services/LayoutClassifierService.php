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
        'กระบวนการ','ขั้นตอน','วิธีการ','แนวทาง','วิธี',
        'process','procedure','step','method','approach','way'

        // เพิ่มเติม
        ,'ชื่ออะไร','ที่อยู่','เบอร์โทร','email','อีเมล','contact','address','phone'

    ];

    public function classify(string $message): string
    {
        $msg = mb_strtolower($message);

        // Question words that usually require facts
        if (preg_match('/(กี่|เมื่อไร|เท่าไร|วันไหน|amount|price|how much|when)/u', $msg)) {
            return 'specific';
        }

        foreach ($this->businessKeywords as $kw) {
            if (str_contains($msg, $kw)) {
                return 'specific';
            }
        }

        return 'general';
    }
}
