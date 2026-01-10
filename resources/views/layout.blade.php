<!doctype html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
       <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SDDS | Enterprise System')</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />

    @yield('styles')
</head>

<body>
    <div class="app">

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <div class="brand__logo">
                    <img src="{{ asset('pictures/logo.png') }}" alt="SDDS">
                </div>
                <div>
                    <div class="brand__name">SDDS</div>
                    <div class="brand__tagline">Enterprise System</div>
                </div>
            </div>

            <nav class="nav">
                <a href="{{ url('customers') }}" class="{{ request()->is('customers') ? 'active' : '' }}">Customers</a>
                <a href="{{ url('email_ai') }}" class="{{ request()->is('email_ai') ? 'active' : '' }}">Email AI</a>
                <a href="{{ url('document') }}" class="{{ request()->is('document') ? 'active' : '' }}">Document</a>
            </nav>
        </aside>

        <!-- Topbar -->
        <header class="topbar">
            <div>
                <div class="topbar__title">@yield('page-title')</div>
                <div class="topbar__menu">@yield('breadcrumb')</div>
            </div>
            <div class="avatar"></div>
        </header>

        <!-- Main Content -->
        <main class="main main--full">
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>

    </div>

    <!-- Chatbot -->
    <div class="chatbot-btn" onclick="toggleChat()">💬</div>

    <div class="chatbot-panel" id="chatbot">
        <div class="chatbot-header">
            SDDS AI Assistant
            <button onclick="toggleChat()">✕</button>
        </div>
        <div class="chatbot-body" id="chatbot-body">
            <div class="chatbot-msg bot">สวัสดีครับ มีอะไรให้ช่วยไหม</div>
        </div>
        <div class="chatbot-input">
          <input type="text" id="chatbot-input" placeholder="พิมพ์คำสั่งถึง AI..." />
          <button id="chatbot-send">ส่ง</button>
        </div>
    </div>

    <!-- External JavaScript -->
    <script src="{{ asset('js/chatbot.js') }}"></script>

    @yield('scripts')
</body>

</html>
