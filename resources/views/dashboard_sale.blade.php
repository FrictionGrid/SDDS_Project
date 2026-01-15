@extends('layout')

@section('content')

<!-- Focus -->
<section class="focus-cards">
    <div class="focus-card urgent">
        <div class="focus-card__number">3</div>
        <div class="focus-card__label">ด่วนมาก</div>
        <div class="focus-card__subtitle">≤ 14 วัน</div>
    </div>
    <div class="focus-card warning">
        <div class="focus-card__number">5</div>
        <div class="focus-card__label">เสี่ยงหลุด</div>
        <div class="focus-card__subtitle">Proposal เกิน 7 วัน</div>
    </div>
    <div class="focus-card info">
        <div class="focus-card__number">8</div>
        <div class="focus-card__label">ต้อง Follow-up</div>
        <div class="focus-card__subtitle">ไม่ติดต่อ &gt; 7 วัน</div>
    </div>
    <div class="focus-card success">
        <div class="focus-card__number">6</div>
        <div class="focus-card__label">งานวันนี้</div>
        <div class="focus-card__subtitle">Tasks Today</div>
    </div>
</section>

<!-- Tasks Today -->
<section class="tasks">
    <div class="tasks__header">งานที่ต้องทำวันนี้</div>

    <div class="task-item">
        <div>
            <div class="task-title">โทร Follow-up ลูกค้า A</div>
            <div class="task-sub">Project Event Q2</div>
        </div>
    </div>

    <div class="task-item">
        <div>
            <div class="task-title">ส่ง Proposal ลูกค้า B</div>
            <div class="task-sub">งานเปิดตัวสินค้า</div>
        </div>
    </div>
</section>

<!-- Priority Clients -->
<section class="priority">
    <div class="priority__header">รายชื่อลูกค้าตามลำดับความสำคัญ</div>

    <table>
        <thead>
        <tr>
            <th>ลูกค้า</th>
            <th>ประเภทงาน</th>
            <th>วันเริ่ม</th>
            <th>เหลือ</th>
            <th>Next Action</th>
            <th>สถานะ</th>
            <th>ความด่วน</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <div class="client-name">คุณสมชาย</div>
                <div class="client-company">ABC Organizer</div>
            </td>
            <td>Event</td>
            <td>20 มี.ค.</td>
            <td>12 วัน</td>
            <td>โทร Follow-up</td>
            <td>รอตอบกลับ</td>
            <td><span class="badge high">สูง</span></td>
        </tr>
        </tbody>
    </table>
</section>

<!-- Work Log -->
<section class="card">
    <div class="card__header">บันทึกงานที่ทำในวันนี้</div>

    <div class="card__body">
        <div class="form-group">
            <label>รายละเอียดสิ่งที่ทำ</label>
            <textarea placeholder="รายละเอียดงานที่ทำ / สิ่งที่ลูกค้าต้องการ / ปัญหาที่พบ"></textarea>
        </div>

        <div class="form-actions">
            <button class="btn">บันทึกงานวันนี้</button>
        </div>
    </div>
</section>

@endsection
