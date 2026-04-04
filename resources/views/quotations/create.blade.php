@extends('layouts.app')

@section('page-title', __('messages.create_quotation'))

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('quotations.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom: 8px;">
            <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
            {{ __('messages.back') }}
        </a>
        <h2>{{ __('messages.create_quotation') }}</h2>
        <p>{{ __('messages.fill_quotation_details') }}</p>
    </div>
</div>

<form action="{{ route('quotations.store') }}" method="POST" id="quotationForm">
    @csrf
    <div class="glass-card-static" style="margin-bottom: 20px;">
        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.deal') }}</label>
                <select name="deal_id" class="form-control">
                    <option value="">{{ __('messages.select_deal') }}</option>
                    @foreach($deals as $deal)
                        <option value="{{ $deal->id }}" {{ old('deal_id') == $deal->id ? 'selected' : '' }}>{{ $deal->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('messages.company') }}</label>
                <select name="customer_id" class="form-control">
                    <option value="">{{ __('messages.select_company') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid-3">
            <div class="form-group">
                <label>{{ __('messages.valid_until') }}</label>
                <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
            </div>
            <div class="form-group">
                <label>{{ __('messages.tax_rate') }} (%)</label>
                <input type="number" name="tax_rate" class="form-control" step="0.01" min="0" max="100" value="{{ old('tax_rate', $settings['system_vat_percentage'] ?? 14) }}" id="taxRate" oninput="calculateTotals()">
            </div>
            <div class="form-group">
                <label>{{ __('messages.discount_rate') }} (%)</label>
                <input type="number" name="discount_rate" class="form-control" step="0.01" min="0" max="100" value="{{ old('discount_rate', 0) }}" id="discountRate" oninput="calculateTotals()">
            </div>
        </div>
        <div class="form-group">
            <label>{{ __('messages.notes') }}</label>
            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>
        <div class="form-group">
            <label>{{ __('messages.terms') }}</label>
            <textarea name="terms" class="form-control">{{ old('terms') }}</textarea>
        </div>
    </div>

    {{-- Items Section --}}
    <div class="glass-card-static" style="margin-bottom: 20px;">
        <div class="page-header" style="margin-bottom: 16px;">
            <h3 style="font-size: 18px;">{{ __('messages.items') }}</h3>
            <button type="button" class="btn btn-ghost btn-sm" onclick="addItem()">
                <i class="fas fa-plus"></i>
                {{ __('messages.add_item') }}
            </button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('messages.product') }}</th>
                        <th>{{ __('messages.description') }}</th>
                        <th style="width: 90px;">{{ __('messages.quantity') }}</th>
                        <th style="width: 120px;">{{ __('messages.unit_price') }}</th>
                        <th style="width: 90px;">{{ __('messages.discount') }}</th>
                        <th style="width: 120px;">{{ __('messages.total') }}</th>
                        <th style="width: 60px;"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr class="item-row">
                        <td>
                            <select name="items[0][product_id]" class="form-control" onchange="fillPrice(this, 0)">
                                <option value="">{{ __('messages.select_product') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="items[0][description]" class="form-control" placeholder="{{ __('messages.description') }}"></td>
                        <td><input type="number" name="items[0][quantity]" class="form-control" min="1" value="1" oninput="calcRowTotal(0)"></td>
                        <td><input type="number" name="items[0][unit_price]" class="form-control" step="0.01" min="0" value="0" oninput="calcRowTotal(0)"></td>
                        <td><input type="number" name="items[0][discount]" class="form-control" step="0.01" min="0" value="0" oninput="calcRowTotal(0)"></td>
                        <td><strong class="row-total">0.00</strong></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm btn-icon" onclick="removeItem(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Totals --}}
    <div class="glass-card-static">
        <div style="display: flex; flex-direction: column; gap: 10px; max-width: 400px; {{ app()->getLocale() == 'ar' ? '' : 'margin-left: auto;' }}">
            <div style="display: flex; justify-content: space-between; font-size: 14px;">
                <span style="color: var(--text-secondary);">{{ __('messages.subtotal') }}</span>
                <strong id="subtotalDisplay">0.00</strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 14px;">
                <span style="color: var(--text-secondary);">{{ __('messages.tax') }} (<span id="taxPercent">0</span>%)</span>
                <strong id="taxDisplay">0.00</strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 14px;">
                <span style="color: var(--text-secondary);">{{ __('messages.discount') }} (<span id="discountPercent">0</span>%)</span>
                <strong id="discountDisplay" style="color: var(--danger);">-0.00</strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 18px; padding-top: 12px; border-top: 1px solid var(--glass-border);">
                <span>{{ __('messages.grand_total') }}</span>
                <strong id="grandTotalDisplay" style="color: var(--primary-light);">0.00</strong>
            </div>
        </div>
        <input type="hidden" name="subtotal" id="subtotalInput" value="0">
        <input type="hidden" name="tax_amount" id="taxInput" value="0">
        <input type="hidden" name="discount_amount" id="discountInput" value="0">
        <input type="hidden" name="total" id="totalInput" value="0">
        <div class="actions" style="justify-content: flex-end; margin-top: 20px;">
            <a href="{{ route('quotations.index') }}" class="btn btn-ghost">{{ __('messages.cancel') }}</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ __('messages.save_quotation') }}
            </button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    let rowIndex = 1;
    const products = @json($products->keyBy('id'));

    function addItem() {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-control" onchange="fillPrice(this, ${rowIndex})">
                    <option value="">{{ __('messages.select_product') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">{{ addslashes($product->name) }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="text" name="items[${rowIndex}][description]" class="form-control" placeholder="{{ __('messages.description') }}"></td>
            <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control" min="1" value="1" oninput="calcRowTotal(${rowIndex})"></td>
            <td><input type="number" name="items[${rowIndex}][unit_price]" class="form-control" step="0.01" min="0" value="0" oninput="calcRowTotal(${rowIndex})"></td>
            <td><input type="number" name="items[${rowIndex}][discount]" class="form-control" step="0.01" min="0" value="0" oninput="calcRowTotal(${rowIndex})"></td>
            <td><strong class="row-total">0.00</strong></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btn-icon" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
    }

    function removeItem(btn) {
        const tbody = document.getElementById('itemsBody');
        if (tbody.querySelectorAll('.item-row').length > 1) {
            btn.closest('tr').remove();
            calculateTotals();
        }
    }

    function fillPrice(select, idx) {
        const option = select.options[select.selectedIndex];
        const price = option.getAttribute('data-price') || 0;
        const row = select.closest('tr');
        row.querySelector('input[name="items[' + idx + '][unit_price]"]').value = price;
        calcRowTotal(idx);
    }

    function calcRowTotal(idx) {
        const row = document.querySelector(`input[name="items[${idx}][quantity]"]`).closest('tr');
        const qty = parseFloat(row.querySelector(`input[name="items[${idx}][quantity]"]`).value) || 0;
        const price = parseFloat(row.querySelector(`input[name="items[${idx}][unit_price]"]`).value) || 0;
        const discount = parseFloat(row.querySelector(`input[name="items[${idx}][discount]"]`).value) || 0;
        const total = (qty * price) - discount;
        row.querySelector('.row-total').textContent = total.toFixed(2);
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.row-total').forEach(el => {
            subtotal += parseFloat(el.textContent) || 0;
        });
        const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
        const discountRate = parseFloat(document.getElementById('discountRate').value) || 0;
        const taxAmount = subtotal * (taxRate / 100);
        const discountAmount = subtotal * (discountRate / 100);
        const grandTotal = subtotal + taxAmount - discountAmount;

        document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2);
        document.getElementById('taxDisplay').textContent = taxAmount.toFixed(2);
        document.getElementById('discountDisplay').textContent = '-' + discountAmount.toFixed(2);
        document.getElementById('grandTotalDisplay').textContent = grandTotal.toFixed(2);
        document.getElementById('taxPercent').textContent = taxRate;
        document.getElementById('discountPercent').textContent = discountRate;

        document.getElementById('subtotalInput').value = subtotal.toFixed(2);
        document.getElementById('taxInput').value = taxAmount.toFixed(2);
        document.getElementById('discountInput').value = discountAmount.toFixed(2);
        document.getElementById('totalInput').value = grandTotal.toFixed(2);
    }
</script>
@endsection
