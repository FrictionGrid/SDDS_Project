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
                @auth
                    @php
                        $userMenus = getUserMenus();
                    @endphp

                    @forelse($userMenus as $menu)
                        <a href="{{ url($menu->url) }}"
                           class="{{ request()->is(trim($menu->url, '/')) ? 'active' : '' }}"
                           @if($menu->icon) data-icon="{{ $menu->icon }}" @endif>
                            {{ $menu->name }}
                        </a>

                        {{-- Display submenus if any --}}
                        @if($menu->activeChildren && $menu->activeChildren->count() > 0)
                            @foreach($menu->activeChildren as $submenu)
                                <a href="{{ url($submenu->url) }}"
                                   class="nav__submenu {{ request()->is(trim($submenu->url, '/')) ? 'active' : '' }}"
                                   @if($submenu->icon) data-icon="{{ $submenu->icon }}" @endif>
                                    {{ $submenu->name }}
                                </a>
                            @endforeach
                        @endif
                    @empty
                        {{-- Default fallback menu when no permissions are set --}}
                        <a href="{{ url('dashboard/sale') }}" class="{{ request()->is('dashboard/sale') ? 'active' : '' }}">Dashboard</a>
                        <a href="{{ url('customers') }}" class="{{ request()->is('customers') ? 'active' : '' }}">Customers</a>
                        <a href="{{ url('email_ai') }}" class="{{ request()->is('email_ai') ? 'active' : '' }}">Email AI</a>
                        <a href="{{ url('document') }}" class="{{ request()->is('document') ? 'active' : '' }}">Document</a>
                    @endforelse
                @else
                    {{-- Guest users see no menu or login prompt --}}
                    <a href="{{ url('login') }}">Login</a>
                @endauth
            </nav>
        </aside>

        <!-- Topbar -->
        <header class="topbar">
            <div>
                <div class="topbar__title">@yield('page-title')</div>
                <div class="topbar__menu">@yield('breadcrumb')</div>
            </div>
            <div class="topbar-user">
                @auth
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-email">{{ auth()->user()->email }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="btn-logout">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                    <div class="avatar"></div>
                @endauth
            </div>
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
