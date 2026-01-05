@extends('layout')

@section('title', 'SDDS | รายชื่อลูกค้าหลัก')

@section('page-title', 'รายชื่อลูกค้าหลัก')

@section('breadcrumb', 'Dashboard / Customers')

@section('content')
    <!-- Toolbar -->
    <div class="toolbar">
        <input type="text" class="search-input" id="searchInput"
            placeholder="ค้นหาชื่อลูกค้า, อีเมล, หรือประเภทธุรกิจ..." />

        <button class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <line x1="4" y1="6" x2="20" y2="6"></line>
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <line x1="4" y1="18" x2="14" y2="18"></line>
            </svg>
            ตัวกรอง
        </button>

        <div class="dropdown">
            <select id="statusFilter" onchange="applyFilters()">
                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>สถานะลูกค้า (ทั้งหมด)</option>
                <option value="สนใจ" {{ request('status') == 'สนใจ' ? 'selected' : '' }}>สนใจ</option>
                <option value="ยังไม่ดำเนินการ" {{ request('status') == 'ยังไม่ดำเนินการ' ? 'selected' : '' }}>ยังไม่ดำเนินการ</option>
                <option value="ปิดการขาย" {{ request('status') == 'ปิดการขาย' ? 'selected' : '' }}>ปิดการขาย</option>
            </select>

        </div>

        <div class="dropdown">
            <select id="customerTypeFilter" onchange="applyFilters()">
                <option value="all" {{ request('type') == 'all' || !request('type') ? 'selected' : '' }}>ประเภทลูกค้า (ทั้งหมด)</option>
                <option value="ลูกค้าเก่า" {{ request('type') == 'ลูกค้าเก่า' ? 'selected' : '' }}>ลูกค้าเก่า</option>
                <option value="ลูกค้าใหม่" {{ request('type') == 'ลูกค้าใหม่' ? 'selected' : '' }}>ลูกค้าใหม่</option>
            </select>
        </div>


        <button class="btn btn-primary" id="btnAddCustomer">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            เพิ่มลูกค้าใหม่
        </button>
    </div>

    <!-- Data Table -->
    <div class="table-container">
        <table class="data-table" id="customerTable">
            <thead>
                <tr>
                    <th>ชื่อลูกค้า</th>
                    <th>แหล่งที่มา</th>
                    <th>สถานะล่าสุด</th>
                    <th>ประเภทลูกค้า</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>
                        <a href="{{ route('customers.show', $customer['id'] ?? 0) }}" class="customer-name">{{ $customer['name'] ?? '-' }}</a>
                    </td>
                    <td>
                        <span class="source-tag">
                            @php
                                $source = $customer['source'] ?? '';
                                $sourceIcons = [
                                    'LINE OA' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>',
                                    'อีเมล' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline>',
                                    'แบบฟอร์ม' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14,2 14,8 20,8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10,9 9,9 8,9"></polyline>',
                                    'Excel' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9,22 9,12 15,12 15,22"></polyline>',
                                ];
                                $icon = $sourceIcons[$source] ?? '<circle cx="12" cy="12" r="10"></circle>';
                            @endphp
                
                            {{ $source }}
                        </span>
                    </td>
                    <td>
                        @php
                            $status = $customer['latest_status'] ?? '';
                            $statusClass = match($status) {
                                'สนใจ' => 'badge--interested',
                                'ยังไม่ดำเนินการ' => 'badge--pending',
                                'ปิดการขาย' => 'badge--closed',
                                default => 'badge--pending'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $status }}</span>
                    </td>
                    <td>
                        @php
                            $type = $customer['customer_type'] ?? '';
                            $typeClass = match($type) {
                                'ลูกค้าเก่า' => 'badge--existing',
                                'ลูกค้าใหม่' => 'badge--new',
                                default => 'badge--new'
                            };
                        @endphp
                        <span class="badge {{ $typeClass }}">{{ $type }}</span>
                    </td>
                    <td>
                        <span class="customer-email">{{ $customer['email'] ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="customer-phone">0{{ $customer['phone'] ?? '-' }}</span>
                    </td>
                    <td>
                        <div class="action-menu">
                            <button class="action-menu__trigger" onclick="alert('เมนูเพิ่มเติม')">⋯</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem;">
                        ไม่พบข้อมูลลูกค้า
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Filter Modal -->
    <div class="filter-modal" id="filterModal">
        <div class="filter-modal__overlay"></div>
        <div class="filter-modal__panel">
            <div class="filter-modal__header">
                <h3>ตัวกรองขั้นสูง</h3>
                <button class="filter-modal__close">✕</button>
            </div>

            <div class="filter-modal__body">
                <!-- ประเภทธุรกิจ -->
                <div class="filter-section">
                    <h4 class="filter-section__title">ประเภทธุรกิจ</h4>
                    <div class="filter-options">
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>อุตสาหกรรมการผลิต</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>ค้าปลีก/ค้าส่ง</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>บริการ</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>เทคโนโลยี/IT</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>อสังหาริมทรัพย์</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>การศึกษา</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>สุขภาพและความงาม</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>อาหารและเครื่องดื่ม</span>
                        </label>
                    </div>
                </div>

                <!-- ระดับความสำคัญ -->
                <div class="filter-section">
                    <h4 class="filter-section__title">ระดับความสำคัญ</h4>
                    <div class="filter-options">
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>สูง</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>ปานกลาง</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>ต่ำ</span>
                        </label>
                    </div>
                </div>

                <!-- แหล่งที่มา -->
                <div class="filter-section">
                    <h4 class="filter-section__title">แหล่งที่มา</h4>
                    <div class="filter-options">
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>LINE OA</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>อีเมล</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>แบบฟอร์ม</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox">
                            <span>Excel</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="filter-modal__footer">
                <button class="btn-filter-clear">ล้างตัวกรอง</button>
                <button class="btn-filter-apply">ใช้งาน</button>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="add-customer-modal" id="addCustomerModal">
        <div class="add-customer-modal__overlay"></div>
        <div class="add-customer-modal__panel">
            <div class="add-customer-modal__header">
                <h3>เพิ่มลูกค้าใหม่</h3>
                <button class="add-customer-modal__close">✕</button>
            </div>

            <div class="add-customer-modal__body">
                <form class="customer-form">
                    <!-- ชื่อลูกค้า -->
                    <div class="form-group">
                        <label class="form-label">ชื่อลูกค้า <span class="required">*</span></label>
                        <input type="text" class="form-input" placeholder="กรอกชื่อ-นามสกุล" required>
                    </div>

                    <!-- อีเมล -->
                    <div class="form-group">
                        <label class="form-label">อีเมล <span class="required">*</span></label>
                        <input type="email" class="form-input" placeholder="example@email.com" required>
                    </div>

                    <!-- เบอร์โทรศัพท์ -->
                    <div class="form-group">
                        <label class="form-label">เบอร์โทรศัพท์ <span class="required">*</span></label>
                        <input type="tel" class="form-input" placeholder="0XX-XXX-XXXX" required>
                    </div>

                    <!-- แหล่งที่มา -->
                    <div class="form-group">
                        <label class="form-label">แหล่งที่มา</label>
                        <select class="form-select">
                            <option value="">เลือกแหล่งที่มา</option>
                            <option value="line">LINE OA</option>
                            <option value="email">อีเมล</option>
                            <option value="form">แบบฟอร์ม</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>

                    <!-- สถานะ -->
                    <div class="form-group">
                        <label class="form-label">สถานะ</label>
                        <select class="form-select">
                            <option value="">เลือกสถานะ</option>
                            <option value="interested">สนใจ</option>
                            <option value="pending">ยังไม่ดำเนินการ</option>
                            <option value="closed">ปิดการขาย</option>
                        </select>
                    </div>

                    <!-- ประเภทลูกค้า -->
                    <div class="form-group">
                        <label class="form-label">ประเภทลูกค้า</label>
                        <select class="form-select">
                            <option value="">เลือกประเภทลูกค้า</option>
                            <option value="new">ลูกค้าใหม่</option>
                            <option value="existing">ลูกค้าเก่า</option>
                        </select>
                    </div>

                    <!-- หมายเหตุ -->
                    <div class="form-group">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-textarea" rows="3" placeholder="รายละเอียดเพิ่มเติม..."></textarea>
                    </div>
                </form>
            </div>

            <div class="add-customer-modal__footer">
                <button class="btn-cancel">ยกเลิก</button>
                <button class="btn-submit">บันทึก</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/customers.js') }}"></script>
@endsection
