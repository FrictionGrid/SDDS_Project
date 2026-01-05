@extends('layout')

@section('title', 'SDDS | จัดการเอกสาร')

@section('page-title', 'จัดการเอกสาร')

@section('breadcrumb', 'Dashboard / Document')

@section('styles')
    <style>
        /* ===== Document Page Styles ===== */

        /* Content Wrapper */
        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 32px;
        }

        .page-header__title {
            font-size: 26px;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 8px 0;
        }

        .page-header__description {
            font-size: 14px;
            color: var(--muted);
            margin: 0;
        }

        /* Document Upload Section */
        .document-upload {
            margin-bottom: 28px;
        }

        .upload-box {
            background: var(--panel);
            border: 2px dashed var(--border);
            border-radius: var(--radius-lg);
            padding: 48px 32px;
            text-align: center;
            transition: all .25s ease;
        }

        .upload-box:hover {
            border-color: var(--primary);
            background: linear-gradient(135deg, var(--primary-soft) 0%, var(--panel) 100%);
        }

        .upload-box__title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 8px 0;
        }

        .upload-box__description {
            font-size: 13px;
            color: var(--muted);
            margin: 0 0 28px 0;
        }

        .upload-box__input-wrapper {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .upload-box__label {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all .25s ease;
            font-size: 14px;
            font-weight: 500;
            color: var(--text);
        }

        .upload-box__label:hover {
            background: var(--primary-soft);
            border-color: var(--primary);
            color: var(--primary);
        }

        .upload-box__label svg {
            color: var(--muted);
            transition: color .25s ease;
        }

        .upload-box__label:hover svg {
            color: var(--primary);
        }

        .upload-box__input {
            display: none;
        }

        .upload-box__filename {
            font-size: 13px;
            color: var(--muted);
            font-style: italic;
        }

        .btn-upload {
            background: linear-gradient(135deg, var(--primary) 0%, #2563eb 100%);
            color: #ffffff;
            border: none;
            padding: 12px 36px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all .25s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 64, 175, .25);
        }

        .btn-upload:active {
            transform: translateY(0);
        }

        /* Documents Section */
        .documents-section {
            background: var(--panel);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-sm);
        }

        .documents-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--border);
        }

        .documents-header__title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin: 0;
        }

        .documents-header__count {
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            background: var(--bg);
            padding: 6px 14px;
            border-radius: 20px;
        }

        /* Documents List */
        .documents-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .document-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            transition: all .25s ease;
        }

        .document-item:hover {
            background: var(--primary-soft);
            border-color: #c7d2fe;
            box-shadow: var(--shadow-sm);
            transform: translateY(-1px);
        }

        .document-item__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--primary) 0%, #2563eb 100%);
            border-radius: var(--radius-md);
            flex-shrink: 0;
            box-shadow: var(--shadow-sm);
        }

        .document-item__icon svg {
            color: #ffffff;
        }

        .document-item__content {
            flex: 1;
            min-width: 0;
        }

        .document-item__name {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            word-break: break-word;
        }

        .document-item__details {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
        }

        .document-item__detail {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--muted);
        }

        .document-item__detail svg {
            color: var(--muted);
            opacity: 0.7;
        }

        .document-item__delete {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .25s ease;
            flex-shrink: 0;
        }

        .document-item__delete:hover {
            background: #fee2e2;
            border-color: #fecaca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, .15);
        }

        .document-item__delete:active {
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .upload-box {
                padding: 32px 20px;
            }

            .document-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .document-item__delete {
                width: 100%;
                text-align: center;
            }

            .documents-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .page-header__title {
                font-size: 22px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Upload Section -->
    <div class="document-upload">
        <div class="upload-box">
            <h3 class="upload-box__title">อัปโหลดเอกสาร</h3>
            <p class="upload-box__description">รองรับไฟล์ TXT และ PDF (สูงสุด 10 MB)</p>

            <div class="upload-box__input-wrapper">
                <label for="fileInput" class="upload-box__label">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <span class="upload-box__label-text">เลือกไฟล์</span>
                </label>
                <input type="file" id="fileInput" accept=".txt,.pdf" class="upload-box__input" />
                <span class="upload-box__filename" id="fileName">ไม่ได้เลือกไฟล์</span>
            </div>

            <button class="btn-upload">อัปโหลด</button>
        </div>
    </div>

    <!-- Documents List Section -->
    <div class="documents-section">
        <div class="documents-header">
            <h2 class="documents-header__title">เอกสารที่อัปโหลดแล้ว</h2>
            <span class="documents-header__count">2 ไฟล์</span>
        </div>

        <div class="documents-list">
            <!-- Document Item 1 -->
            <div class="document-item">
                <div class="document-item__icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div class="document-item__content">
                    <div class="document-item__name">ข้อมูลบริษัท.txt</div>
                    <div class="document-item__details">
                        <span class="document-item__detail">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            24 chunks
                        </span>
                        <span class="document-item__detail">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                <polyline points="13 2 13 9 20 9"></polyline>
                            </svg>
                            2.4 MB
                        </span>
                        <span class="document-item__detail">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            28 ธ.ค. 2567 เวลา 14:35 น.
                        </span>
                    </div>
                </div>
                <button class="document-item__delete">ลบ</button>
            </div>

            <!-- Document Item 2 -->
            <div class="document-item">
                <div class="document-item__icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div class="document-item__content">
                    <div class="document-item__name">resume.pdf</div>
                    <div class="document-item__details">
                        <span class="document-item__detail">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            12 chunks
                        </span>
                        <span class="document-item__detail">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                <polyline points="13 2 13 9 20 9"></polyline>
                            </svg>
                            1.8 MB
                        </span>
                        <span class="document-item__detail">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            27 ธ.ค. 2567 เวลา 09:20 น.
                        </span>
                    </div>
                </div>
                <button class="document-item__delete">ลบ</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // File input - Show selected filename
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('fileInput');
            const fileNameDisplay = document.getElementById('fileName');

            if (fileInput && fileNameDisplay) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        fileNameDisplay.textContent = this.files[0].name;
                        fileNameDisplay.style.fontStyle = 'normal';
                        fileNameDisplay.style.color = 'var(--primary)';
                        fileNameDisplay.style.fontWeight = '500';
                    } else {
                        fileNameDisplay.textContent = 'ไม่ได้เลือกไฟล์';
                        fileNameDisplay.style.fontStyle = 'italic';
                        fileNameDisplay.style.color = 'var(--muted)';
                        fileNameDisplay.style.fontWeight = '400';
                    }
                });
            }
        });
    </script>
@endsection
