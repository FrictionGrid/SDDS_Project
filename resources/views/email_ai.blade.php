@extends('layout')

@section('title', 'SDDS | Email AI')

@section('page-title', 'Email AI Workspace')

@section('breadcrumb', 'Dashboard / Email AI')

@section('content')
    <!-- 2-Column Layout -->
    <div class="email-ai-container">

        <!-- Left Column: Draft List -->
        <div class="draft-list-container">
            <div class="draft-list-header">
                <div class="draft-list-header__title">Draft Emails</div>
                <div class="draft-list-header__count" id="draftCount">{{ count($drafts) }} drafts</div>
            </div>

            <div class="draft-list" id="draftList">

                @forelse($drafts as $index => $draft)
                <div class="draft-card {{ $index === 0 ? 'active' : '' }}" data-draft-id="{{ $draft['id'] }}">
                    <div class="draft-card__to">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path
                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                            </path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        {{ $draft['to_email'] }}
                    </div>
                    <div class="draft-card__subject">
                        {{ $draft['subject'] }}
                    </div>
                    <div class="draft-card__meta">
                        <div class="draft-card__time">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ $draft['time_ago'] }}
                        </div>
                        <div class="draft-card__badge">AI Draft</div>
                    </div>
                </div>
                @empty
                <div style="padding: 40px; text-align: center; color: #999;">
                    ยังไม่มี Draft Email<br>
                    <small>ใช้ Chatbot พิมพ์ "ส่ง email ถึงลูกค้า VIP" เพื่อสร้าง Draft</small>
                </div>
                @endforelse

            </div>
        </div>

        <!-- Right Column: Email Editor -->
        <div class="email-editor-container">
            <div class="email-editor-header">
                <div class="email-editor-header__title">
                    Email Editor
                </div>
            </div>

            <div class="email-editor-body" id="emailEditorBody">
                <!-- Email Form -->
                <div class="email-field">
                    <label class="email-field__label">To</label>
                    <input type="text" class="email-field__input email-field__input--to" id="emailTo"
                        placeholder="recipient@example.com" readonly />
                </div>

                <div class="email-field">
                    <label class="email-field__label">Subject</label>
                    <input type="text" class="email-field__input" id="emailSubject"
                        placeholder="Enter email subject..." />
                </div>

                <div class="email-field">
                    <label class="email-field__label">Content</label>
                    <textarea class="email-field__textarea" id="emailContent" placeholder="Write your email content here...

AI will help you draft professional emails based on your CRM data and context."></textarea>
                </div>
            </div>

            <div class="email-editor-actions">
                <button class="btn-update" id="btnUpdate">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Update Draft
                </button>
                <button class="btn-send" id="btnSend">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Send Email
                </button>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/email_ai.js') }}"></script>
@endsection
