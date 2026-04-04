<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 2cm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header { border-bottom: 2px solid #6366f1; padding-bottom: 20px; margin-bottom: 20px; }
        .company-info { float: left; width: 50%; }
        .quotation-info { float: right; width: 40%; text-align: right; }
        .clear { clear: both; }
        .bill-to { margin-bottom: 30px; }
        .bill-to h3 { color: #6366f1; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .rtl th, .rtl td { text-align: right; }
        .totals { float: right; width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 5px 0; }
        .totals-row.grand-total { font-size: 16px; font-weight: bold; color: #6366f1; border-top: 2px solid #6366f1; margin-top: 10px; padding-top: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .notes { margin-top: 50px; }
        .notes h4 { color: #6366f1; margin-bottom: 10px; }
    </style>
</head>
<body class="{{ app()->getLocale() == 'ar' ? 'rtl' : '' }}">
    <div class="header">
        <div class="company-info">
            @if(isset($system_branding['system_logo']))
                <img src="{{ public_path('storage/'.$system_branding['system_logo']) }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">
            @endif
            <h2 style="color: #6366f1; margin: 0;">{{ $system_branding['company_name'] ?? $quotation->page->name }}</h2>
            <p style="margin: 0; font-size: 10px; color: #64748b;">
                {{ $system_branding['company_address'] ?? '' }}<br>
                {{ $system_branding['company_phone'] ?? '' }} @if(isset($system_branding['company_email'])) | {{ $system_branding['company_email'] }} @endif
            </p>
        </div>
        <div class="quotation-info">
            <h1 style="margin: 0; color: #6366f1;">{{ __('messages.quotation') }}</h1>
            <p>#{{ $quotation->quotation_number }}</p>
            <p><strong>{{ __('messages.date') }}:</strong> {{ $quotation->created_at->format('Y-m-d') }}</p>
            <p><strong>{{ __('messages.valid_until') }}:</strong> {{ $quotation->valid_until ? $quotation->valid_until->format('Y-m-d') : '-' }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="bill-to">
        <h3>{{ __('messages.bill_to') }}</h3>
        <p><strong>{{ $quotation->company->name ?? '-' }}</strong></p>
        @if($quotation->contact)
            <p>{{ $quotation->contact->name }}</p>
            <p>{{ $quotation->contact->email }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('messages.item') }}</th>
                <th style="text-align: center;">{{ __('messages.quantity') }}</th>
                <th style="text-align: right;">{{ __('messages.unit_price') }}</th>
                <th style="text-align: right;">{{ __('messages.discount') }}</th>
                <th style="text-align: right;">{{ __('messages.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->product->name ?? $item->description }}</strong><br>
                    <small style="color: #64748b;">{{ $item->description }}</small>
                </td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align: right;">{{ $item->discount }}%</td>
                <td style="text-align: right;">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="width: 100%;">
        <div style="float: left; width: 50%;">
            @if($quotation->terms)
            <div class="notes">
                <h4>{{ __('messages.terms') }}</h4>
                <p>{!! nl2br(e($quotation->terms)) !!}</p>
            </div>
            @endif
        </div>
        <div style="float: right; width: 40%;">
            <table style="border: none;">
                <tr style="border: none;">
                    <td style="border: none; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ __('messages.subtotal') }}:</td>
                    <td style="border: none; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ number_format($quotation->subtotal, 2) }} <span dir="ltr">{{ \App\Models\SystemSetting::getCurrencySymbol() }}</span></td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ __('messages.discount') }}:</td>
                    <td style="border: none; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">-{{ number_format($quotation->discount_amount, 2) }} <span dir="ltr">{{ \App\Models\SystemSetting::getCurrencySymbol() }}</span></td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ __('messages.tax') }} ({{ $quotation->tax_rate }}%):</td>
                    <td style="border: none; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ number_format($quotation->tax_amount, 2) }} <span dir="ltr">{{ \App\Models\SystemSetting::getCurrencySymbol() }}</span></td>
                </tr>
                <tr style="background-color: #6366f1; color: white; font-weight: bold;">
                    <td style="padding: 10px; border: none; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ __('messages.total') }}:</td>
                    <td style="padding: 10px; border: none; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ number_format($quotation->total, 2) }} <span dir="ltr">{{ \App\Models\SystemSetting::getCurrencySymbol() }}</span></td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    @if($quotation->notes)
    <div class="notes">
        <h4>{{ __('messages.notes') }}</h4>
        <p>{!! nl2br(e($quotation->notes)) !!}</p>
    </div>
    @endif

    <div class="footer">
        <p>
            {{ $system_branding['company_name'] ?? $quotation->page->name }} 
            @if(isset($system_branding['company_tax_id'])) | {{ __('messages.tax_registration_number') }}: {{ $system_branding['company_tax_id'] }} @endif
            @if(isset($system_branding['company_cr_number'])) | {{ __('messages.commercial_registration') }}: {{ $system_branding['company_cr_number'] }} @endif
        </p>
        <p>© {{ date('Y') }} {{ $system_branding['company_name'] ?? 'Floor-in' }}. {{ __('messages.footer_generated_by') }}</p>
    </div>
</body>
</html>
