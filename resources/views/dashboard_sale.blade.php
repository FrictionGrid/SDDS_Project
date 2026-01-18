@extends('layout')

@section('title', 'SDDS | Dashboard Sale')

@section('page-title', 'Dashboard Sale')

@section('breadcrumb', 'Dashboard / Sale')

@section('styles')
    <style>
        /* Dashboard Sale Custom Styles */
        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Focus Cards - ปรับสีให้เข้ากับโปรเจค */
        .focus-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .focus-card {
            background: var(--panel);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--primary);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .focus-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .focus-card.urgent {
            border-left-color: #ef4444;
        }

        .focus-card.warning {
            border-left-color: #f59e0b;
        }

        .focus-card.info {
            border-left-color: var(--primary);
        }

        .focus-card.success {
            border-left-color: #10b981;
        }

        .focus-card__number {
            font-size: 36px;
            font-weight: 700;
            line-height: 1;
            color: var(--text);
        }

        .focus-card__label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-top: 8px;
        }

        .focus-card__subtitle {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
        }

        /* Tasks Section - ปรับสีให้เข้ากับโปรเจค */
        .tasks {
            background: var(--panel);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: 28px;
        }

        .tasks__header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 16px;
            color: var(--text);
        }

        .task-item {
            display: flex;
            align-items: center;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            transition: background 0.2s;
        }

        .task-item:last-child {
            border-bottom: none;
        }

        .task-item:hover {
            background: #f8fafc;
        }

        .task-title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            max-width: 100%;
        }

        .task-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
            max-width: 100%;
        }


        .task-sub {
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px;
        }

        /* Priority Table - ปรับสีให้เข้ากับโปรเจค */
        .priority {
            background: var(--panel);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 28px;
        }

        .priority__header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 16px;
            color: var(--text);
        }

        .priority table {
            width: 100%;
            border-collapse: collapse;
        }

        .priority th {
            background: #f8fafc;
            text-align: left;
            padding: 16px 20px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid var(--border);
        }

        .priority td {
            padding: 18px 20px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            color: var(--text);
        }

        .priority tbody tr:last-child td {
            border-bottom: none;
        }

        .priority tbody tr:hover {
            background: #f8fafc;
        }

        .client-name {
            font-weight: 600;
            color: var(--text);
        }

        .client-company {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }

        /* Badge - ใช้สไตล์เดียวกับโปรเจค */
        .badge {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge.high {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge.medium {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.low {
            background: #d1fae5;
            color: #065f46;
        }

        /* Work Log Card - ปรับสีให้เข้ากับโปรเจค */
        .card {
            background: var(--panel);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .card__header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 16px;
            color: var(--text);
        }

        .card__body {
            padding: 24px;
        }

        .card__body .form-group {
            margin-bottom: 20px;
        }

        .card__body .form-group label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 8px;
            display: block;
        }

        .card__body .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-size: 14px;
            color: var(--text);
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
            transition: border 0.2s, box-shadow 0.2s;
        }

        .card__body .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        .form-actions {
            margin-top: 20px;
            text-align: right;
        }

        .form-actions .btn {
            background: linear-gradient(135deg, var(--primary), #2563eb);
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .form-actions .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.25);
        }

        /* ============================= */
        /* TASKS TODAY (Action First)   */
        /* ============================= */

        .section.tasks {
            background: var(--panel);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 28px;
        }

        /* Header */
        .section__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
        }

        .section__title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
        }

        .section__meta {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 13px;
            color: var(--muted);
            flex-wrap: wrap;
        }

        /* Daily Goal */
        .goal {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 10px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid var(--border);
        }

        .goal__label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .goal__value {
            font-size: 12px;
            font-weight: 700;
            color: var(--text);
        }

        .goal__bar {
            width: 100px;
            height: 6px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
        }

        .goal__bar span {
            display: block;
            height: 100%;
            width: 50%;
            /* ปรับจาก backend */
            background: linear-gradient(135deg, var(--primary), #2563eb);
        }

        /* Body */
        .tasks__body {
            padding: 8px 0;
        }

        /* Task Item */
        .task-item {
            display: flex;
            gap: 14px;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            transition: background 0.2s ease;
        }

        .task-item:last-child {
            border-bottom: none;
        }

        .task-item:hover {
            background: #f8fafc;
        }

        /* Checkbox */
        .task-check {
            margin-top: 4px;
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        /* Main Content */
        .task-main {
            flex: 1;
            min-width: 0;
        }

        /* Title Row */
        .task-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .task-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Actions */
        .task-actions {
            display: flex;
            gap: 8px;
        }

        /* Reason */
        .task-reason {
            margin-top: 4px;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.4;
        }

        /* Tags */
        .task-tags {
            margin-top: 8px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Pills */
        .pill {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid var(--border);
            background: #ffffff;
            color: #475569;
        }

        .pill.system {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .pill.manager {
            background: #fef3c7;
            border-color: #fde68a;
            color: #92400e;
        }

        .pill.self {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }

        .pill.urgent {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .pill.warning {
            background: #fef3c7;
            border-color: #fde68a;
            color: #92400e;
        }

        .pill.info {
            background: #e0f2fe;
            border-color: #bae6fd;
            color: #075985;
        }

        /* Buttons */
        .btn-sm {
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #ffffff;
            color: var(--text);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-sm:hover {
            background: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
        }

        .btn-primary-sm {
            border: none;
            background: linear-gradient(135deg, var(--primary), #2563eb);
            color: #ffffff;
        }

        .btn-primary-sm:hover {
            box-shadow: 0 8px 18px rgba(30, 64, 175, 0.25);
        }

        /* ===== Task Urgency Inline ===== */
        .task-urgency {
            margin-left: 10px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
            vertical-align: middle;
        }

        /* ด่วนมาก = แดง (เหมือน focus-card urgent) */
        .task-urgency.critical {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* ด่วน = ส้ม */
        .task-urgency.high {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        /* ปานกลาง = น้ำเงิน */
        .task-urgency.medium {
            background: #e0f2fe;
            color: #075985;
            border: 1px solid #bae6fd;
        }

        /* ไม่ด่วน = เขียว */
        .task-urgency.low {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }


        /* Responsive */
        @media (max-width: 768px) {
            .section__header {
                flex-direction: column;
                align-items: flex-start;
            }

            .task-title-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .task-actions {
                width: 100%;
            }
        }


        /* Responsive */
        @media (max-width: 768px) {
            .focus-cards {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .priority {
                overflow-x: auto;
            }

            .priority table {
                min-width: 800px;
            }
        }

    </style>
@endsection

@section('content')

    <div class="content-wrapper">

        <!-- Focus -->
        <section class="focus-cards">
            <div class="focus-card urgent">
                <div class="focus-card__number">1</div>
                <div class="focus-card__label">ด่วนมาก</div>
                <div class="focus-card__subtitle">ควรทำตอนนี้</div>
            </div>
            <div class="focus-card warning">
                <div class="focus-card__number">1</div>
                <div class="focus-card__label">ด่วน</div>
                <div class="focus-card__subtitle">ควรทำภายในวันนี้</div>
            </div>
            <div class="focus-card info">
                <div class="focus-card__number">1</div>
                <div class="focus-card__label">ปกติ</div>
                <div class="focus-card__subtitle">ควรทำภายในอาทิตย์นี้</div>
            </div>
            <div class="focus-card success">
                <div class="focus-card__number">1</div>
                <div class="focus-card__label">ไม่เร่งด่วน</div>
                <div class="focus-card__subtitle">ควรวางเเผนการทำ</div>
            </div>
        </section>

        <!-- Tasks Today -->
        <!-- 1) TASKS TODAY (Action First) -->
        <section class="section tasks">
            <div class="section__header">
                <div class="section__title">งานที่ต้องทำสัปดาห์นี้</div>

                <div class="section__meta">
                    <div class="goal">
                        <span class="goal__label">Week Goal</span>
                        <span class="goal__value">2 / 4</span>
                        <span class="goal__bar"><span></span></span>
                    </div>
               
                </div>
            </div>

            <div class="tasks__body">
                <!-- Task 1 -->
                <div class="task-item">
                    <input class="task-check" type="checkbox" />
                    <div class="task-main">
                        <div class="task-title-row">
                            <div class="task-title">
                                <span class="task-text">โทร Follow-up ลูกค้า A</span>
                                <span class="task-urgency critical">ด่วนมาก</span>
                            </div>
                            <div class="task-actions">
                                <button class="btn-sm">พบปัญหา</button>
                                <button class="btn-sm btn-primary-sm">ทำเสร็จ</button>
                            </div>
                        </div>
                        <div class="task-reason">เหตุผล: ส่ง Proposal มาแล้ว 8 วัน | งานเริ่มใน 12 วัน | มูลค่า ฿480,000
                        </div>

                    </div>
                </div>

                <!-- Task 2 -->
                <div class="task-item">
                    <input class="task-check" type="checkbox" />
                    <div class="task-main">
                        <div class="task-title-row">
                            <div class="task-title">
                                <span class="task-text">ส่ง Proposal ลูกค้า B</span>
                                <span class="task-urgency high">ด่วน</span>
                            </div>
                            <div class="task-actions">
                                <button class="btn-sm">พบปัญหา</button>
                                <button class="btn-sm btn-primary-sm">ทำเสร็จ</button>
                            </div>
                        </div>
                        <div class="task-reason">เหตุผล: นัดคุยจบแล้ว | ขอเอกสารวันนี้ | ปิดดีลภายในสัปดาห์นี้</div>

                    </div>
                </div>

                <!-- Task 3 -->
                <div class="task-item">
                    <input class="task-check" type="checkbox" />
                    <div class="task-main">
                        <div class="task-title-row">
                            <div class="task-title">
                                <span class="task-text">ทักไลน์ลูกค้า C เพื่อยืนยันนัด</span>
                                <span class="task-urgency medium">ปกติ</span>
                            </div>
                            <div class="task-actions">
                                <button class="btn-sm">พบปัญหา</button>
                                <button class="btn-sm btn-primary-sm">ทำเสร็จ</button>
                            </div>
                        </div>
                        <div class="task-reason">เหตุผล: มีนัดพรุ่งนี้ | ต้องยืนยันก่อน 18:00</div>

                    </div>
                </div>

                    <!-- Task 3 -->
                <div class="task-item">
                    <input class="task-check" type="checkbox" />
                    <div class="task-main">
                        <div class="task-title-row">
                            <div class="task-title">
                                <span class="task-text">ทักไลน์ลูกค้า D เพื่อยืนยันนัด</span>
                                <span class="task-urgency low">ไม่เร่งด่วน</span>
                            </div>
                            <div class="task-actions">
                                <button class="btn-sm">พบปัญหา</button>
                                <button class="btn-sm btn-primary-sm">ทำเสร็จ</button>
                            </div>
                        </div>
                        <div class="task-reason">เหตุผล: มีนัดพรุ่งนี้ | ต้องยืนยันก่อน 18:00</div>

                    </div>
                </div>


                <!-- Empty state (ตัวอย่าง - ปิดไว้ก่อน)
                <div class="task-item">
                    <div class="task-main">
                        <div class="task-title">วันนี้ไม่มีงานด่วน</div>
                        <div class="task-reason">แนะนำ: ตรวจ “ลูกค้าเสี่ยงหลุด” 3 ราย</div>
                    </div>
                </div>
                -->
            </div>
        </section>
        <!-- Priority Clients -->
        <section class="priority">
            <div class="priority__header">รายชื่อลูกค้าตามความดูเเล</div>

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
                            <div class="client-name">บริษัท เอ็กซ์โป พลัส จำกัด</div>
                            <div class="client-company">Organizer</div>
                        </td>
                        <td>ThaiExpo</td>
                        <td>20 มี.ค.</td>
                        <td>12 วัน</td>
                        <td>โทร Follow-up</td>
                        <td>รอตอบกลับ</td>
                        <td><span class="badge high">สูง</span></td>
                    </tr>
                      <tr>
                        <td>
                            <div class="client-name">บริษัท ไทยอีเวนต์ โซลูชั่น จำกัด</div>
                            <div class="client-company">Organizer</div>
                        </td>
                        <td>ThaiExpo</td>
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

    </div>

 

@endsection



