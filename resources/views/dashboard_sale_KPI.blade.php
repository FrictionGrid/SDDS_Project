@extends('layout')

@section('title', 'SDDS | รายชื่อลูกค้าหลัก')
@section('page-title', 'รายชื่อลูกค้าหลัก')
@section('breadcrumb', 'Dashboard / Customers')

@section('styles')
<style>
    /* ============================= */
    /* Activity Modal                */
    /* ============================= */
    .activity-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .activity-modal.active {
        display: flex;
    }

    .activity-modal__overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
    }

    .activity-modal__panel {
        position: relative;
        width: 90%;
        max-width: 540px;
        max-height: 85vh;
        background: var(--panel);
        border-radius: var(--radius-lg);
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.3);
        display: flex;
        flex-direction: column;
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .activity-modal__header {
        padding: 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .activity-modal__header h3 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin: 0;
    }

    .activity-modal__close {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: var(--muted);
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }

    .activity-modal__close:hover {
        background: #f1f5f9;
        color: var(--text);
    }

    .activity-modal__body {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
    }

    .activity-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .activity-modal__footer {
        padding: 20px 24px;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8fafc;
    }

    /* Responsive for Modal */
    @media (max-width: 768px) {
        .activity-modal__panel {
            width: 95%;
            max-height: 90vh;
        }

        .activity-modal__footer {
            flex-direction: column;
        }

        .activity-modal__footer .btn-cancel,
        .activity-modal__footer .btn-submit {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<!-- Toolbar -->
<div class="toolbar">
    <input type="text" class="search-input" placeholder="" />

    <button class="btn btn-secondary">
        ตัวกรอง
    </button>

    <div class="dropdown">
        <select>
            <option>ผู้ใช้งาน</option>
            <option>userA</option>
            <option>userB</option>
            <option>userC</option>
        </select>
    </div>

    <div class="dropdown">
        <select>
            <option>วันที่</option>
            <option>วันนี้</option>
            <option>อาทิตย์นี้</option>
             <option>เดือนนี้</option>
        </select>
    </div>

    <button class="btn btn-primary" onclick="openActivityModal()">
        เพิ่มกิจกรรมใหม่
    </button>
</div>

<!-- Data Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ชื่อพนักงาน</th>
                <th>กิจกรรมที่ทำ</th>
                  <th>วันที่</th>
                  <th>เพิ่มเติม</th>
                <th></th>
            </tr>
        </thead>
        <tbody>

            <!-- Row 1 -->
            <tr>
                <td>
                    <a href="#" class="customer-name">User A</a>
                </td>
                <td>
                    <span class="source-tag">วันนี้ติดต่อกับบริษัท </span>
                </td>
      
                <td>
                    <span class="badge badge--existing">20 มี.ค.</span>
                </td>
                     <td>
                    <span class="more">  <button class="btn-sm">เเก้ไข</button>
                                <button class="btn-sm btn-primary-sm">ลบ</button></span>
                </td>
          
                 
          
            </tr>

            <!-- Row 2 -->
            <tr>
                <td>
                    <a href="#" class="customer-name">
User A</a>
                </td>
                <td>
                    <span class="source-tag">บริษัท ไทยอีเวนต์ โซลูชั่น จำกัด</span>
                </td>

                <td>
                    <span class="badge badge--new">	20 มี.ค.</span>
                     <td>
                    <span class="more">  <button class="btn-sm">เเก้ไข</button>
                                <button class="btn-sm btn-primary-sm">ลบ</button></span>
                </td>

            </tr>

   

         

        </tbody>
    </table>
</div>

<!-- Modal เพิ่มกิจกรรมใหม่ -->
<div class="activity-modal" id="activityModal">
    <div class="activity-modal__overlay" onclick="closeActivityModal()"></div>
    <div class="activity-modal__panel">
        <div class="activity-modal__header">
            <h3>เพิ่มกิจกรรม KPI</h3>
            <button class="activity-modal__close" onclick="closeActivityModal()">✕</button>
        </div>

        <div class="activity-modal__body">
            <form class="activity-form">
                <div class="form-group">
                    <label class="form-label">ชื่อพนักงาน <span class="required">*</span></label>
                    <select class="form-select" required>
                        <option value="">-- เลือกพนักงาน --</option>
                        <option value="userA">User A</option>
                        <option value="userB">User B</option>
                        <option value="userC">User C</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">กิจกรรมที่ทำ <span class="required">*</span></label>
                    <textarea class="form-textarea" rows="4" placeholder="ระบุรายละเอียดกิจกรรมที่ทำ..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">วันที่ <span class="required">*</span></label>
                    <input type="date" class="form-input" required />
                </div>
            </form>
        </div>

        <div class="activity-modal__footer">
            <button class="btn-cancel" onclick="closeActivityModal()">ยกเลิก</button>
            <button class="btn-submit">บันทึกกิจกรรม</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // เปิด Modal
    function openActivityModal() {
        const modal = document.getElementById('activityModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // ป้องกันการ scroll พื้นหลัง
    }

    // ปิด Modal
    function closeActivityModal() {
        const modal = document.getElementById('activityModal');
        modal.classList.remove('active');
        document.body.style.overflow = ''; // คืนค่า scroll
    }

    // ปิด Modal เมื่อกด Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeActivityModal();
        }
    });

    // จัดการ Submit Form (ตัวอย่าง)
    document.querySelector('.btn-submit')?.addEventListener('click', function(e) {
        e.preventDefault();

        // TODO: เพิ่มการ validate และส่งข้อมูลไป backend
        alert('บันทึกกิจกรรมสำเร็จ! (ตัวอย่าง)');
        closeActivityModal();

        // Reset form
        document.querySelector('.activity-form').reset();
    });
</script>
@endsection
