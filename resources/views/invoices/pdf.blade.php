<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 1.5cm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 20px; margin-bottom: 20px; }
        .company-info { float: left; width: 50%; }
        .invoice-info { float: right; width: 40%; text-align: right; }
        .clear { clear: both; }
        .bill-to { margin-bottom: 25px; }
        .bill-to h3 { color: #10b981; border-bottom: 1px solid #eee; padding-bottom: 5px; font-size: 14px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        th { background-color: #f9fafb; border-bottom: 2px solid #e5e7eb; padding: 10px; text-align: left; color: #374151; font-weight: bold; }
        td { padding: 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .rtl th, .rtl td { text-align: right; }
        .totals-container { float: right; width: 300px; }
        .totals-table { border: none; margin-bottom: 0; }
        .totals-table td { border: none; padding: 4px 10px; }
        .grand-total-row { background-color: #10b981; color: white; font-weight: bold; font-size: 14px; }
        .grand-total-row td { padding: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #9ca3af; font-size: 9px; border-top: 1px solid #f3f4f6; padding-top: 10px; }
        .notes-section { margin-top: 30px; }
        .notes-section h4 { color: #10b981; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-unpaid { background-color: #fee2e2; color: #991b1b; }
        .status-partial { background-color: #fef3c7; color: #92400e; }
    </style>
</head>
<body class="{{ app()->getLocale() == 'ar' ? 'rtl' : '' }}">
    <div class="header">
        <div class="company-info">
            @if(isset($system_branding['system_logo']))
                <img src="{{ public_path('storage/'.$system_branding['system_logo']) }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">
            @endif
            <h2 style="color: #10b981; margin: 0; font-size: 20px;">{{ $system_branding['company_name'] ?? ($invoice->page->name ?? config('app.name')) }}</h2>
            <p style="margin: 4px 0; font-size: 10px; color: #6b7280;">
                {{ $system_branding['company_address'] ?? '' }}<br>
                {{ $system_branding['company_phone'] ?? '' }} @if(isset($system_branding['company_email'])) | {{ $system_branding['company_email'] }} @endif
            </p>
        </div>
        <div class="invoice-info">
            <h1 style="margin: 0; color: #10b981; font-size: 24px;">{{ __('messages.invoice') }}</h1>
            <p style="margin: 4px 0; font-weight: bold;">#{{ $invoice->invoice_number }}</p>
            <p style="margin: 2px 0;"><strong>{{ __('messages.date') }}:</strong> {{ $invoice->created_at->format('Y-m-d') }}</p>
            @if($invoice->due_date)
            <p style="margin: 2px 0;"><strong>{{ __('messages.due_date') }}:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') }}</p>
            @endif
            <div style="margin-top: 8px;">
                @php
                    $statusClass = 'status-' . ($invoice->status ?? 'unpaid');
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ __('messages.' . ($invoice->status ?? 'unpaid')) }}</span>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="bill-to">
        <h3>{{ __('messages.bill_to') }}</h3>
        <p style="margin: 2px 0; font-size: 13px; font-weight: bold;">{{ $invoice->company->name ?? '-' }}</p>
        @if($invoice->company && $invoice->company->address)
            <p style="margin: 2px 0; color: #4b5563;">{{ $invoice->company->address }}</p>
        @endif
        @if($invoice->deal && $invoice->deal->contact)
            <p style="margin: 2px 0; color: #4b5563;">{{ $invoice->deal->contact->name }}</p>
            <p style="margin: 2px 0; color: #4b5563;">{{ $invoice->deal->contact->email }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40%;">{{ __('messages.service') }}</th>
                <th style="text-align: center;">{{ __('messages.quantity') }}</th>
                <th style="text-align: right;">{{ __('messages.unit_price') }}</th>
                <th style="text-align: right;">{{ __('messages.discount') }}</th>
                <th style="text-align: right;">{{ __('messages.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>
                    <div style="font-weight: bold;">{{ $item->service->name ?? $item->description }}</div>
                    @if($item->description && $item->description != ($item->service->name ?? ''))
                        <div style="color: #6b7280; font-size: 10px; margin-top: 2px;">{{ $item->description }}</div>
                    @endif
                </td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align: right;">{{ number_format($item->discount ?? 0, 2) }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="width: 100%;">
        <div style="float: left; width: 55%;">
            @if($invoice->notes)
            <div class="notes-section">
                <h4>{{ __('messages.notes') }}</h4>
                <div style="color: #4b5563; font-size: 10px;">{!! nl2br(e($invoice->notes)) !!}</div>
            </div>
            @endif
            @if($invoice->terms)
            <div class="notes-section">
                <h4>{{ __('messages.terms_and_conditions') }}</h4>
                <div style="color: #4b5563; font-size: 10px;">{!! nl2br(e($invoice->terms)) !!}</div>
            </div>
            @endif
        </div>
        <div style="float: right; width: 40%;">
            <table class="totals-table">
                <tr>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ __('messages.subtotal') }}:</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ number_format($invoice->subtotal, 2) }} <span dir="ltr">{{ \App\Models\SystemSetting::getCurrencySymbol() }}</span></td>
                </tr>
                @if($invoice->discount_amount > 0)
                <tr>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ __('messages.discount') }}:</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; color: #dc2626;">-{{ number_format($invoice->discount_amount, 2) }} <span dir="ltr">{{ \App\Models\SystemSetting::getCurrencySymbol() }}</span></td>
                </tr>
                @endif
                @if($invoice->tax_amount > 0)
                <tr>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ __('messages.tax') }} ({{ $invoice->tax_rate }}%):</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ number_format($invoice->tax_amount, 2) }} <span dir="ltr">{{ \App\Models\SystemSetting::getCurrencySymbol() }}</span></td>
                </tr>
                @endif
                <tr class="grand-total-row">
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ __('messages.total') }}:</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ number_format($invoice->total, 2) }} <span dir="ltr">{{ \App\Models\SystemSetting::getCurrencySymbol() }}</span></td>
                </tr>
                @if($invoice->paid_amount > 0)
                <tr>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; padding-top: 10px;">{{ __('messages.paid_amount') }}:</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; padding-top: 10px; color: #059669;">{{ number_format($invoice->paid_amount, 2) }} <span dir="ltr">{{ \App\Models\SystemSetting::getCurrencySymbol() }}</span></td>
                </tr>
                <tr>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; font-weight: bold; border-top: 1px solid #eee;">{{ __('messages.balance_due') }}:</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; font-weight: bold; border-top: 1px solid #eee; color: #dc2626;">{{ number_format($invoice->total - $invoice->paid_amount, 2) }} <span dir="ltr">{{ \App\Models\SystemSetting::getCurrencySymbol() }}</span></td>
                </tr>
                @endif
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <div class="footer">
        <p>
            {{ $system_branding['company_name'] ?? ($invoice->page->name ?? __('messages.app_name')) }} 
            @if(isset($system_branding['company_tax_id'])) | {{ __('messages.tax_registration_number') }}: {{ $system_branding['company_tax_id'] }} @endif
            @if(isset($system_branding['company_cr_number'])) | {{ __('messages.commercial_registration') }}: {{ $system_branding['company_cr_number'] }} @endif
        </p>
        <p>&copy; {{ date('Y') }} {{ $system_branding['company_name'] ?? 'Floor-in' }}. All Rights Reserved</p>
    </div>
</body>
</html>
