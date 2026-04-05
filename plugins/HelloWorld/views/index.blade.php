@extends('layouts.app')

@section('page-title', 'Hello World Plugin')

@section('page-content')
<div class="page-header">
    <div>
        <h2>Hello World Plugin!</h2>
        <p>This is a demonstration of the CRM plugin system.</p>
    </div>
</div>

<div class="glass-card" style="padding:40px; text-align:center;">
    <div style="width:100px; height:100px; background:linear-gradient(135deg, #a855f7, #ec4899); border-radius:30px; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; font-size:48px; color:#fff;">
        <i class="fas fa-magic"></i>
    </div>
    <h3 style="font-size:24px; margin-bottom:12px;">It Works!</h3>
    <p style="color:var(--text-muted); max-width:500px; margin:0 auto 24px; line-height:1.6;">
        If you see this page, it means the plugin system is successfully registering dynamic routes, providers, and views from the <code>plugins/</code> directory.
    </p>
    <a href="{{ route('settings.plugins') }}" class="btn btn-ghost">
        <i class="fas fa-arrow-left"></i> Back to Plugin Settings
    </a>
</div>
@endsection
