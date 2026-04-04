<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.sign_in') }} | {{ $system_branding['app_name'] ?? 'Floor-in' }}</title>
    @if(isset($system_branding['favicon_path']))
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/'.$system_branding['favicon_path']) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --primary-dark: #4f46e5;
            --accent: #06b6d4;
            --bg-primary: #0a0a1a;
            --glass-bg: rgba(255, 255, 255, 0.06);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --danger: #ef4444;
            --radius: 20px;
            --font-ar: 'Tajawal', sans-serif;
            --font-en: 'Inter', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-en);
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated Background */
        .bg-scene {
            position: fixed;
            inset: 0;
            overflow: hidden;
        }
        .bg-scene .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
        }
        .bg-scene .orb-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.3), transparent 70%);
            top: -200px; left: -100px;
            animation: orbFloat1 20s ease-in-out infinite;
        }
        .bg-scene .orb-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.2), transparent 70%);
            bottom: -200px; right: -100px;
            animation: orbFloat2 25s ease-in-out infinite;
        }
        .bg-scene .orb-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.15), transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation: orbFloat3 15s ease-in-out infinite;
        }

        @keyframes orbFloat1 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(80px,60px) scale(1.2); } }
        @keyframes orbFloat2 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(-60px,-80px) scale(1.15); } }
        @keyframes orbFloat3 { 0%,100% { transform: translate(-50%,-50%) scale(1); } 50% { transform: translate(-50%,-50%) scale(1.4); } }

        /* Login Card */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }
        .login-card {
            background: rgba(17, 17, 40, 0.5);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 48px 40px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.4);
        }

        .login-brand {
            text-align: center;
            margin-bottom: 40px;
        }
        .login-brand .logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4);
            animation: logoPulse 3s ease-in-out infinite;
        }
        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4); }
            50% { box-shadow: 0 8px 40px rgba(99, 102, 241, 0.6); }
        }
        .login-brand h1 {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #fff, var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }
        .login-brand p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
            transition: 0.2s;
        }
        .input-wrapper input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 14px;
            font-family: inherit;
            transition: 0.2s;
        }
        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            background: rgba(255, 255, 255, 0.06);
        }
        .input-wrapper input:focus + i,
        .input-wrapper input:focus ~ i {
            color: var(--primary-light);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        .checkbox-wrapper span {
            font-size: 13px;
            color: var(--text-muted);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
            position: relative;
            overflow: hidden;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: translateX(-100%);
        }
        .btn-login:hover::after { transform: translateX(100%); transition: 0.6s; }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .lang-switch {
            text-align: center;
            margin-top: 24px;
        }
        .lang-switch a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            transition: 0.2s;
        }
        .lang-switch a:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
        }

        @media (max-width: 480px) {
            .login-card { padding: 36px 24px; }
        }
    </style>
</head>
<body>
    <div class="bg-scene">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-brand">
                <div class="logo">
                    @if(isset($system_branding['logo_path']))
                        <img src="{{ asset('storage/'.$system_branding['logo_path']) }}" alt="Logo" style="max-height: 48px; width: auto;">
                    @else
                        <i class="{{ $system_branding['system_icon'] ?? 'fas fa-bolt' }}"></i>
                    @endif
                </div>
                <h1>{{ $system_branding['app_name'] ?? 'Floor-in' }}</h1>
                <p>{{ __('messages.login_subtitle') }}</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label>{{ __('messages.username') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="{{ __('messages.username') }}" required autofocus>
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.password') }}</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" placeholder="{{ __('messages.password') }}" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember">
                        <span>{{ __('messages.remember_me') }}</span>
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    {{ __('messages.sign_in') }}
                </button>
            </form>


        </div>
    </div>
</body>
</html>
