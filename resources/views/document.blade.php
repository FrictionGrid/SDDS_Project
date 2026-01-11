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

<div class="content-wrapper">

    {{-- Flash Message --}}
    @if(session('success'))
        <div style="background:#ecfeff;color:#0369a1;padding:14px;border-radius:12px;margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Upload Section -->
    <div class="document-upload">
        <form class="upload-box"
              method="POST"
              action="{{ route('document.upload') }}"
              enctype="multipart/form-data">
            @csrf

            <h3 class="upload-box__title">อัปโหลดเอกสาร</h3>
            <p class="upload-box__description">
                รองรับ PDF, DOCX, TXT, MD, JSON (สูงสุด 50 MB)
            </p>

            <div class="upload-box__input-wrapper">
                <label for="fileInput" class="upload-box__label">
                    📄 เลือกไฟล์
                </label>

                <input 
                    type="file" 
                    id="fileInput" 
                    name="file"
                    accept=".pdf,.docx,.txt,.md,.json"
                    class="upload-box__input" 
                    required />

                <span class="upload-box__filename" id="fileName">ไม่ได้เลือกไฟล์</span>
            </div>

            @error('file')
                <div style="color:red;font-size:13px;margin-bottom:10px;">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn-upload">
                อัปโหลดและให้ AI เรียนรู้
            </button>
        </form>
    </div>

    <!-- Documents List -->
    <div class="documents-section">
        <div class="documents-header">
            <h2 class="documents-header__title">เอกสารที่อัปโหลดแล้ว</h2>
            <span class="documents-header__count">
                {{ count($documents) }} ไฟล์
            </span>
        </div>

        <div class="documents-list">
            @forelse($documents as $doc)
                <div class="document-item">
                    <div class="document-item__icon">📄</div>

                    <div class="document-item__content">
                        <div class="document-item__name">
                            {{ $doc['name'] }}
                        </div>

                        <div class="document-item__details">
                            <span class="document-item__detail">
                                {{ $doc['chunks'] }} chunks
                            </span>
                            <span class="document-item__detail">
                                {{ number_format($doc['size']/1024/1024,2) }} MB
                            </span>
                            <span class="document-item__detail">
                                {{ $doc['created_at']->format('d M Y H:i') }}
                            </span>
                            <span class="document-item__detail">
                                {{ $doc['status'] }}
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('document.destroy', $doc['uuid']) }}">
                        @csrf
                        @method('DELETE')
                        <button class="document-item__delete">ลบ</button>
                    </form>
                </div>
            @empty
                <div style="color:#888;padding:24px;text-align:center;">
                    ยังไม่มีเอกสารในระบบ
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');

    if(fileInput){
        fileInput.addEventListener('change', function(){
            if(this.files.length > 0){
                fileName.textContent = this.files[0].name;
                fileName.style.color = '#2563eb';
                fileName.style.fontStyle = 'normal';
            } else {
                fileName.textContent = 'ไม่ได้เลือกไฟล์';
                fileName.style.color = '#999';
                fileName.style.fontStyle = 'italic';
            }
        });
    }
});
</script>
@endsection