@extends('layouts.app')

@section('page-title', $quotation->quotation_number)

@section('content')
<div class="page-header" style="margin-bottom: 30px;">
    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="{{ route('quotations.index') }}" class="btn btn-icon btn-sm" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
            <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        </a>
        <div>
            <h2 class="text-glow" style="font-size: 26px; font-weight: 800; letter-spacing: -0.5px;">{{ $quotation->quotation_number }}</h2>
            <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                <span style="font-size: 13px; color: var(--text-muted);">{{ __('messages.status') }}:</span>
                @php
                    $statusColor = 'var(--brand-cyan)';
                    if($quotation->status == 'accepted') $statusColor = 'var(--success)';
                    if($quotation->status == 'rejected') $statusColor = '#f87171';
                @endphp
                <span class="badg-count" style="background: {{ $statusColor }}22; color: {{ $statusColor }}; border-color: {{ $statusColor }}44;">
                    {{ __('messages.' . $quotation->status) }}
                </span>
            </div>
        </div>
    </div>
    <div style="display: flex; gap: 12px;">
        @if($quotation->status !== 'accepted')
        <form method="POST" action="{{ route('quotations.convert', $quotation) }}">
            @csrf
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px; background: linear-gradient(135deg, var(--brand-blue), var(--brand-cyan)); border: none; box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);">
                <i class="fas fa-check-double" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i>
                {{ __('messages.convert_to_invoice') }}
            </button>
        </form>
        @endif
        <a href="{{ route('quotations.download', $quotation) }}" class="btn btn-ghost">
            <i class="fas fa-file-pdf" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: #f87171;"></i> {{ __('messages.download_pdf') }}
        </a>
        <button class="btn btn-ghost" onclick="window.print()">
            <i class="fas fa-print" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: var(--text-secondary);"></i> {{ __('messages.print') }}
        </button>
    </div>
</div>

<div class="glass-card" style="max-width: 1000px; padding: 60px; border-radius: 32px; border: 1px solid var(--glass-border); margin-bottom: 40px; position: relative; overflow: hidden;">
    {{-- Decorative Background Element --}}
    <div style="position: absolute; top: -100px; right: -100px; width: 300px; height: 300px; background: radial-gradient(circle, var(--brand-cyan) 0%, transparent 70%); opacity: 0.05; pointer-events: none;"></div>

    {{-- Quotation Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 60px;">
        <div>
            <div style="font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 3px; color: var(--brand-cyan); margin-bottom: 12px;">{{ __('messages.quotation') }}</div>
            <h1 style="font-size: 42px; font-weight: 900; letter-spacing: -1.5px; color: #fff; margin: 0; line-height: 1;">{{ $quotation->quotation_number }}</h1>
            @if($quotation->deal)
                <div style="margin-top: 12px; font-size: 14px; color: var(--brand-cyan); font-weight: 700;">
                    <i class="fas fa-briefcase" style="font-size: 10px; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; opacity: 0.7;"></i>
                    {{ $quotation->deal->title }}
                </div>
            @endif
        </div>
        <div style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
            <div style="margin-bottom: 20px;">
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 4px;">{{ __('messages.date') }}</div>
                <div style="font-size: 15px; font-weight: 700; color: #fff;">{{ $quotation->created_at->translatedFormat('d F, Y') }}</div>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 4px;">{{ __('messages.valid_until') }}</div>
                <div style="font-size: 15px; font-weight: 700; color: var(--warning);">{{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->translatedFormat('d F, Y') : '—' }}</div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 60px;">
        {{-- Bill To --}}
        <div>
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 16px; border-bottom: 1px solid var(--glass-border); padding-bottom: 8px;">{{ __('messages.prepared_for') }}</div>
            @if($quotation->company)
                <div style="font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 8px;">{{ $quotation->company->name }}</div>
                @if($quotation->company->address)
                    <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; max-width: 300px;">{{ $quotation->company->address }}</div>
                @endif
            @else
                <div style="color: var(--text-muted); font-style: italic;">—</div>
            @endif
        </div>

        {{-- From (Brand) --}}
        <div style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 16px; border-bottom: 1px solid var(--glass-border); padding-bottom: 8px;">{{ __('messages.from') }}</div>
            <div style="font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 8px;">{{ $settings['company_name'] ?? 'Floor-in' }}</div>
            @if(isset($settings['company_address']))
                <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; max-width: 250px; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: auto;">{{ $settings['company_address'] }}</div>
            @endif
            @if(isset($settings['company_email']))
                <div style="font-size: 14px; color: var(--text-muted); margin-top: 4px;">{{ $settings['company_email'] }}</div>
            @endif
            @if(isset($settings['company_phone']))
                <div style="font-size: 14px; color: var(--text-muted);">{{ $settings['company_phone'] }}</div>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <div style="margin-bottom: 60px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--glass-border);">
                    <th style="padding: 16px 0; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted);">#</th>
                    <th style="padding: 16px 0; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted);">{{ __('messages.product') }}</th>
                    <th style="padding: 16px 0; text-align: center; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted);">{{ __('messages.qty') }}</th>
                    <th style="padding: 16px 0; text-align: center; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted);">{{ __('messages.unit_price') }}</th>
                    <th style="padding: 16px 0; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted);">{{ __('messages.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <td style="padding: 24px 0; font-size: 13px; color: var(--text-muted);">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td style="padding: 24px 0;">
                        <div style="font-weight: 700; color: #fff; font-size: 15px;">{{ $item->product->name ?? '—' }}</div>
                        @if($item->description)
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td style="padding: 24px 0; text-align: center; font-weight: 600; color: #fff;">{{ $item->quantity }}</td>
                    <td style="padding: 24px 0; text-align: center; font-weight: 600; color: #fff;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="padding: 24px 0; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; font-weight: 800; color: #fff;">{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Summary --}}
    <div style="display: flex; justify-content: flex-end;">
        <div style="width: 320px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--text-secondary);">
                <span>{{ __('messages.subtotal') }}</span>
                <span style="font-weight: 700;">{{ number_format($quotation->subtotal, 2) }}</span>
            </div>
            @if($quotation->tax_amount)
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--text-secondary);">
                <span>{{ __('messages.tax') }}</span>
                <span style="font-weight: 700;">{{ number_format($quotation->tax_amount, 2) }}</span>
            </div>
            @endif
            @if($quotation->discount_amount)
            <div style="display: flex; justify-content: space-between; margin-bottom: 24px; font-size: 14px; color: #f87171;">
                <span>{{ __('messages.discount') }}</span>
                <span style="font-weight: 700;">-{{ number_format($quotation->discount_amount, 2) }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; padding-top: 24px; border-top: 1px solid var(--glass-border);">
                <span style="font-size: 16px; font-weight: 800; color: #fff;">{{ __('messages.grand_total') }}</span>
                <div style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                    <div style="font-size: 28px; font-weight: 900; color: var(--brand-cyan); letter-spacing: -1px; line-height: 1;">{{ number_format($quotation->total, 2) }}</div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-top: 4px; text-transform: uppercase; letter-spacing: 1px;">{{ $system_branding['system_currency'] ?? 'EGP' }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($quotation->notes || $quotation->terms)
    <div style="margin-top: 60px; border-top: 1px solid var(--glass-border); padding-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
        @if($quotation->notes)
        <div>
            <h4 style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 12px;">{{ __('messages.notes') }}</h4>
            <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.7; margin: 0;">{!! nl2br(e($quotation->notes)) !!}</p>
        </div>
        @endif
        @if($quotation->terms)
        <div>
            <h4 style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 12px;">{{ __('messages.terms') }}</h4>
            <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.7; margin: 0;">{!! nl2br(e($quotation->terms)) !!}</p>
        </div>
        @endif
    </div>
    @endif
</div>

<style>
    @media print {
        .sidebar, .header, .page-header, .btn { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: none !important; }
        .glass-card { 
            background: white !important; 
            color: #1a1a1a !important; 
            border: 1px solid #eee !important; 
            box-shadow: none !important; 
            padding: 40px !important; 
            border-radius: 0 !important;
            margin: 0 !important;
        }
        h1, h3, h4, div, span, td, th { color: #1a1a1a !important; }
        .badg-count { border: 1px solid #ccc !important; background: transparent !important; color: #1a1a1a !important; }
        tr { border-bottom: 1px solid #eee !important; }
        thead tr { border-bottom: 2px solid #333 !important; }
        body { background: white !important; }
    }
</style>
@endsection
