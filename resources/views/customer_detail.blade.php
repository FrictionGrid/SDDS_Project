@extends('layout')

@section('title', 'SDDS | รายชื่อลูกค้าหลัก')

@section('page-title', 'รายละเอียดลูกค้า')

@section('breadcrumb', ' Customer Details    ')


@section('content')

    <!-- Success/Error Messages -->
    @if(session('success'))
      <div style="background: #d1fae5; color: #059669; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
        {{ session('error') }}
      </div>
    @endif

    <!-- Main Content -->
    <main class="main main--full">
      <div class="content-wrapper">

        <!-- Customer Header -->
        <div class="customer-header">
          <div class="customer-header__top">
            <div class="customer-header__info">
              <div class="customer-header__name">{{ $customer['name'] ?? '-' }}</div>
              <div class="customer-header__tags">
                @php
                    $type = $customer['customer_type'] ?? '';
                    $typeClass = match($type) {
                        'ลูกค้าเก่า' => 'customer-tag--current',
                        'ลูกค้าใหม่' => 'customer-tag--new',
                        default => 'customer-tag--current'
                    };

                    $status = $customer['latest_status'] ?? '';
                    $statusClass = match($status) {
                        'สนใจ' => 'customer-tag--interested',
                        'ยังไม่ดำเนินการ' => 'customer-tag--pending',
                        'ปิดการขาย' => 'customer-tag--closed',
                        default => 'customer-tag--pending'
                    };
                @endphp
                <div class="customer-tag {{ $typeClass }}">{{ $type }}</div>
                <div class="customer-tag {{ $statusClass }}">{{ $status }}</div>
              </div>
              <div class="customer-header__contact">
                <div class="customer-header__contact-item">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                  </svg>
                  {{ $customer['email'] ?? '-' }}
                </div>
                <div class="customer-header__contact-item">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path
                      d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                    </path>
                  </svg>
                  0{{ $customer['phone'] ?? '-' }}
                </div>
              </div>
            </div>

            <div class="customer-header__actions">
              <button class="btn-action btn-action--secondary" id="btnEditProfile">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                แก้ไขโปรไฟล์
              </button>
              <button class="btn-action btn-action--secondary" id="btnSendEmail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                  <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                ส่งอีเมล
              </button>
            </div>
          </div>
        </div>

        <!-- Customer Detail Layout -->
        <div class="customer-detail-layout">

          <!-- Sidebar Column -->
          <div class="customer-sidebar">

            <!-- AI Insights Card -->
            <div class="info-card ai-insights">
              <div class="info-card__header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                  <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
                </svg>
                ข้อมูลเชิงวิเคราะห์ด้วย AI
              </div>
              <div class="info-card__body">

                <!-- ระดับความสำคัญลูกค้า -->
                <div class="ai-metric">
                  <div class="ai-metric__label">ระดับความสำคัญลูกค้า</div>
                  <div class="ai-metric__badge ai-metric__badge--high">ยังไม่เปิดให้บริการ</div>
                </div>

                <!-- มูลค่าที่คาดการณ์ได้ -->
                <div class="ai-metric">
                  <div class="ai-metric__label">มูลค่าที่คาดการณ์ได้</div>
                  <div class="ai-metric__value">ยังไม่เปิดให้บริการ</div>
                </div>

                <!-- สถานะโอกาสขาย -->
                <div class="ai-metric">
                  <div class="ai-metric__label">สถานะโอกาสขาย</div>
                  <div class="ai-metric__badge ai-metric__badge--success">ยังไม่เปิดให้บริการ</div>
                </div>

                <!-- AI Insight -->
                <div class="ai-insight-text">
                  💡 <strong>AI Insight:</strong> ยังไม่เปิดให้บริการ
                </div>

              </div>
            </div>

            <!-- Contact History -->
            <div class="contact-history">
              <div class="contact-history__header">
                <span>ประวัติการติดต่อ</span>
                <button class="btn btn-primary" id="btnAddContact">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                  </svg>
                  เพิ่มการติดต่อ
                </button>
              </div>

              <div class="contact-history__list">

                @forelse($contactHistories as $history)
                <div class="contact-item">
                  @php
                    $iconMap = [
                      'call' => '📞',
                      'email' => '📧',
                      'meeting' => '📅',
                      'line' => '💬',
                      'other' => '📝'
                    ];
                    $icon = $iconMap[$history->contact_type] ?? '📝';

                    $typeClass = 'contact-item__icon--' . ($history->contact_type ?? 'other');
                  @endphp
                  <div class="contact-item__icon {{ $typeClass }}">{{ $icon }}</div>
                  <div class="contact-item__content">
                    <div class="contact-item__type">{{ $history->subject ?? $history->contact_type ?? '-' }}</div>
                    <div class="contact-item__description">
                      {{ $history->description ?? 'ไม่มีรายละเอียด' }}
                    </div>
                    <div class="contact-item__meta">
                      <div class="contact-item__by">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                          <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        {{ $history->contacted_by ?? 'ไม่ระบุ' }}
                      </div>
                      <div class="contact-item__date">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <circle cx="12" cy="12" r="10"></circle>
                          <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        {{ $history->contacted_at ? $history->contacted_at->format('d/m/Y H:i') : '-' }}
                      </div>
                    </div>
                  </div>
                  <div class="contact-item__actions">
                    <div class="action-menu">
                      <button class="action-menu__trigger" type="button"
                              onclick="openActionModal({{ $history->id }}, '{{ $history->contact_type }}', '{{ $history->subject }}', '{{ addslashes($history->description ?? '') }}', '{{ $history->contacted_at ? $history->contacted_at->format('Y-m-d') : '' }}', '{{ $history->contacted_at ? $history->contacted_at->format('H:i') : '' }}', '{{ $history->contacted_by }}', '{{ $history->status }}')">
                        ⋯
                      </button>
                    </div>
                  </div>
                </div>
                @empty
                <div style="text-align: center; padding: 2rem; color: #999;">
                  ยังไม่มีประวัติการติดต่อ
                </div>
                @endforelse

              </div>
            </div>

              <!-- Documents -->
               <div class="contact-history">
          <div class="card">
            <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
              <h2 class="card-title">เอกสารที่เกี่ยวข้อง</h2>
              <button class="btn btn-primary" id="btnUploadDocument">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                อัพโหลด
              </button>
            </div>

            <!-- Hidden Form for Upload -->
            <form id="uploadDocumentForm" action="{{ route('documents.upload') }}" method="POST" enctype="multipart/form-data" style="display: none;">
              @csrf
              <input type="hidden" name="customer_id" value="{{ $customer['id'] }}">
              <input type="file" name="files[]" id="fileInput" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
            </form>
            <div class="documents-list">
              @forelse($documents as $doc)
              <div class="document-item">
                <div class="document-info">
                  <div class="document-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                  </div>
                  <div class="document-details">
                    <div class="document-name">{{ $doc->file_name }}</div>
                    <div class="document-meta">อัพเดท {{ $doc->created_at->format('d/m/Y') }} • {{ $doc->file_size_human }}</div>
                  </div>
                </div>
                <div class="document-actions" style="display: flex; gap: 0.5rem;">
                  <a href="{{ route('documents.download', $doc->id) }}" class="document-action">ดาวน์โหลด</a>
                  <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('ยืนยันการลบเอกสาร?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="document-action document-action--delete" style="background: none; border: none; color: #ef4444; cursor: pointer;">ลบ</button>
                  </form>
                </div>
              </div>
              @empty
              <div style="text-align: center; padding: 2rem; color: #999;">
                ยังไม่มีเอกสาร
              </div>
              @endforelse
            </div>
          </div>
          </div>

          </div>

          <!-- Main Column -->
          <div class="customer-main">

            <!-- Customer Info Card -->
            <div class="info-card">
              <div class="info-card__header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                ข้อมูลลูกค้าหลัก
              </div>
              <div class="info-card__body">
                <div class="info-list-grid">

                  
                   <!--ประเภทธุรกิจ -->
                  <div class="info-item">
                    <div class="info-item__label">ประเภทธุรกิจ</div>
                    <div class="info-item__value">{{ $customer['Business_type'] ?? '-' }}</div>
                  </div>

                  <!-- ประเภทลูกค้า -->
                  <div class="info-item">
                    <div class="info-item__label">ประเภทลูกค้า</div>
                    <div class="info-item__value">{{ $customer['customer_type'] ?? '-' }}</div>
                  </div>

                       <!-- แหล่งที่มา -->
                  <div class="info-item">
                    <div class="info-item__label">แหล่งที่มา</div>
                    <div class="info-item__value">{{ $customer['source'] ?? '-' }}</div>
                  </div>

                  <!-- สถานะล่าสุด -->
                  <div class="info-item">
                    <div class="info-item__label">สถานะล่าสุด</div>
                    <div class="info-item__value">{{ $customer['latest_status'] ?? '-' }}</div>
                  </div>

                  <!-- อีเมล -->
                  <div class="info-item info-item--full">
                    <div class="info-item__label">อีเมล</div>
                    <div class="info-item__value">{{ $customer['email'] ?? '-' }}</div>
                  </div>

                  <!-- เบอร์โทรศัพท์ -->
                  <div class="info-item">
                    <div class="info-item__label">เบอร์โทรศัพท์</div>
                    <div class="info-item__value">{{ $customer['phone'] ? '0' . $customer['phone'] : '-' }}</div>
                  </div>

                  <!-- เบอร์โทรสำรอง -->
                  <div class="info-item">
                    <div class="info-item__label">เบอร์โทรสำรอง</div>
                    <div class="info-item__value">{{ $customer['phone_backup'] ? '0' . $customer['phone_backup'] : '-' }}</div>
                  </div>

                  <!-- ที่อยู่บริษัท -->
                  <div class="info-item info-item--full">
                    <div class="info-item__label">ที่อยู่บริษัท</div>
                    <div class="info-item__value info-item__value--muted">
                      {{ $customer['company_address'] ?? '-' }}
                    </div>
                  </div>

                  <!-- วันที่เริ่มเป็นลูกค้า -->
                  <div class="info-item">
                    <div class="info-item__label">วันที่เริ่มเป็นลูกค้า</div>
                    <div class="info-item__value">
                      @if(!empty($customer['start_date']))
                        {{ date('d/m/Y', strtotime($customer['start_date'])) }}
                      @else
                        -
                      @endif
                    </div>
                  </div>

                  <!-- มูลค่าโครงการรวม -->
                  <div class="info-item">
                    <div class="info-item__label">มูลค่าโครงการรวม</div>
                    <div class="info-item__value">{{ $customer['project_value'] ?? '-' }}</div>
                  </div>

                  <!-- ผู้ติดต่อหลัก -->
                  <div class="info-item">
                    <div class="info-item__label">ผู้ติดต่อหลัก</div>
                    <div class="info-item__value">{{ $customer['main_contact'] ?? '-' }}</div>
                  </div>

                  <!-- หมายเหตุ -->
                  <div class="info-item info-item--full">
                    <div class="info-item__label">หมายเหตุ</div>
                    <div class="info-item__value info-item__value--muted">
                      {{ $customer['note'] ?? '-' }}
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- Related Projects -->
            <div class="projects-section">
              <div class="projects-section__header">
                <span>โปรเจกต์ที่เกี่ยวข้อง(ยังไม่เปิดให้บริการ)</span>
                <button class="btn btn-primary" id="btnAddProject">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                  </svg>
                  เพิ่มโปรเจกต์
                </button>
              </div>

              <div class="projects-grid">

                <!-- Project 1 -->
                <div class="project-card-detail">
                  <div class="project-card-detail__header">
                    <div>
                      <div class="project-card-detail__title">Project1</div>
                      <div class="project-card-detail__type">
                          <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                          </path>
                        </svg>
                        Project1
                      </div>
                    </div>
                    <div class="project-card-detail__status">
                      <div class="status-badge status-badge--active">กำลังดำเนินการ</div>
                    </div>
                  </div>
                  <div class="project-card-detail__progress">
                    <div class="project-card-detail__progress-label">
                      <span>ความคืบหน้า</span>
                      <span>78%</span>
                    </div>
                    <div class="project-card-detail__progress-bar">
                      <div class="project-card-detail__progress-fill" style="width: 78%;"></div>
                    </div>
                  </div>
                  <div class="project-card-detail__dates">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    1 ม.ค. 2567 – 30 มิ.ย. 2567
                  </div>
                </div>

                <!-- Project 2 -->
                <div class="project-card-detail">
                  <div class="project-card-detail__header">
                    <div>
                      <div class="project-card-detail__title">Project2</div>
                      <div class="project-card-detail__type">
                       Project2
                      </div>
                    </div>
                    <div class="project-card-detail__status">
                      <div class="status-badge status-badge--completed">เสร็จสิ้น</div>
                    </div>
                  </div>
                  <div class="project-card-detail__progress">
                    <div class="project-card-detail__progress-label">
                      <span>ความคืบหน้า</span>
                      <span>100%</span>
                    </div>
                    <div class="project-card-detail__progress-bar">
                      <div class="project-card-detail__progress-fill project-card-detail__progress-fill--success"
                        style="width: 100%;"></div>
                    </div>
                  </div>
                  <div class="project-card-detail__dates">
                    15 ก.พ. 2567 – 30 เม.ย. 2567
                  </div>
                </div>

                <!-- Project 3 -->
                <div class="project-card-detail">
                  <div class="project-card-detail__header">
                    <div>
                      <div class="project-card-detail__title">Project3</div>
                      <div class="project-card-detail__type">

                      Project3
                      </div>
                    </div>
                    <div class="project-card-detail__status">
                      <div class="status-badge status-badge--active">กำลังดำเนินการ</div>
                    </div>
                  </div>
                  <div class="project-card-detail__progress">
                    <div class="project-card-detail__progress-label">
                      <span>ความคืบหน้า</span>
                      <span>60%</span>
                    </div>
                    <div class="project-card-detail__progress-bar">
                      <div class="project-card-detail__progress-fill project-card-detail__progress-fill--warning"
                        style="width: 60%;"></div>
                    </div>
                  </div>
                  <div class="project-card-detail__dates">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    1 พ.ค. 2567 – 31 ก.ค. 2567
                  </div>
                </div>

              </div>
            </div>

          </div>

        </div>

      </div>
    </main>

  </div>

  <!-- Add Contact History Modal -->
  <div class="add-customer-modal" id="addContactModal">
    <div class="add-customer-modal__overlay"></div>
    <div class="add-customer-modal__panel">
      <div class="add-customer-modal__header">
        <h3>เพิ่มประวัติการติดต่อ</h3>
        <button class="add-customer-modal__close">✕</button>
      </div>

      <div class="add-customer-modal__body">
        <form action="{{ route('contacts.store') }}" method="POST" class="customer-form">
          @csrf
          <input type="hidden" name="customer_id" value="{{ $customer['id'] }}">

          <!-- ประเภทการติดต่อ -->
          <div class="form-group">
            <label class="form-label">ประเภทการติดต่อ</label>
            <select name="contact_type" class="form-select">
              <option value="">เลือกประเภท</option>
              <option value="call">โทรศัพท์</option>
              <option value="email">อีเมล</option>
              <option value="meeting">ประชุม</option>
              <option value="line">LINE</option>
              <option value="other">อื่นๆ</option>
            </select>
          </div>

          <!-- หัวข้อ -->
          <div class="form-group">
            <label class="form-label">หัวข้อ</label>
            <input type="text" name="subject" class="form-input" placeholder="หัวข้อการติดต่อ">
          </div>

          <!-- รายละเอียด -->
          <div class="form-group">
            <label class="form-label">รายละเอียด</label>
            <textarea name="description" class="form-textarea" rows="4" placeholder="รายละเอียดการติดต่อ"></textarea>
          </div>

          <!-- วันที่ติดต่อ -->
          <div class="form-group">
            <label class="form-label">วันที่ติดต่อ</label>
            <input type="date" name="contact_date" class="form-input">
          </div>

          <!-- เวลาที่ติดต่อ -->
          <div class="form-group">
            <label class="form-label">เวลาที่ติดต่อ</label>
            <input type="time" name="contact_time" class="form-input">
          </div>

          <!-- ผู้ติดต่อ -->
          <div class="form-group">
            <label class="form-label">ผู้ติดต่อ</label>
            <input type="text" name="contacted_by" class="form-input" placeholder="ชื่อผู้ติดต่อ">
          </div>

          <!-- สถานะ -->
          <div class="form-group">
            <label class="form-label">สถานะ</label>
            <select name="status" class="form-select">
              <option value="">เลือกสถานะ</option>
              <option value="completed">เสร็จสิ้น</option>
              <option value="pending">รอดำเนินการ</option>
              <option value="follow_up">ติดตามผล</option>
            </select>
          </div>

          <div class="add-customer-modal__footer">
            <button type="button" class="btn-cancel">ยกเลิก</button>
            <button type="submit" class="btn-submit">บันทึก</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Contact History Modal -->
  <div class="add-customer-modal" id="editContactModal">
    <div class="add-customer-modal__overlay"></div>
    <div class="add-customer-modal__panel">
      <div class="add-customer-modal__header">
        <h3>แก้ไขประวัติการติดต่อ</h3>
        <button class="add-customer-modal__close">✕</button>
      </div>

      <div class="add-customer-modal__body">
        <form id="editContactForm" method="POST" class="customer-form">
          @csrf
          @method('PUT')
          <input type="hidden" name="customer_id" value="{{ $customer['id'] }}">

          <!-- ประเภทการติดต่อ -->
          <div class="form-group">
            <label class="form-label">ประเภทการติดต่อ</label>
            <select name="contact_type" id="edit_contact_type" class="form-select">
              <option value="">เลือกประเภท</option>
              <option value="call">โทรศัพท์</option>
              <option value="email">อีเมล</option>
              <option value="meeting">ประชุม</option>
              <option value="line">LINE</option>
              <option value="other">อื่นๆ</option>
            </select>
          </div>

          <!-- หัวข้อ -->
          <div class="form-group">
            <label class="form-label">หัวข้อ</label>
            <input type="text" name="subject" id="edit_subject" class="form-input" placeholder="หัวข้อการติดต่อ">
          </div>

          <!-- รายละเอียด -->
          <div class="form-group">
            <label class="form-label">รายละเอียด</label>
            <textarea name="description" id="edit_description" class="form-textarea" rows="4" placeholder="รายละเอียดการติดต่อ"></textarea>
          </div>

          <!-- วันที่ติดต่อ -->
          <div class="form-group">
            <label class="form-label">วันที่ติดต่อ</label>
            <input type="date" name="contact_date" id="edit_contact_date" class="form-input">
          </div>

          <!-- เวลาที่ติดต่อ -->
          <div class="form-group">
            <label class="form-label">เวลาที่ติดต่อ</label>
            <input type="time" name="contact_time" id="edit_contact_time" class="form-input">
          </div>

          <!-- ผู้ติดต่อ -->
          <div class="form-group">
            <label class="form-label">ผู้ติดต่อ</label>
            <input type="text" name="contacted_by" id="edit_contacted_by" class="form-input" placeholder="ชื่อผู้ติดต่อ">
          </div>

          <!-- สถานะ -->
          <div class="form-group">
            <label class="form-label">สถานะ</label>
            <select name="status" id="edit_status" class="form-select">
              <option value="">เลือกสถานะ</option>
              <option value="completed">เสร็จสิ้น</option>
              <option value="pending">รอดำเนินการ</option>
              <option value="follow_up">ติดตามผล</option>
            </select>
          </div>

          <div class="add-customer-modal__footer">
            <button type="button" class="btn-cancel">ยกเลิก</button>
            <button type="submit" class="btn-submit">บันทึกการแก้ไข</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Add Project Modal -->
  <div class="add-customer-modal" id="addProjectModal">
    <div class="add-customer-modal__overlay"></div>
    <div class="add-customer-modal__panel">
      <div class="add-customer-modal__header">
        <h3>เพิ่มโปรเจกต์</h3>
        <button class="add-customer-modal__close">✕</button>
      </div>

      <div class="add-customer-modal__body">
        <form class="customer-form">
          <!-- ชื่อโปรเจกต์ -->
          <div class="form-group">
            <label class="form-label">ชื่อโปรเจกต์ <span class="required">*</span></label>
            <input type="text" class="form-input" placeholder="กรอกชื่อโปรเจกต์" required>
          </div>

          <!-- ประเภทโปรเจกต์ -->
          <div class="form-group">
            <label class="form-label">ประเภทโปรเจกต์</label>
            <select class="form-select">
              <option value="">เลือกประเภทโปรเจกต์</option>
              <option value="crm">CRM Integration</option>
              <option value="ai">AI Analytics Platform</option>
              <option value="web">Web Development</option>
              <option value="mobile">Mobile Application</option>
              <option value="other">อื่นๆ</option>
            </select>
          </div>

          <!-- สถานะ -->
          <div class="form-group">
            <label class="form-label">สถานะ</label>
            <select class="form-select">
              <option value="">เลือกสถานะ</option>
              <option value="active">กำลังดำเนินการ</option>
              <option value="pending">รอดำเนินการ</option>
              <option value="completed">เสร็จสิ้น</option>
              <option value="onhold">พักไว้ชั่วคราว</option>
            </select>
          </div>

          <!-- วันที่เริ่มต้น -->
          <div class="form-group">
            <label class="form-label">วันที่เริ่มต้น</label>
            <input type="date" class="form-input">
          </div>

          <!-- วันที่สิ้นสุด -->
          <div class="form-group">
            <label class="form-label">วันที่สิ้นสุด (โดยประมาณ)</label>
            <input type="date" class="form-input">
          </div>

          <!-- ความคืบหน้า -->
          <div class="form-group">
            <label class="form-label">ความคืบหน้า (%)</label>
            <input type="number" class="form-input" placeholder="0-100" min="0" max="100">
          </div>

          <!-- รายละเอียด -->
          <div class="form-group">
            <label class="form-label">รายละเอียดโปรเจกต์</label>
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

  <!-- Add Contact Modal -->
  <div class="add-customer-modal" id="addContactModal">
    <div class="add-customer-modal__overlay"></div>
    <div class="add-customer-modal__panel">
      <div class="add-customer-modal__header">
        <h3>เพิ่มการติดต่อ</h3>
        <button class="add-customer-modal__close">✕</button>
      </div>

      <div class="add-customer-modal__body">
        <form class="customer-form">
          <!-- ประเภทการติดต่อ -->
          <div class="form-group">
            <label class="form-label">ประเภทการติดต่อ <span class="required">*</span></label>
            <select class="form-select" required>
              <option value="">เลือกประเภทการติดต่อ</option>
              <option value="meeting">Meeting (ประชุม)</option>
              <option value="email">Email (อีเมล)</option>
              <option value="call">โทรศัพท์</option>
              <option value="line">LINE Official Account</option>
              <option value="other">อื่นๆ</option>
            </select>
          </div>

          <!-- หัวข้อ/เรื่อง -->
          <div class="form-group">
            <label class="form-label">หัวข้อ/เรื่อง <span class="required">*</span></label>
            <input type="text" class="form-input" placeholder="กรอกหัวข้อการติดต่อ" required>
          </div>

          <!-- รายละเอียด -->
          <div class="form-group">
            <label class="form-label">รายละเอียด <span class="required">*</span></label>
            <textarea class="form-textarea" rows="4" placeholder="รายละเอียดการติดต่อ..." required></textarea>
          </div>

          <!-- ผู้ติดต่อ -->
          <div class="form-group">
            <label class="form-label">ผู้ติดต่อ/ผู้รับผิดชอบ</label>
            <input type="text" class="form-input" placeholder="ชื่อผู้ติดต่อ">
          </div>

          <!-- วันที่-เวลา -->
          <div class="form-group">
            <label class="form-label">วันที่-เวลา <span class="required">*</span></label>
            <input type="datetime-local" class="form-input" required>
          </div>

          <!-- หมายเหตุ -->
          <div class="form-group">
            <label class="form-label">หมายเหตุ</label>
            <textarea class="form-textarea" rows="2" placeholder="หมายเหตุเพิ่มเติม..."></textarea>
          </div>
        </form>
      </div>

      <div class="add-customer-modal__footer">
        <button class="btn-cancel">ยกเลิก</button>
        <button class="btn-submit">บันทึก</button>
      </div>
    </div>
  </div>

  <!-- Chatbot -->
  <div class="chatbot-btn" onclick="toggleChat()">💬</div>

  <div class="chatbot-panel" id="chatbot">
    <div class="chatbot-header">
      SDDS AI Assistant
      <button onclick="toggleChat()">✕</button>
    </div>
    <div class="chatbot-body">
      <div class="chatbot-msg bot">สวัสดีครับ มีอะไรให้ช่วยไหม</div>
    </div>
    <div class="chatbot-input">
      <input type="text" placeholder="พิมพ์คำสั่งถึง AI..." />
      <button>ส่ง</button>
    </div>
  </div>

  <!-- External JavaScript -->
  <script src="js/script.js"></script>
  <script src="js/customer_detail.js"></script>
</body>


@endsection

@section('scripts')
    <script src="{{ asset('js/customers.js') }}"></script>
    <script>
      // เปิด Modal เพิ่มประวัติการติดต่อ
      document.getElementById('btnAddContact')?.addEventListener('click', function() {
        document.getElementById('addContactModal').classList.add('active');
      });

      // ปิด Modal
      document.querySelectorAll('.add-customer-modal__close, .btn-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
          this.closest('.add-customer-modal').classList.remove('active');
        });
      });

      // ปิด Modal เมื่อคลิกนอก panel
      document.querySelectorAll('.add-customer-modal__overlay').forEach(overlay => {
        overlay.addEventListener('click', function() {
          this.closest('.add-customer-modal').classList.remove('active');
        });
      });

      // อัพโหลดเอกสาร
      document.getElementById('btnUploadDocument')?.addEventListener('click', function() {
        document.getElementById('fileInput').click();
      });

      document.getElementById('fileInput')?.addEventListener('change', function(e) {
        if (this.files.length > 0) {
          const fileNames = Array.from(this.files).map(f => f.name).join(', ');
          if (confirm(`ต้องการอัพโหลดไฟล์: ${fileNames}?`)) {
            document.getElementById('uploadDocumentForm').submit();
          }
        }
      });

      // ====== Simple Action Modal ======
      let currentContactId = null;
      let currentContactData = {};

      function openActionModal(id, contactType, subject, description, contactDate, contactTime, contactedBy, status) {
        console.log('openActionModal called, id:', id);

        // เก็บข้อมูล
        currentContactId = id;
        currentContactData = {
          id: id,
          contactType: contactType,
          subject: subject,
          description: description,
          contactDate: contactDate,
          contactTime: contactTime,
          contactedBy: contactedBy,
          status: status
        };

        // เปิด modal
        const modal = document.getElementById('actionModal');
        console.log('Modal element:', modal);

        if (modal) {
          modal.classList.add('active');
          console.log('Modal classes:', modal.className);
        } else {
          console.error('Modal #actionModal not found!');
        }
      }

    
     

      // ====== Edit Contact Modal ======
      function openEditContactModal(id, contactType, subject, description, contactDate, contactTime, contactedBy, status) {
        // ตั้งค่า action URL ของฟอร์ม
        const form = document.getElementById('editContactForm');
        form.action = `/contacts/${id}`;

        // กรอกข้อมูลเข้าฟอร์ม
        document.getElementById('edit_contact_type').value = contactType || '';
        document.getElementById('edit_subject').value = subject || '';
        document.getElementById('edit_description').value = description || '';
        document.getElementById('edit_contact_date').value = contactDate || '';
        document.getElementById('edit_contact_time').value = contactTime || '';
        document.getElementById('edit_contacted_by').value = contactedBy || '';
        document.getElementById('edit_status').value = status || '';


        // เปิด modal
        document.getElementById('editContactModal').classList.add('active');
      }
    </script>
@endsection
