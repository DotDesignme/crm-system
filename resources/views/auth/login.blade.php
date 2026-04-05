<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.sign_in') ?? 'Sign In' }} | {{ $system_branding['app_name'] ?? 'OpenCRM' }}</title>
    
    @if(isset($system_branding['system_favicon']))
    <link rel="icon" type="image/png" href="{{ asset('storage/'.$system_branding['system_favicon']) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @php
        $primaryColor = $system_branding['primary_color'] ?? '#1d4ed8';
        $accentColor = $system_branding['accent_color'] ?? '#0ea5e9';
    @endphp

    <style>
        :root {
            --primary: {{ $primaryColor }};
            --accent: {{ $accentColor }};
            --bg-dark: #050a15;
            --glass-bg: rgba(15, 23, 42, 0.6);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-main: #ffffff;
            --text-dim: rgba(255, 255, 255, 0.6);
            --radius: 28px;
        }

        * { margin:0; padding:0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .login-page {
            display: flex;
            min-height: 100vh;
        }

        /* --- LEFT SIDE: THE BRAND HERO --- */
        .brand-section {
            flex: 1.2;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: var(--bg-dark);
            padding: 60px;
        }

        /* Animated Mesh Gradient Background */
        .mesh-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
        }
        .mesh-bg::before {
            content: '';
            position: absolute;
            width: 140%;
            height: 140%;
            top: -20%;
            left: -20%;
            background: 
                radial-gradient(circle at 20% 30%, {{ $primaryColor }}33 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, {{ $accentColor }}22 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, #1e1b4b 0%, transparent 60%);
            filter: blur(80px);
            animation: meshRotate 20s linear infinite;
        }
        @keyframes meshRotate {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.1); }
            100% { transform: rotate(360deg) scale(1); }
        }

        .brand-content {
            position: relative;
            z-index: 10;
            max-width: 540px;
            text-align: center;
        }

        .brand-logo-frame {
            width: 100px;
            height: 100px;
            margin: 0 auto 40px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .brand-logo-frame img {
            max-height: 60px;
            max-width: 60px;
            filter: drop-shadow(0 0 10px {{ $primaryColor }}66);
        }
        .brand-logo-frame i {
            font-size: 40px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-content h1 {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 16px;
            background: linear-gradient(to bottom, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-content .slogan {
            font-size: 20px;
            color: var(--text-dim);
            line-height: 1.6;
            font-weight: 400;
        }

        /* --- RIGHT SIDE: THE LOGIN FORM --- */
        .form-section {
            flex: 0.8;
            background: #080d1a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            border-left: 1px solid rgba(255,255,255,0.05);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--glass-bg);
            backdrop-filter: blur(40px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 48px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), var(--accent), transparent);
        }

        .form-header { margin-bottom: 40px; }
        .form-header h2 { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        .form-header p { color: var(--text-dim); font-size: 14px; }

        .form-group { margin-bottom: 24px; position: relative; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dim);
            margin-bottom: 10px;
        }

        .input-control {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 14px 18px 14px 48px;
            color: #fff;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }
        .input-control:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255,255,255,0.08);
            box-shadow: 0 0 0 4px {{ $primaryColor }}22;
        }
        .form-group i {
            position: absolute;
            left: 18px;
            top: 42px;
            color: var(--text-dim);
            font-size: 16px;
            transition: 0.3s;
        }
        .input-control:focus + i { color: var(--primary); }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            font-size: 14px;
        }
        .remember-me { display: flex; align-items: center; gap: 10px; cursor: pointer; color: var(--text-dim); }
        .remember-me input { width: 18px; height: 18px; accent-color: var(--primary); }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border: none;
            border-radius: 14px;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 10px 20px {{ $primaryColor }}44;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px {{ $primaryColor }}66;
            filter: brightness(1.1);
        }

        .alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 14px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .brand-section { display: none; }
            .form-section { flex: 1; background: var(--bg-dark); }
            .form-section::before {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(circle at 50% 50%, {{ $primaryColor }}11 0%, transparent 70%);
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <!-- BRANDING SIDE -->
        <div class="brand-section">
            <div class="mesh-bg"></div>
            <div class="brand-content">
                <div class="brand-logo-frame">
                    @if(isset($system_branding['system_logo']))
                        <img src="{{ asset('storage/'.$system_branding['system_logo']) }}" alt="Logo">
                    @else
                        <i class="{{ $system_branding['system_icon'] ?? 'fas fa-rocket' }}"></i>
                    @endif
                </div>
                <h1>{{ $system_branding['system_name'] ?? 'OpenCRM' }}</h1>
                <p class="slogan">{{ $system_branding['system_slogan'] ?? 'Elevate Your Business Workflow with Next-Generation Intelligence and Seamless Management.' }}</p>
            </div>
        </div>

        <!-- FORM SIDE -->
        <div class="form-section">
            <div class="login-card">
                <div class="form-header">
                    <h2>{{ __('messages.welcome_back') ?? 'Welcome Back' }}</h2>
                    <p>{{ __('messages.please_enter_details') ?? 'Please enter your account details' }}</p>
                </div>

                @if($errors->any())
                    <div class="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        @foreach($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('messages.username') ?? 'Username' }}</label>
                        <input type="text" name="username" class="input-control" value="{{ old('username') }}" required autofocus>
                        <i class="fas fa-user"></i>
                    </div>

                    <div class="form-group">
                        <label>{{ __('messages.password') ?? 'Password' }}</label>
                        <input type="password" name="password" class="input-control" required>
                        <i class="fas fa-lock"></i>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>{{ __('messages.remember_me') ?? 'Remember me' }}</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        {{ __('messages.sign_in') ?? 'Sign In' }}
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
