<!doctype html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SDDS | Login</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
</head>

<body class="login-page">

    <div class="login-card">

        <!-- Brand -->
        <div class="brand">
            <div class="brand__logo">
                <img src="{{ asset('pictures/logo.png') }}" alt="SDDS">
            </div>
            <div>
                <div class="brand__name">SDDS</div>
                <div class="brand__tagline">Enterprise System</div>
            </div>
        </div>

        <!-- Title -->
        <div class="login-title">เข้าสู่ระบบ</div>
        <div class="login-sub">กรุณาเข้าสู่ระบบเพื่อใช้งานระบบองค์กร</div>

        @if(session('error'))
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
                @error('password')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

         

            <button class="btn-login" type="submit">
                เข้าสู่ระบบ
            </button>
        </form>

        <div class="login-footer">
            © 2026 SDDS Enterprise Platform
        </div>

    </div>

</body>

</html>
