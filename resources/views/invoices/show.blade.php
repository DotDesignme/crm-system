@extends('layouts.app')

@section('page-title', $invoice->invoice_number)

@section('content')
<div class="page-header" style="margin-bottom: 30px;">
    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="{{ route('invoices.index') }}" class="btn btn-icon btn-sm" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
            <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        </a>
        <div>
            <h2 class="text-glow" style="font-size: 26px; font-weight: 800; letter-spacing: -0.5px;">{{ $invoice->invoice_number }}</h2>
            <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                <span style="font-size: 13px; color: var(--text-muted);">{{ __('messages.status') }}:</span>
                @php
                    $statusColor = 'var(--brand-cyan)';
                    if($invoice->status == 'paid') $statusColor = 'var(--success)';
                    if($invoice->status == 'overdue') $statusColor = '#f87171';
                @endphp
                <span class="badg-count" style="background: {{ $statusColor }}22; color: {{ $statusColor }}; border-color: {{ $statusColor }}44;">
                    {{ __('messages.' . $invoice->status) }}
                </span>
            </div>
        </div>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-ghost">
            <i class="fas fa-file-pdf" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: #f87171;"></i> {{ __('messages.download_pdf') }}
        </a>
        <button class="btn btn-ghost" onclick="window.print()">
            <i class="fas fa-print" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: var(--text-secondary);"></i> {{ __('messages.print') }}
        </button>
        @if($invoice->status !== 'paid')
        <button class="btn btn-primary" onclick="openPaymentModal()" style="padding: 12px 24px;">
            <i class="fas fa-money-bill-wave" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i>
            {{ __('messages.record_payment') }}
        </button>
        @endif
    </div>
</div>

<div class="glass-card" style="max-width: 1000px; padding: 60px; border-radius: 32px; border: 1px solid var(--glass-border); margin-bottom: 40px; position: relative; overflow: hidden;">
    {{-- Decorative Background Element --}}
    <div style="position: absolute; top: -100px; right: -100px; width: 300px; height: 300px; background: radial-gradient(circle, var(--brand-cyan) 0%, transparent 70%); opacity: 0.05; pointer-events: none;"></div>

    {{-- Invoice Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 60px;">
        <div style="display: flex; align-items: center; gap: 20px;">
            <div class="logo-text">
                @if(isset($system_branding['system_logo']))
                    <img src="{{ asset('storage/'.$system_branding['system_logo']) }}" style="max-height: 50px;">
                @else
                    <i class="{{ $system_branding['system_icon'] ?? 'fas fa-layer-group' }} text-glow" style="font-size: 24px; color: var(--brand-cyan);"></i>
                    <span style="font-weight: 800; font-size: 24px; letter-spacing: -1px; color: #fff;">{{ $system_branding['system_name'] ?? config('app.name') }}</span>
                @endif
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 3px; color: var(--brand-cyan); margin-bottom: 12px;">{{ __('messages.invoice') }}</div>
                <h1 style="font-size: 42px; font-weight: 900; letter-spacing: -1.5px; color: #fff; margin: 0; line-height: 1;">{{ $invoice->invoice_number }}</h1>
            </div>
        </div>
        <div style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
            <div style="margin-bottom: 20px;">
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 4px;">{{ __('messages.issue_date') }}</div>
                <div style="font-size: 15px; font-weight: 700; color: #fff;">{{ $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->translatedFormat('d F, Y') : $invoice->created_at->translatedFormat('d F, Y') }}</div>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 4px;">{{ __('messages.due_date') }}</div>
                <div style="font-size: 15px; font-weight: 700; color: #f87171;">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->translatedFormat('d F, Y') : '—' }}</div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 60px;">
        {{-- Bill To --}}
        <div>
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 16px; border-bottom: 1px solid var(--glass-border); padding-bottom: 8px;">{{ __('messages.bill_to') }}</div>
            @if($invoice->company)
                <div style="font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 8px;">{{ $invoice->company->name }}</div>
                @if($invoice->company->address)
                    <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; max-width: 300px;">{{ $invoice->company->address }}</div>
                @endif
                @if($invoice->company->phone)
                    <div style="font-size: 14px; color: var(--text-muted); margin-top: 8px;"><i class="fas fa-phone" style="font-size: 10px; margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ $invoice->company->phone }}</div>
                @endif
            @else
                <div style="color: var(--text-muted); font-style: italic;">—</div>
            @endif
        </div>

        {{-- From (Brand) --}}
        <div style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 16px; border-bottom: 1px solid var(--glass-border); padding-bottom: 8px;">{{ __('messages.from') }}</div>
            <div style="font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 8px;">{{ $system_branding['company_name'] ?? 'Floor-in' }}</div>
            @if(isset($system_branding['company_address']))
                <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; max-width: 250px; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: auto;">{{ $system_branding['company_address'] }}</div>
            @endif
            @if(isset($system_branding['company_email']))
                <div style="font-size: 14px; color: var(--text-muted); margin-top: 4px;">{{ $system_branding['company_email'] }}</div>
            @endif
            @if(isset($system_branding['company_phone']))
                <div style="font-size: 14px; color: var(--text-muted);">{{ $system_branding['company_phone'] }}</div>
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
                @foreach($invoice->items as $index => $item)
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

    {{-- Summary & Progress --}}
    <div style="display: grid; grid-template-columns: 1fr 320px; gap: 60px; align-items: end;">
        <div>
            {{-- Payment Progress --}}
            <div style="background: rgba(0,0,0,0.2); padding: 24px; border-radius: 20px; border: 1px solid var(--glass-border);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted);">{{ __('messages.payment_progress') }}</span>
                    <span style="font-size: 12px; font-weight: 800; color: var(--brand-cyan);">{{ number_format($paidPct, 1) }}%</span>
                </div>
                <div style="height: 8px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; margin-bottom: 16px;">
                    <div style="width: {{ $paidPct }}%; height: 100%; background: linear-gradient(90deg, var(--brand-blue), var(--brand-cyan)); box-shadow: 0 0 10px var(--brand-cyan); border-radius: 10px;"></div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase;">{{ __('messages.paid') }}</div>
                        <div style="font-weight: 800; color: var(--success); font-size: 15px;">{{ number_format($invoice->paid_amount ?? 0, 2) }}</div>
                    </div>
                    <div style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase;">{{ __('messages.balance') }}</div>
                        <div style="font-weight: 800; color: #f87171; font-size: 15px;">{{ number_format($invoice->total - ($invoice->paid_amount ?? 0), 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--text-secondary);">
                <span>{{ __('messages.subtotal') }}</span>
                <span style="font-weight: 700;">{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if($invoice->tax_amount)
            <div style="display: flex; justify-content: space-between; margin-bottom: 24px; font-size: 14px; color: var(--text-secondary);">
                <span>{{ __('messages.tax') }}</span>
                <span style="font-weight: 700;">{{ number_format($invoice->tax_amount, 2) }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; padding-top: 24px; border-top: 1px solid var(--glass-border);">
                <span style="font-size: 16px; font-weight: 800; color: #fff;">{{ __('messages.grand_total') }}</span>
                <div style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                    <div style="font-size: 28px; font-weight: 900; color: var(--brand-cyan); letter-spacing: -1px; line-height: 1;">{{ number_format($invoice->total, 2) }}</div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-top: 4px; text-transform: uppercase; letter-spacing: 1px;">{{ $invoice->currency ?? $system_branding['system_currency'] ?? 'EGP' }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($invoice->notes || $invoice->terms)
    <div style="margin-top: 60px; border-top: 1px solid var(--glass-border); padding-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
        @if($invoice->notes)
        <div>
            <h4 style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 12px;">{{ __('messages.notes') }}</h4>
            <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.7; margin: 0;">{!! nl2br(e($invoice->notes)) !!}</p>
        </div>
        @endif
        @if($invoice->terms)
        <div>
            <h4 style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 12px;">{{ __('messages.terms') }}</h4>
            <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.7; margin: 0;">{!! nl2br(e($invoice->terms)) !!}</p>
        </div>
        @endif
    </div>
    @endif
</div>

{{-- Payment Modal --}}
<div class="modal-overlay" id="paymentModal">
    <div class="modal" style="max-width: 440px; border: 1px solid var(--glass-border);">
        <div class="modal-header">
            <h3 class="text-glow">{{ __('messages.record_payment') }}</h3>
            <button class="modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <form action="{{ route('invoices.payment', $invoice) }}" method="POST">
            @csrf
            <div class="form-group">
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: block;">{{ __('messages.amount') }}</label>
                <div style="position: relative;">
                    <input type="number" name="amount" class="form-control form-control-recessed" step="0.01" min="0.01" max="{{ $invoice->total - ($invoice->paid_amount ?? 0) }}" value="{{ $invoice->total - ($invoice->paid_amount ?? 0) }}" style="font-size: 20px; font-weight: 800; color: var(--brand-cyan); padding-right: 60px;" required>
                    <span style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-weight: 700; opacity: 0.5;">{{ $invoice->currency ?? $system_branding['system_currency'] ?? 'EGP' }}</span>
                </div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 10px; background: rgba(0,0,0,0.2); padding: 8px 12px; border-radius: 8px; width: fit-content;">
                    {{ __('messages.balance_due') }}: <strong style="color: #f87171;">{{ number_format($invoice->total - ($invoice->paid_amount ?? 0), 2) }}</strong>
                </div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px;">
                <button type="button" class="btn btn-ghost" onclick="closePaymentModal()">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">
                    <i class="fas fa-check" style="margin-right: 8px;"></i>
                    {{ __('messages.confirm_payment') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @media print {
        .sidebar, .header, .page-header, .btn, .modal-overlay { display: none !important; }
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
        .text-glow { text-shadow: none !important; }
        h1, h3, h4, div, span, td, th { color: #1a1a1a !important; }
        .badg-count { border: 1px solid #ccc !important; background: transparent !important; color: #1a1a1a !important; }
        tr { border-bottom: 1px solid #eee !important; }
        thead tr { border-bottom: 2px solid #333 !important; }
        .brand-cyan { color: #0891b2 !important; }
        body { background: white !important; }
    }
</style>
@endsection

@section('scripts')
<script>
    function openPaymentModal() {
        document.getElementById('paymentModal').classList.add('show');
    }
    function closePaymentModal() {
        document.getElementById('paymentModal').classList.remove('show');
    }
    document.getElementById('paymentModal').addEventListener('click', function(e) {
        if (e.target === this) closePaymentModal();
    });
</script>
@endsection
