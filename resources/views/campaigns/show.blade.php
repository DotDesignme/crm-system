@extends('layouts.app')
@section('page-title', __('messages.campaign_analytics') . ': ' . $campaign->name)

@section('content')
<div class="page-header" style="margin-bottom: 30px;">
    <div>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            <a href="{{ route('campaigns.index') }}" class="btn btn-icon" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
                <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
            </a>
            <h2 style="font-size: 28px; font-weight: 800;">{{ $campaign->name }}</h2>
            @php
                $badgeClass = match($campaign->status) {
                    'active' => 'badge-new',
                    'paused' => 'badge-contacted',
                    'completed' => 'badge-converted',
                    default => 'badge-new',
                };
            @endphp
            <span class="badge {{ $badgeClass }}" style="padding: 6px 16px; border-radius: 12px;">{{ __('messages.status_' . $campaign->status) }}</span>
        </div>
        <div style="display: flex; align-items: center; gap: 15px; color: var(--text-muted); font-size: 14px; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 48px;">
            <span><i class="far fa-calendar-alt" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px;"></i> {{ $campaign->start_date ? $campaign->start_date->format('d M Y') : '-' }} - {{ $campaign->end_date ? $campaign->end_date->format('d M Y') : __('messages.ongoing') }}</span>
            <span style="width: 1px; height: 14px; background: var(--glass-border);"></span>
            <span>
                @if($campaign->platforms)
                    @foreach($campaign->platforms as $platform)
                        <i class="fab fa-{{ $platform }}" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; color: var(--brand-cyan);"></i>
                    @endforeach
                @endif
            </span>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <button class="btn btn-ghost" onclick="window.print()">
            <i class="fas fa-print"></i> {{ __('messages.print_report') ?? 'Print Report' }}
        </button>
        <button class="btn btn-primary" onclick="editCampaign({{ $campaign->id }}, {{ $campaign->toJson() }})">
            <i class="fas fa-edit"></i> {{ __('messages.edit') }}
        </button>
    </div>
</div>

{{-- Analytics Grid --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="stat-card fade-in" style="animation-delay: 0.1s;">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: var(--warning);">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-value" style="font-size: 24px;">{{ number_format($campaign->total_spend, 2) }} <span style="font-size: 12px; font-weight: 500;">{{ $campaign->currency }}</span></div>
        <div class="stat-label">{{ __('messages.total_spend') }}</div>
        <div style="margin-top: 10px; font-size: 11px; color: var(--text-muted);">
            {{ __('messages.budget') }}: {{ number_format($campaign->budget, 2) }} {{ $campaign->currency }}
        </div>
    </div>

    <div class="stat-card fade-in" style="animation-delay: 0.2s;">
        <div class="stat-icon" style="background: rgba(14, 165, 233, 0.15); color: var(--brand-cyan);">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="stat-value" style="font-size: 24px;">{{ number_format($campaign->cpl, 2) }} <span style="font-size: 12px; font-weight: 500;">{{ $campaign->currency }}</span></div>
        <div class="stat-label">{{ __('messages.cpl') }}</div>
        <div style="margin-top: 10px; font-size: 11px; color: var(--text-muted);">
            {{ $campaign->leads()->count() }} {{ __('messages.leads') }}
        </div>
    </div>

    <div class="stat-card fade-in" style="animation-delay: 0.3s;">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: var(--success);">
            <i class="fas fa-bullseye"></i>
        </div>
        <div class="stat-value" style="font-size: 24px;">{{ number_format($campaign->cpql, 2) }} <span style="font-size: 12px; font-weight: 500;">{{ $campaign->currency }}</span></div>
        <div class="stat-label">{{ __('messages.cpql') }}</div>
        <div style="margin-top: 10px; font-size: 11px; color: var(--text-muted);">
            {{ $campaign->leads()->where('status', '!=', 'new')->count() }} {{ __('messages.qualified_leads') }}
        </div>
    </div>

    <div class="stat-card fade-in" style="animation-delay: 0.4s;">
        <div class="stat-icon" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
        @php $roi = $campaign->roi @endphp
        <div class="stat-value" style="font-size: 24px; color: {{ $roi >= 0 ? 'var(--success)' : 'var(--danger)' }};">
            {{ number_format($roi, 1) }}%
        </div>
        <div class="stat-label">{{ __('messages.roi') }}</div>
        <div style="margin-top: 10px; font-size: 11px; color: var(--text-muted);">
            {{ __('messages.revenue') ?? 'Revenue' }}: {{ number_format($campaign->deals()->where('status', 'won')->sum('value'), 2) }} {{ $campaign->currency }}
        </div>
    </div>

    <div class="stat-card fade-in" style="animation-delay: 0.5s;">
        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: var(--danger);">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-value" style="font-size: 24px;">{{ number_format($campaign->cac, 2) }} <span style="font-size: 12px; font-weight: 500;">{{ $campaign->currency }}</span></div>
        <div class="stat-label">{{ __('messages.cac') }}</div>
        <div style="margin-top: 10px; font-size: 11px; color: var(--text-muted);">
            {{ $campaign->deals()->where('status', 'won')->count() }} {{ __('messages.won_deals') ?? 'Won Deals' }}
        </div>
    </div>
</div>

<div class="grid-2" style="grid-template-columns: 1.5fr 1fr; gap: 24px; margin-bottom: 30px;">
    {{-- Conversion Funnel --}}
    <div class="glass-card fade-in" style="animation-delay: 0.6s; padding: 30px;">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-filter" style="color: var(--brand-cyan);"></i>
            {{ __('messages.funnel_performance') }}
        </h3>
        <div id="funnelChart" style="min-height: 350px;"></div>
    </div>

    {{-- Details & Notes --}}
    <div class="glass-card fade-in" style="animation-delay: 0.7s; padding: 30px;">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">{{ __('messages.campaign_details') ?? 'Campaign Details' }}</h3>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div>
                <label style="display: block; font-size: 12px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">{{ __('messages.description') }}</label>
                <p style="font-size: 14px; line-height: 1.6; color: var(--text-secondary);">{{ $campaign->description ?: __('messages.no_description') }}</p>
            </div>
            <div class="grid-2">
                <div>
                    <label style="display: block; font-size: 12px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">{{ __('messages.reach') }}</label>
                    <p style="font-size: 18px; font-weight: 700;">{{ number_format($campaign->reach) }}</p>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">{{ __('messages.clicks') }}</label>
                    <p style="font-size: 18px; font-weight: 700;">{{ number_format($campaign->clicks) }}</p>
                </div>
            </div>
            <div class="grid-2">
                <div>
                    <label style="display: block; font-size: 12px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">{{ __('messages.impressions') }}</label>
                    <p style="font-size: 18px; font-weight: 700;">{{ number_format($campaign->impressions) }}</p>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">{{ __('messages.ctr') }}</label>
                    <p style="font-size: 18px; font-weight: 700; color: var(--brand-cyan);">{{ $campaign->ctr }}%</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Attributed Deals Table --}}
<div class="glass-card fade-in" style="animation-delay: 0.8s; padding: 0; overflow: hidden;">
    <div style="padding: 24px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-handshake" style="color: var(--success);"></i>
            {{ __('messages.attributed_deals') }}
        </h3>
        <span class="badge badge-admin" style="padding: 6px 14px;">{{ $deals->count() }} {{ __('messages.deals') }}</span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.deal_name') ?? 'Deal' }}</th>
                    <th>{{ __('messages.customer') ?? 'Customer' }}</th>
                    <th>{{ __('messages.value') ?? 'Value' }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deals as $deal)
                <tr>
                    <td style="font-weight: 600;">{{ $deal->title }}</td>
                    <td>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 13px; font-weight: 600;">{{ $deal->lead->name }}</span>
                            <span style="font-size: 11px; color: var(--text-muted);">{{ $deal->lead->phone }}</span>
                        </div>
                    </td>
                    <td style="font-weight: 700; color: var(--brand-cyan);">{{ number_format($deal->value, 2) }} {{ $campaign->currency }}</td>
                    <td>
                        <span class="badge badge-{{ $deal->status }}" style="padding: 4px 12px; border-radius: 8px;">{{ __('messages.status_' . $deal->status) }}</span>
                    </td>
                    <td style="font-size: 12px; color: var(--text-muted);">{{ $deal->created_at->format('d/m/Y') }}</td>
                    <td style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        <a href="{{ route('deals.show', $deal) }}" class="btn btn-icon" style="background: rgba(255,255,255,0.05); color: var(--brand-cyan);">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 15px; opacity: 0.2;"></i>
                        {{ __('messages.no_attributed_deals') ?? 'No deals attributed to this campaign yet.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Reusing Edit Modal Logic from Index --}}
<div class="modal-overlay" id="editModal">
    <div class="modal" style="max-width: 700px;">
        <div class="modal-header">
            <h3>{{ __('messages.edit_campaign') }}</h3>
            <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('show')">&times;</button>
        </div>
        <form method="POST" id="editForm" action="{{ route('campaigns.update', $campaign) }}">
            @csrf @method('PUT')
            <div class="grid-2">
                <div class="form-group">
                    <label>{{ __('messages.name') }} *</label>
                    <input type="text" name="name" id="edit-name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>{{ __('messages.budget') }}</label>
                    <input type="number" name="budget" id="edit-budget" class="form-control" step="0.01" min="0">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>{{ __('messages.total_spend') }} *</label>
                    <input type="number" name="total_spend" id="edit-total_spend" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>{{ __('messages.currency') }}</label>
                    <select name="currency" id="edit-currency" class="form-control">
                        <option value="EGP">EGP</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                    </select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>{{ __('messages.status') }}</label>
                    <select name="status" id="edit-status" class="form-control">
                        <option value="active">{{ __('messages.status_active') }}</option>
                        <option value="paused">{{ __('messages.status_paused') }}</option>
                        <option value="completed">{{ __('messages.status_completed') }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('messages.start_date') }}</label>
                    <input type="date" name="start_date" id="edit-start_date" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>{{ __('messages.end_date') }}</label>
                <input type="date" name="end_date" id="edit-end_date" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ __('messages.platforms') }}</label>
                <div style="display: flex; flex-wrap: wrap; gap: 16px; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid var(--glass-border);">
                    @foreach(['facebook','instagram','google','tiktok','youtube','twitter','linkedin','snapchat'] as $platform)
                    <label style="display: flex; align-items: center; gap: 6px; font-size: 14px; color: var(--text-secondary); cursor: pointer; margin-bottom: 0;">
                        <input type="checkbox" name="platforms[]" value="{{ $platform }}" id="edit-platform-{{ $platform }}"
                            style="accent-color: var(--primary);">
                        <i class="fab fa-{{ $platform }}" style="color: var(--primary-light);"></i> {{ ucfirst($platform) }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <label>{{ __('messages.description') }}</label>
                <textarea name="description" id="edit-description" class="form-control" rows="3"></textarea>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>{{ __('messages.reach') }}</label>
                    <input type="number" name="reach" id="edit-reach" class="form-control" min="0">
                </div>
                <div class="form-group">
                    <label>{{ __('messages.impressions') }}</label>
                    <input type="number" name="impressions" id="edit-impressions" class="form-control" min="0">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>{{ __('messages.clicks') }}</label>
                    <input type="number" name="clicks" id="edit-clicks" class="form-control" min="0">
                </div>
                <div class="form-group">
                    <label>{{ __('messages.conversions') }}</label>
                    <input type="number" name="conversions" id="edit-conversions" class="form-control" min="0">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px;">
                <i class="fas fa-save"></i>
                {{ __('messages.save') }}
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var options = {
            series: [{
                name: "{{ __('messages.funnel') ?? 'Funnel' }}",
                data: [
                    {{ $funnelData['leads'] }},
                    {{ $funnelData['qualified'] }},
                    {{ $funnelData['interested'] }},
                    {{ $funnelData['converted'] }},
                    {{ $funnelData['won'] }}
                ],
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                background: 'transparent',
                foreColor: '#94a3b8'
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                    barHeight: '60%',
                    isFunnel: true,
                    distributed: true
                },
            },
            colors: [
                '#60a5fa', // Leads
                '#fbbf24', // Qualified
                '#34d399', // Interested
                '#f472b6', // Converted
                '#c084fc'  // Won
            ],
            dataLabels: {
                enabled: true,
                formatter: function (val, opt) {
                    return opt.w.globals.labels[opt.dataPointIndex] + ': ' + val
                },
                dropShadow: { enabled: false },
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                    fontFamily: 'inherit'
                }
            },
            xaxis: {
                categories: [
                    "{{ __('messages.all_leads') ?? 'Total Leads' }}",
                    "{{ __('messages.qualified_leads') }}",
                    "{{ __('messages.interested') }}",
                    "{{ __('messages.converted') }}",
                    "{{ __('messages.won_deals') }}"
                ],
            },
            legend: { show: false },
            grid: {
                show: false
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function(val) {
                        return val + " {{ __('messages.leads') }}"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#funnelChart"), options);
        chart.render();
    });

    function editCampaign(id, data) {
        document.getElementById('editForm').action = '{{ url("campaigns") }}/' + id;
        document.getElementById('edit-name').value = data.name || '';
        document.getElementById('edit-budget').value = data.budget || 0;
        document.getElementById('edit-total_spend').value = data.total_spend || 0;
        document.getElementById('edit-currency').value = data.currency || 'EGP';
        document.getElementById('edit-status').value = data.status || 'active';
        document.getElementById('edit-start_date').value = data.start_date ? data.start_date.substring(0, 10) : '';
        document.getElementById('edit-end_date').value = data.end_date ? data.end_date.substring(0, 10) : '';
        document.getElementById('edit-description').value = data.description || '';
        document.getElementById('edit-reach').value = data.reach || 0;
        document.getElementById('edit-impressions').value = data.impressions || 0;
        document.getElementById('edit-clicks').value = data.clicks || 0;
        document.getElementById('edit-conversions').value = data.conversions || 0;

        var platforms = data.platforms || [];
        ['facebook','instagram','google','tiktok','youtube','twitter','linkedin','snapchat'].forEach(function(p) {
            var cb = document.getElementById('edit-platform-' + p);
            if (cb) cb.checked = platforms.includes(p);
        });

        document.getElementById('editModal').classList.add('show');
    }
</script>
@endsection
