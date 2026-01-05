<!doctype html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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

        <!-- Form -->
        <form>
            <div class="form-group">
                <label>Email</label>
                <input type="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" required>
            </div>

            <a href="{{ url('customers') }}">
                <button class="btn-login" type="button">
                    เข้าสู่ระบบ
                </button>
            </a>
        </form>

        <div class="login-footer">
            © 2025 SDDS Enterprise Platform
        </div>

    </div>

</body>

</html>
