<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $system_branding['system_name'] ?? config('app.name') }} | @yield('page-title')</title>
    @if(isset($system_branding['system_favicon']))
    <link rel="icon" type="image/png" href="{{ asset('storage/'.$system_branding['system_favicon']) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <style>
        :root {
            --brand-navy: #050a15;
            --brand-navy-accent: #0f172a;
            --brand-cyan: #0ea5e9;
            --brand-blue: #1d4ed8;

            --primary: var(--brand-blue);
            --primary-light: var(--brand-cyan);
            --primary-dark: #1e3a8a;
            --accent: var(--brand-cyan);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;

            --glass-bg: rgba(15, 23, 42, 0.4);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-inner-stroke: rgba(255, 255, 255, 0.12);
            --glass-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            --glass-blur: 40px;

            --bg-primary: var(--brand-navy);
            --bg-secondary: var(--brand-navy-accent);
            --bg-tertiary: #1e293b;

            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-muted: rgba(255, 255, 255, 0.45);

            --sidebar-width: 280px;
            --header-height: 72px;
            --radius: 24px;
            --radius-sm: 14px;
            --radius-xs: 8px;

            --font-ar: 'Tajawal', sans-serif;
            --font-en: 'Inter', sans-serif;

            --text-start: left;
            --text-end: right;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-en);
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }


        /* ===== ANIMATED BACKGROUND ===== */
        .bg-gradient {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            background-color: var(--brand-navy);
        }
        .bg-gradient::before {
            content: '';
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(29, 78, 216, 0.15) 0%, transparent 70%);
            top: -200px;
            left: -200px;
            animation: float1 25s ease-in-out infinite;
        }


        .bg-gradient::after {
            content: '';
            position: absolute;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.1) 0%, transparent 70%);
            bottom: -150px;
            right: -150px;
            animation: float2 30s ease-in-out infinite;
        }

        .bg-orb {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(30, 27, 75, 0.2) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: float3 35s ease-in-out infinite;
        }

        @keyframes float1 { 0%, 100% { transform: translate(0, 0) scale(1); } 50% { transform: translate(100px, 50px) scale(1.1); } }
        @keyframes float2 { 0%, 100% { transform: translate(0, 0) scale(1); } 50% { transform: translate(-80px, -60px) scale(1.15); } }
        @keyframes float3 { 0%, 100% { transform: translate(-50%, -50%) scale(1); } 50% { transform: translate(-50%, -50%) scale(1.3); } }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(var(--glass-blur));
            -webkit-backdrop-filter: blur(var(--glass-blur));
            border-right: 1px solid var(--glass-border);
            z-index: 50;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.2);
        }


        .sidebar-brand {
            padding: 24px 24px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid var(--glass-border);
        }
        .sidebar-brand .logo {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-cyan));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
        }
        .sidebar-brand .brand-text h1 {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .sidebar-brand .brand-text span {
            font-size: 11px;
            color: var(--text-muted);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }
        .sidebar-nav .nav-section {
            margin-bottom: 32px;
        }
        .sidebar-nav .nav-section-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 0 12px;
            margin-bottom: 8px;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 8px;
            position: relative;
        }
        .sidebar-nav a i {
            width: 20px;
            text-align: center;
            font-size: 15px;
            opacity: 0.7;
            transition: 0.2s;
        }
        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }
        .sidebar-nav a:hover i { opacity: 1; }
        .sidebar-nav a.active {
            background: rgba(14, 165, 233, 0.12);
            color: var(--brand-cyan);
            font-weight: 700;
            box-shadow: inset 0 0 15px rgba(14, 165, 233, 0.05);
        }
        .sidebar-nav a.active i {
            opacity: 1;
            color: var(--brand-cyan);
            filter: drop-shadow(0 0 5px rgba(14, 165, 233, 0.5));
        }
        .sidebar-nav a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: linear-gradient(to bottom, var(--brand-blue), var(--brand-cyan));
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px rgba(14, 165, 233, 0.5);
        }
        [dir="rtl"] .sidebar-nav a.active::before { left: auto; right: 0; border-radius: 4px 0 0 4px; }
        .sidebar-nav .nav-badge {
            margin-inline-start: 12px;
            background: rgba(99, 102, 241, 0.2);
            color: var(--primary-light);
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--glass-border);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: var(--glass-bg);
            border-radius: var(--radius-sm);
            border: 1px solid var(--glass-border);
        }
        .sidebar-user .avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: white;
        }
        .sidebar-user .user-info {
            flex: 1;
            min-width: 0;
        }
        .sidebar-user .user-name {
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }
        .sidebar-user .btn-logout {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: 0.2s;
        }
        .sidebar-user .btn-logout:hover {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }
        [dir="rtl"] .main-content { margin-left: 0; margin-right: var(--sidebar-width); }

        /* ===== HEADER ===== */
        .header {
            position: sticky;
            top: 0;
            z-index: 40;
            height: var(--header-height);
            background: rgba(10, 10, 26, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .header-left .page-title {
            font-size: 18px;
            font-weight: 600;
        }
        .header-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
        }
        .header-breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            transition: 0.2s;
        }
        .header-breadcrumb a:hover { color: var(--primary-light); }
        .header-breadcrumb .sep { opacity: 0.4; }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-search {
            position: relative;
        }
        .header-search input {
            width: 240px;
            padding: 8px 16px 8px 36px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 13px;
            font-family: inherit;
            transition: 0.2s;
        }
        .header-search input::placeholder { color: var(--text-muted); }
        .header-search input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            width: 300px;
        }
        .header-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 13px;
        }
        [dir="rtl"] .header-search i { left: auto; right: 12px; }

        .lang-switch {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s;
        }
        .lang-switch:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
        }
        .lang-switch i { font-size: 14px; }

        .btn-mobile-menu {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 20px;
            cursor: pointer;
            padding: 8px;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content {
            padding: 28px 32px 40px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }
        .page-header h2 {
            font-size: 26px;
            font-weight: 700;
        }
        .page-header p {
            color: var(--text-secondary);
            font-size: 14px;
            margin-top: 4px;
        }

        /* ===== GLASS CARDS ===== */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(var(--glass-blur));
            -webkit-backdrop-filter: blur(var(--glass-blur));
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 24px;
            position: relative;
            box-shadow: var(--glass-shadow);
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            overflow: hidden;
        }
        .glass-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: var(--radius);
            padding: 1px;
            background: linear-gradient(135deg, var(--glass-inner-stroke), transparent 50%, rgba(255,255,255,0.02));
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: xor;
            -webkit-mask-composite: destination-out;
            pointer-events: none;
        }
        .glass-card:hover {
            border-color: rgba(255, 255, 255, 0.18);
            transform: translateY(-4px);
            background: rgba(15, 23, 42, 0.5);
        }

        /* ===== STAT CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 22px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            border-radius: var(--radius) var(--radius) 0 0;
        }
        [dir="rtl"] .stat-card::before { left: auto; right: 0; }
        .stat-card:nth-child(1)::before { background: linear-gradient(90deg, var(--primary), var(--primary-light)); }
        .stat-card:nth-child(2)::before { background: linear-gradient(90deg, var(--success), #34d399); }
        .stat-card:nth-child(3)::before { background: linear-gradient(90deg, var(--warning), #fbbf24); }
        .stat-card:nth-child(4)::before { background: linear-gradient(90deg, var(--accent), #22d3ee); }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--glass-shadow);
        }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
        }
        .stat-card:nth-child(1) .stat-icon { background: rgba(99, 102, 241, 0.15); color: var(--primary-light); }
        .stat-card:nth-child(2) .stat-icon { background: rgba(16, 185, 129, 0.15); color: var(--success); }
        .stat-card:nth-child(3) .stat-icon { background: rgba(245, 158, 11, 0.15); color: var(--warning); }
        .stat-card:nth-child(4) .stat-icon { background: rgba(6, 182, 212, 0.15); color: var(--accent); }
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 6px;
        }
        .stat-card .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
        }
        .stat-card .stat-change {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
            padding: 2px 8px;
            border-radius: 20px;
        }
        .stat-change.up { background: rgba(16, 185, 129, 0.15); color: var(--success); }
        .stat-change.down { background: rgba(239, 68, 68, 0.15); color: var(--danger); }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }
        .btn-ghost {
            background: var(--glass-bg);
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
        }
        .btn-ghost:hover { background: rgba(255, 255, 255, 0.1); }
        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .btn-danger:hover { background: rgba(239, 68, 68, 0.25); }
        .btn-success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        /* ===== TABLES ===== */
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            padding: 14px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--glass-border);
            white-space: nowrap;
        }
        [dir="rtl"] thead th { text-align: right; }
        tbody td {
            padding: 14px 16px;
            font-size: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            vertical-align: middle;
        }
        tbody tr {
            transition: 0.15s;
        }
        tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        /* ===== RESPONSIVE GRID SYSTEM ===== */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-inline: -15px;
            margin-top: calc(-1 * var(--bs-gutter-y, 0));
        }
        .row > * {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-inline: 15px;
            margin-top: var(--bs-gutter-y, 0);
        }
        
        .col-12 { flex: 0 0 auto; width: 100%; }
        
        @media (min-width: 768px) {
            .col-md-4 { flex: 0 0 auto; width: 33.33333333%; }
            .col-md-6 { flex: 0 0 auto; width: 50%; }
        }
        @media (min-width: 992px) {
            .col-lg-4 { flex: 0 0 auto; width: 33.33333333%; }
            .col-lg-8 { flex: 0 0 auto; width: 66.66666667%; }
            .col-lg-12 { flex: 0 0 auto; width: 100%; }
        }

        /* Increased Spacing Utilities for Independent Cards */
        .g-3 { --bs-gutter-x: 20px; --bs-gutter-y: 20px; margin-inline: -10px; }
        .g-3 > * { padding-inline: 10px; }
        
        .g-4 { --bs-gutter-x: 32px; --bs-gutter-y: 32px; margin-inline: -16px; }
        .g-4 > * { padding-inline: 16px; }
        
        .g-5 { --bs-gutter-x: 48px; --bs-gutter-y: 48px; margin-inline: -24px; }
        .g-5 > * { padding-inline: 24px; }
        
        .mb-3 { margin-bottom: 20px !important; }
        .mb-4 { margin-bottom: 30px !important; }
        .mb-5 { margin-bottom: 48px !important; }
        .mt-4 { margin-top: 30px !important; }
        .mt-5 { margin-top: 48px !important; }

        /* ===== BADGES ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-new { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .badge-contacted { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .badge-interested { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .badge-not_interested { background: rgba(239, 68, 68, 0.15); color: #f87171; }
        .badge-converted { background: rgba(168, 85, 247, 0.15); color: #c084fc; }
        .badge-low { background: rgba(148, 163, 184, 0.15); color: #94a3b8; }
        .badge-medium { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .badge-high { background: rgba(249, 115, 22, 0.15); color: #fb923c; }
        .badge-urgent { background: rgba(239, 68, 68, 0.15); color: #f87171; }
        .badge-admin { background: rgba(99, 102, 241, 0.15); color: var(--primary-light); }

        /* ===== FORMS ===== */
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            padding: 12px 18px;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
        }
        .form-control::placeholder { color: var(--text-muted); }
        .form-control:focus {
            outline: none;
            border-color: var(--brand-cyan);
            background: rgba(0, 0, 0, 0.35);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4), 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 40px;
            cursor: pointer;
        }
        [dir="rtl"] select.form-control { background-position: left 12px center; padding-right: 18px; padding-left: 40px; }
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
            line-height: 1.6;
        }

        /* ===== SEARCH BAR ===== */
        .search-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .search-bar .search-input {
            flex: 1;
            min-width: 250px;
            position: relative;
        }
        .search-bar .search-input input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 14px;
            font-family: inherit;
        }
        .search-bar .search-input i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        [dir="rtl"] .search-bar .search-input i { left: auto; right: 14px; }
        .search-bar select {
            min-width: 160px;
            padding: 10px 16px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 14px;
            font-family: inherit;
            appearance: none;
            cursor: pointer;
        }

        /* ===== MODAL ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 200;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.show { display: flex; }
        .modal {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 28px;
            width: 100%;
            max-width: 520px;
            max-height: 85vh;
            overflow-y: auto;
            animation: modalIn 0.3s ease;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
        }
        .modal-close {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: 0.2s;
        }
        .modal-close:hover { background: rgba(239, 68, 68, 0.15); color: var(--danger); border-color: rgba(239, 68, 68, 0.2); }

        /* ===== ALERTS ===== */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--success);
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }
        .alert i { font-size: 16px; }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        .empty-state .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.3;
        }
        .empty-state h3 { font-size: 18px; color: var(--text-secondary); margin-bottom: 8px; }
        .empty-state p { font-size: 14px; margin-bottom: 20px; }

        /* ===== PAGINATION ===== */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: 24px;
        }
        .pagination a, .pagination span {
            padding: 8px 14px;
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            color: var(--text-secondary);
            transition: 0.2s;
        }
        .pagination a:hover { background: rgba(255, 255, 255, 0.05); color: var(--text-primary); }
        .pagination span.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* ===== DETAIL LIST ===== */
        .detail-list tr th {
            width: 140px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ===== GRID LAYOUTS ===== */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }

        /* ===== ACTIONS & DROPDOWNS ===== */
        .actions { display: flex; gap: 6px; align-items: center; }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: var(--bg-secondary);
            min-width: 160px;
            box-shadow: var(--glass-shadow);
            z-index: 100;
            border-radius: var(--radius-sm);
            border: 1px solid var(--glass-border);
            padding: 8px 0;
            margin-top: 4px;
            backdrop-filter: blur(20px);
            animation: dropdownIn 0.2s ease;
        }
        [dir="rtl"] .dropdown-content { right: auto; left: 0; }

        @keyframes dropdownIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13px;
            transition: 0.2s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        [dir="rtl"] .dropdown-item { text-align: right; }

        .dropdown-item i {
            width: 16px;
            text-align: center;
            font-size: 14px;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }

        .dropdown-item.text-danger { color: var(--danger); }
        .dropdown-item.text-danger:hover { background: rgba(239, 68, 68, 0.1); }

        /* ===== CHART PLACEHOLDER ===== */
        .chart-container {
            height: 200px;
            display: flex;
            align-items: flex-end;
            gap: 8px;
            padding-top: 20px;
        }
        .chart-bar {
            flex: 1;
            border-radius: 6px 6px 0 0;
            background: linear-gradient(180deg, var(--primary), rgba(99, 102, 241, 0.3));
            min-height: 20px;
            transition: 0.3s;
            position: relative;
        }
        .chart-bar:hover { opacity: 0.8; }
        .chart-bar .bar-label {
            position: absolute;
            bottom: -24px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
        }
        .chart-bar .bar-value {
            position: absolute;
            top: -22px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        /* ===== STATUS DISTRIBUTION ===== */
        .status-bar {
            display: flex;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.05);
            margin: 16px 0;
        }
        .status-bar .segment { transition: 0.3s; }
        .status-bar .seg-new { background: #60a5fa; }
        .status-bar .seg-contacted { background: #fbbf24; }
        .status-bar .seg-interested { background: #34d399; }
        .status-bar .seg-not_interested { background: #f87171; }
        .status-bar .seg-converted { background: #c084fc; }

        .status-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 12px;
        }
        .status-legend .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-secondary);
        }
        .status-legend .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        /* ===== ERROR TEXT ===== */
        .error-text { color: var(--danger); font-size: 12px; margin-top: 4px; }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .main-content { margin-left: 0; }
            .btn-mobile-menu { display: block; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .page-content { padding: 20px 16px; }
            .header { padding: 0 16px; }
            .stats-grid { grid-template-columns: 1fr; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .header-search { display: none; }
        }

        /* ===== FADE IN ANIMATION ===== */
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== TOOLTIP ===== */
        [data-tooltip] {
            position: relative;
        }
        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 4px 10px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-size: 11px;
            border-radius: 6px;
            white-space: nowrap;
            margin-bottom: 6px;
            border: 1px solid var(--glass-border);
        }

        /* ===== SIDEBAR OVERLAY (mobile) ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 45;
        }
        .sidebar-overlay.show { display: block; }

        /* ================================================================
           GLASSMORPHISM PAGE SYSTEM — Shared across all pages
        ================================================================ */

        /* --- Page Shell (header + actions) --- */
        .page-shell {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 28px;
            padding: 0 2px;
        }
        .page-shell-left { display: flex; align-items: center; gap: 18px; }
        .page-icon {
            width: 52px; height: 52px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .page-icon-cyan  { background: rgba(14,165,233,.15); color: #38bdf8; box-shadow: 0 0 20px rgba(14,165,233,.15); }
        .page-icon-blue  { background: rgba(99,102,241,.15); color: #818cf8; box-shadow: 0 0 20px rgba(99,102,241,.15); }
        .page-icon-green { background: rgba(16,185,129,.15); color: #34d399; box-shadow: 0 0 20px rgba(16,185,129,.15); }
        .page-icon-amber { background: rgba(245,158,11,.15); color: #fbbf24; box-shadow: 0 0 20px rgba(245,158,11,.15); }
        .page-icon-violet{ background: rgba(167,139,250,.15);color: #a78bfa; box-shadow: 0 0 20px rgba(167,139,250,.15); }
        .page-icon-rose  { background: rgba(251,113,133,.15);color: #fb7185; box-shadow: 0 0 20px rgba(251,113,133,.15); }
        .page-shell-title { font-size: clamp(20px,2.5vw,28px); font-weight: 800; color: #fff; margin: 0 0 4px; }
        .page-shell-sub   { font-size: 13px; color: var(--text-muted); margin: 0; }
        .page-shell-right { display: flex; gap: 10px; flex-wrap: wrap; }

        /* --- Glass Panel (card container) --- */
        .g-panel {
            background: rgba(15,23,42,0.55);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 20px;
            transition: box-shadow .3s;
        }
        .g-panel:hover { box-shadow: 0 16px 40px rgba(0,0,0,.35); }
        .g-panel-p { padding: 22px 26px; }

        /* --- Filter Bar --- */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            padding: 18px 22px;
        }
        .filter-search {
            flex: 1;
            min-width: 240px;
            position: relative;
        }
        .filter-search i {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 13px;
            pointer-events: none;
        }
        .filter-search input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 12px;
            color: #fff;
            font-size: 13px;
            font-family: inherit;
            transition: .2s;
        }
        .filter-search input::placeholder { color: var(--text-muted); }
        .filter-search input:focus { outline: none; border-color: var(--brand-cyan); background: rgba(255,255,255,.06); box-shadow: 0 0 0 3px rgba(14,165,233,.1); }

        .filter-select {
            padding: 10px 36px 10px 14px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 12px;
            color: #fff;
            font-size: 13px;
            font-family: inherit;
            appearance: none;
            cursor: pointer;
            min-width: 150px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            transition: .2s;
        }
        .filter-select:focus { outline: none; border-color: var(--brand-cyan); }
        .filter-select option { background: #0f172a; color: #fff; }

        .filter-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 13px; font-weight: 600; font-family: inherit;
            border: none; cursor: pointer; text-decoration: none;
            transition: all .2s;
        }
        .filter-btn-primary {
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-cyan));
            color: #fff;
            box-shadow: 0 4px 16px rgba(29,78,216,.35);
        }
        .filter-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(29,78,216,.45); color:#fff; }
        .filter-btn-ghost {
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.75);
            border: 1px solid rgba(255,255,255,.1);
        }
        .filter-btn-ghost:hover { background: rgba(255,255,255,.12); color: #fff; }
        .filter-btn-danger {
            background: rgba(239,68,68,.1);
            color: #f87171;
            border: 1px solid rgba(239,68,68,.2);
        }
        .filter-btn-danger:hover { background: rgba(239,68,68,.2); }

        /* --- Data Table --- */
        .g-table-wrap { overflow-x: auto; }
        .g-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .g-table thead tr {
            background: rgba(255,255,255,.025);
        }
        .g-table thead th {
            padding: 14px 20px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            white-space: nowrap;
        }
        .g-table tbody tr {
            transition: background .15s;
        }
        .g-table tbody tr:hover { background: rgba(255,255,255,.04); }
        .g-table tbody td {
            padding: 14px 20px;
            border-bottom: 1px solid rgba(255,255,255,.04);
            vertical-align: middle;
        }
        .g-table tbody tr:last-child td { border-bottom: none; }

        /* Row avatar + name pattern */
        .t-avatar-wrap { display: flex; align-items: center; gap: 14px; }
        .t-avatar {
            width: 38px; height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-cyan));
            color: #fff;
            font-size: 14px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .t-avatar-violet { background: linear-gradient(135deg, #7c3aed, #a78bfa); }
        .t-avatar-green  { background: linear-gradient(135deg, #059669, #34d399); }
        .t-avatar-amber  { background: linear-gradient(135deg, #b45309, #fbbf24); }
        .t-name  { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.3; }
        .t-sub   { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .t-muted { font-size: 13px; color: var(--text-muted); }
        .t-link  { color: var(--brand-cyan); text-decoration: none; font-weight: 600; font-size: 13px; }
        .t-link:hover { text-decoration: underline; }

        /* Action icon buttons */
        .g-btn-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            border: none; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 13px;
            text-decoration: none;
            transition: all .15s;
        }
        .g-btn-icon-view   { background: rgba(14,165,233,.1);  color: #38bdf8; }
        .g-btn-icon-view:hover   { background: rgba(14,165,233,.25); }
        .g-btn-icon-edit   { background: rgba(245,158,11,.1);  color: #fbbf24; }
        .g-btn-icon-edit:hover   { background: rgba(245,158,11,.25); }
        .g-btn-icon-delete { background: rgba(239,68,68,.08); color: #f87171; }
        .g-btn-icon-delete:hover { background: rgba(239,68,68,.2); }
        .g-act-row { display: flex; justify-content: flex-end; gap: 6px; align-items: center; }

        /* Status / health pills */
        .g-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
        }
        /* lead statuses */
        .g-pill-new          { background: rgba(96,165,250,.15); color: #60a5fa; }
        .g-pill-contacted    { background: rgba(251,191,36,.15); color: #fbbf24; }
        .g-pill-interested   { background: rgba(52,211,153,.15); color: #34d399; }
        .g-pill-not_interested{ background: rgba(248,113,113,.15);color: #f87171; }
        .g-pill-converted    { background: rgba(192,132,252,.15);color: #c084fc; }
        /* invoice/deal statuses */
        .g-pill-draft        { background: rgba(148,163,184,.12);color: #94a3b8; }
        .g-pill-sent         { background: rgba(14,165,233,.15); color: #38bdf8; }
        .g-pill-paid         { background: rgba(52,211,153,.15); color: #34d399; }
        .g-pill-partial      { background: rgba(251,191,36,.15); color: #fbbf24; }
        .g-pill-overdue      { background: rgba(248,113,113,.15);color: #f87171; }
        .g-pill-cancelled    { background: rgba(148,163,184,.08);color: #94a3b8; }
        .g-pill-active       { background: rgba(52,211,153,.15); color: #34d399; }
        .g-pill-inactive     { background: rgba(148,163,184,.1); color: #94a3b8; }
        .g-pill-won          { background: rgba(52,211,153,.15); color: #34d399; }
        .g-pill-lost         { background: rgba(248,113,113,.15);color: #f87171; }
        /* health score */
        .g-pill-hot          { background: rgba(248,113,113,.15);color: #f87171; }
        .g-pill-warm         { background: rgba(251,191,36,.15); color: #fbbf24; }
        .g-pill-cold         { background: rgba(96,165,250,.15); color: #60a5fa; }
        .g-pill-churning     { background: rgba(239,68,68,.12);  color: #f87171; }
        .g-pill-signed       { background: rgba(52,211,153,.15); color: #34d399; }
        .g-pill-pending      { background: rgba(251,191,36,.15); color: #fbbf24; }
        .g-pill-expired      { background: rgba(148,163,184,.1); color: #94a3b8; }
        .g-pill-terminated   { background: rgba(248,113,113,.15);color: #f87171; }

        /* Empty state */
        .g-empty {
            text-align: center;
            padding: 80px 24px;
            color: var(--text-muted);
        }
        .g-empty i { font-size: 48px; display: block; margin-bottom: 16px; opacity: .25; }
        .g-empty h3 { font-size: 18px; font-weight: 700; color: rgba(255,255,255,.6); margin-bottom: 8px; }
        .g-empty p  { font-size: 13px; margin-bottom: 24px; }

        /* Mini summary stat */
        .g-stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 16px; margin-bottom: 24px; }
        .g-stat {
            background: rgba(15,23,42,.55);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 16px;
            padding: 20px 22px;
            display: flex; align-items: center; gap: 16px;
        }
        .g-stat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .g-stat-val { font-size: 26px; font-weight: 900; color: #fff; line-height: 1; margin-bottom: 3px; }
        .g-stat-lbl { font-size: 12px; color: var(--text-muted); font-weight: 600; }

        /* Glassmorphism modal */
        .gm-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.6);
            backdrop-filter: blur(10px);
            z-index: 300;
            align-items: center; justify-content: center;
            padding: 20px;
        }
        .gm-overlay.show { display: flex; }
        .gm-box {
            background: rgba(15,23,42,.9);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 24px;
            width: 100%;
            max-width: 560px;
            max-height: 90vh;
            overflow-y: auto;
            animation: gmIn .3s ease;
            box-shadow: 0 32px 80px rgba(0,0,0,.5);
        }
        .gm-box-lg { max-width: 720px; }
        @keyframes gmIn { from { opacity:0; transform: scale(.95) translateY(20px); } to { opacity:1; transform: scale(1) translateY(0); } }
        .gm-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 22px 26px 0;
        }
        .gm-title { font-size: 18px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; }
        .gm-close {
            width: 32px; height: 32px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 8px;
            color: var(--text-muted);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            transition: .2s;
        }
        .gm-close:hover { background: rgba(239,68,68,.15); color: #f87171; }
        .gm-body  { padding: 20px 26px; }
        .gm-footer { padding: 0 26px 22px; display: flex; justify-content: flex-end; gap: 10px; }

        .gm-label {
            display: block;
            font-size: 12px; font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase; letter-spacing: .8px;
            margin-bottom: 6px;
        }
        .gm-input {
            width: 100%;
            padding: 11px 16px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 10px;
            color: #fff;
            font-size: 14px; font-family: inherit;
            transition: .2s;
        }
        .gm-input::placeholder { color: var(--text-muted); }
        .gm-input:focus { outline: none; border-color: var(--brand-cyan); background: rgba(255,255,255,.07); box-shadow: 0 0 0 3px rgba(14,165,233,.1); }
        select.gm-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
            cursor: pointer;
        }
        select.gm-input option { background: #0f172a; }

        /* Whatsapp contact icon */
        .wa-link { color: #22c55e; font-size: 16px; text-decoration: none; transition: .15s; }
        .wa-link:hover { color: #4ade80; }

    </style>
</head>
<body>
    <div class="bg-gradient"><div class="bg-orb"></div></div>

    @auth
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 40px; padding: 0 8px;">
                <div class="logo-container" style="width: 45px; height: 45px; border-radius: 14px; background: linear-gradient(135deg, var(--brand-blue), var(--brand-cyan)); display: flex; align-items: center; justify-content: center; box-shadow: var(--neon-glow); overflow: hidden;">
                    @if(isset($system_branding['system_logo']))
                        <img src="{{ asset('storage/'.$system_branding['system_logo']) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="{{ $system_branding['system_icon'] ?? 'fas fa-layer-group' }}" style="font-size: 20px; color: #fff;"></i>
                    @endif
                </div>
                <div class="sidebar-header-text">
                    <h1 style="font-size: 18px; font-weight: 800; color: #fff; margin: 0; letter-spacing: -0.5px;">{{ $system_branding['system_name'] ?? config('app.name') }}</h1>
                    <div style="font-size: 10px; color: var(--brand-cyan); font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Growth OS</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">{{ __('messages.personal') }}</div>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i><span>{{ __('messages.dashboard') }}</span>
                </a>
                <a href="{{ route('employees.profile') }}" class="{{ request()->routeIs('employees.profile') ? 'active' : '' }}">
                    <i class="fas fa-user-astronaut"></i><span>{{ __('messages.command_center') }}</span>
                </a>
                @if(auth()->user()->is_admin)
                <a href="{{ route('admin.executive') }}" class="{{ request()->routeIs('admin.executive') ? 'active' : '' }}">
                    <i class="fas fa-crown"></i><span>{{ __('messages.executive_dashboard') }}</span>
                </a>
                @endif
            </div>
            <div class="nav-section">
                <div class="nav-section-title">{{ __('messages.sales') }}</div>
                @can('view-leads')
                <a href="{{ route('leads.index') }}" class="{{ request()->routeIs('leads.*') && !request()->routeIs('leads.kanban') ? 'active' : '' }}">
                    <i class="fas fa-users"></i><span>{{ __('messages.leads') }}</span>
                </a>
                <a href="{{ route('leads.kanban') }}" class="{{ request()->routeIs('leads.kanban') ? 'active' : '' }}">
                    <i class="fas fa-columns"></i><span>{{ __('messages.pipeline') }}</span>
                </a>
                @endcan
                <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-building-user"></i><span>{{ __('messages.customers') }}</span>
                </a>
                <a href="{{ route('deals.index') }}" class="{{ request()->routeIs('deals.*') ? 'active' : '' }}">
                    <i class="fas fa-handshake"></i><span>{{ __('messages.deals') }}</span>
                </a>
                <a href="{{ route('campaigns.index') }}" class="{{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i><span>{{ __('messages.campaigns') }}</span>
                </a>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">{{ __('messages.finance') }}</div>
                @can('view-quotations')
                <a href="{{ route('quotations.index') }}" class="{{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i><span>{{ __('messages.quotations') }}</span>
                </a>
                @endcan
                @can('view-invoices')
                <a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i><span>{{ __('messages.invoices') }}</span>
                </a>
                @endcan
                <a href="{{ route('contracts.index') }}" class="{{ request()->routeIs('contracts.*') ? 'active' : '' }}">
                    <i class="fas fa-file-contract"></i><span>{{ __('messages.contracts') }}</span>
                </a>
                <a href="{{ route('services.index') }}" class="{{ request()->routeIs('services.*') ? 'active' : '' }}">
                    <i class="fas fa-concierge-bell"></i><span>{{ __('messages.services') }}</span>
                </a>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">{{ __('messages.productivity') }}</div>
                <a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i><span>{{ __('messages.tasks') }}</span>
                </a>
                <a href="{{ route('task-templates.index') }}" class="{{ request()->routeIs('task-templates.*') ? 'active' : '' }}">
                    <i class="fas fa-magic"></i><span>{{ __('messages.task_templates') }}</span>
                </a>
                <a href="{{ route('reports') }}" class="{{ request()->routeIs('reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i><span>{{ __('messages.reports') }}</span>
                </a>
                <a href="{{ route('team.performance') }}" class="{{ request()->routeIs('team.*') ? 'active' : '' }}">
                    <i class="fas fa-trophy"></i><span>{{ __('messages.team') }}</span>
                </a>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">{{ __('messages.settings') }}</div>
                <a href="{{ route('companies.index') }}" class="{{ request()->routeIs('companies.*') ? 'active' : '' }}">
                    <i class="fas fa-sitemap"></i><span>{{ __('messages.companies') }}</span>
                </a>
                @can('manage-employees')
                <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i><span>{{ __('messages.employees') }}</span>
                </a>
                @endcan
                @can('manage-roles')
                <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i><span>{{ __('messages.roles') }}</span>
                </a>
                @endcan

                @if(auth()->user()->is_admin)
                <a href="{{ route('settings.branding') }}" class="{{ request()->routeIs('settings.branding') ? 'active' : '' }}">
                    <i class="fas fa-tools"></i><span>{{ __('messages.system_settings') ?? 'System Settings' }}</span>
                </a>
                @endif
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->company->name ?? '-' }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout" title="{{ __('messages.logout') }}">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- HEADER -->
        <header class="header">
            <div class="header-left">
                <button class="btn-mobile-menu" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div>
                    <h2 class="page-title">@yield('page-title', __('messages.dashboard'))</h2>
                </div>
            </div>
            <div class="header-right">
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <div class="page-content fade-in">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
    @else
        @yield('content')
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
    </script>
</body>
</html>
