@extends('layouts.app')

@section('page-title', __('messages.executive_dashboard'))

@section('content')
<div class="container-fluid fade-in">
    <!-- Premium Date Filter (Glass Pill) -->
    <div style="display: flex; justify-content: center; margin-bottom: 40px;">
        <div class="glass-pill" style="display: inline-flex; align-items: center; gap: 16px; padding: 8px 12px; background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border-radius: 100px; border: 1px solid var(--glass-border); box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <form action="{{ route('admin.executive') }}" method="GET" style="display: flex; align-items: center; gap: 12px; margin: 0;">
                <div style="display: flex; align-items: center; gap: 8px; padding: 0 12px;">
                    <i class="fas fa-calendar-alt" style="color: var(--brand-cyan); font-size: 14px;"></i>
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">{{ __('messages.period') ?? 'Report Period' }}</span>
                </div>
                
                <div style="height: 24px; width: 1px; background: var(--glass-border);"></div>

                <div class="date-input-wrapper" style="position: relative;">
                    <input type="date" name="start_date" class="glass-date-input" value="{{ $startDate }}" style="background: transparent; border: none; color: #fff; font-size: 13px; font-weight: 600; padding: 4px 8px; outline: none;">
                </div>
                
                <span style="color: var(--text-muted); font-size: 12px; font-weight: 800;">→</span>

                <div class="date-input-wrapper" style="position: relative;">
                    <input type="date" name="end_date" class="glass-date-input" value="{{ $endDate }}" style="background: transparent; border: none; color: #fff; font-size: 13px; font-weight: 600; padding: 4px 8px; outline: none;">
                </div>

                <button type="submit" class="btn-pill-primary" style="background: var(--brand-cyan); color: #fff; border: none; padding: 8px 20px; border-radius: 100px; font-size: 12px; font-weight: 800; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-sync-alt" style="font-size: 11px;"></i>
                    {{ __('messages.update') ?? 'Update' }}
                </button>
                
                <a href="{{ route('admin.executive') }}" class="btn-pill-ghost" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 100px; background: rgba(255,255,255,0.05); color: var(--text-muted); transition: 0.3s;">
                    <i class="fas fa-redo" style="font-size: 12px;"></i>
                </a>
            </form>
        </div>
    </div>

    <!-- Funnel Hero Transition -->
    <div class="funnel-container" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px;">
        @php
            $funnelSteps = [
                ['label' => __('messages.total_leads'), 'value' => $funnel['leads'], 'icon' => 'fa-users', 'color' => '#60a5fa', 'bg' => 'rgba(96, 165, 250, 0.1)'],
                ['label' => __('messages.meetings') ?? 'Scheduled Viewings', 'value' => $funnel['meetings'], 'icon' => 'fa-calendar-check', 'color' => '#0ea5e9', 'bg' => 'rgba(14, 165, 233, 0.1)'],
                ['label' => __('messages.quotations'), 'value' => $funnel['quotations'], 'icon' => 'fa-file-invoice-dollar', 'color' => '#a855f7', 'bg' => 'rgba(168, 85, 247, 0.1)'],
                ['label' => __('messages.contracts'), 'value' => $funnel['contracts'], 'icon' => 'fa-file-contract', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.1)'],
            ];
        @endphp

        @foreach($funnelSteps as $index => $step)
        <div class="glass-card funnel-card" style="padding: 24px; border-radius: 28px; text-align: center; position: relative;">
            @if($index > 0)
                <div class="funnel-connector" style="position: absolute; {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: -25px; top: 50%; transform: translateY(-50%); z-index: 2; color: var(--glass-border); font-size: 20px;">
                    <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                </div>
            @endif
            
            <div class="funnel-icon" style="width: 50px; height: 50px; background: {{ $step['bg'] }}; color: {{ $step['color'] }}; display: flex; align-items: center; justify-content: center; border-radius: 16px; margin: 0 auto 16px; font-size: 20px; border: 1px solid {{ $step['color'] }}33;">
                <i class="fas {{ $step['icon'] }}"></i>
            </div>
            
            <div style="font-size: 32px; font-weight: 900; color: #fff; letter-spacing: -1px; margin-bottom: 4px;">{{ number_format($step['value']) }}</div>
            <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">{{ $step['label'] }}</div>
            
            @if($index > 0 && isset($funnelSteps[$index-1]['value']) && $funnelSteps[$index-1]['value'] > 0)
                <div style="margin-top: 12px; font-size: 11px; font-weight: 700; color: {{ $step['color'] }}; background: {{ $step['color'] }}15; display: inline-block; padding: 2px 10px; border-radius: 100px;">
                    {{ round(($step['value'] / $funnelSteps[$index-1]['value']) * 100, 1) }}%
                </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="row g-4">
        <!-- Sales Leaderboard (Task 1 & 2) -->
        <div class="col-xl-8">
            <div class="glass-card" style="height: 100%; border-radius: 32px; padding: 24px; position: relative; overflow: hidden;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; background: rgba(255, 215, 0, 0.1); color: #ffd700; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; border: 1px solid rgba(255, 215, 0, 0.2);">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #fff;">{{ __('messages.sales_leaderboard') ?? 'Executive Leaderboard' }}</h3>
                            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">{{ __('messages.top_performers_desc') ?? 'Performance Evaluation' }}</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0 12px;">
                        <thead>
                            <tr style="text-transform: uppercase; font-size: 10px; color: var(--text-muted); font-weight: 800; letter-spacing: 1.5px;">
                                <th style="padding: 0 12px 10px; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ __('messages.rank') ?? 'Rank' }}</th>
                                <th style="padding: 0 12px 10px; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">{{ __('messages.employee') ?? 'Employee' }}</th>
                                <th style="padding: 0 12px 10px; text-align: center;">{{ __('messages.won_deals') }}</th>
                                <th style="padding: 0 12px 10px; text-align: center;">{{ __('messages.conversion_rate') }}</th>
                                <th style="padding: 0 12px 10px; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ __('messages.revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $index => $employee)
                            @php
                                $isFirst = $index === 0;
                                $rowBg = $isFirst ? 'rgba(255, 215, 0, 0.05)' : 'rgba(255,255,255,0.02)';
                                $rowBorder = $isFirst ? 'rgba(255, 215, 0, 0.2)' : 'var(--glass-border)';
                                $rankColor = $isFirst ? '#ffd700' : ($index === 1 ? '#e5e7eb' : ($index === 2 ? '#cd7f32' : 'var(--text-muted)'));
                            @endphp
                            <tr style="background: {{ $rowBg }}; border-radius: 16px; border: 1px solid {{ $rowBorder }}; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.01)'" onmouseout="this.style.transform='scale(1)'">
                                <td style="padding: 16px 12px; border-radius: 16px 0 0 16px;">
                                    <div style="width: 28px; height: 28px; background: {{ $rankColor }}22; color: {{ $rankColor }}; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 14px; border: 1px solid {{ $rankColor }}44;">
                                        {{ $index + 1 }}
                                    </div>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--brand-cyan); font-size: 12px;">
                                            {{ substr($employee->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 800; color: #fff; font-size: 14px;">{{ $employee->name }}</div>
                                            <div style="font-size: 10px; color: var(--text-muted);">{{ $employee->job_title ?? 'Sales Expert' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 800; border: 1px solid #10b98133;">
                                        {{ $employee->won_deals_count }}
                                    </span>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <div style="font-weight: 800; color: var(--brand-cyan); font-size: 14px;">{{ $employee->conversion_rate }}%</div>
                                    <div class="progress" style="height: 4px; width: 60px; background: rgba(255,255,255,0.05); border-radius: 10px; margin: 4px auto 0;">
                                        <div class="progress-bar" style="width: {{ $employee->conversion_rate }}%; background: var(--brand-cyan); border-radius: 10px;"></div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; border-radius: 0 16px 16px 0;">
                                    <div style="font-weight: 900; color: #fff; font-size: 15px;">{{ number_format($employee->won_revenue, 2) }}</div>
                                    <div style="font-size: 10px; color: var(--text-muted); font-weight: 700;">{{ $system_branding['system_currency_symbol'] }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Lead Leakage / Loss Report -->
        <div class="col-xl-4">
            <div class="glass-card" style="height: 100%; border-radius: 32px; padding: 24px;">
                <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 30px;">
                    <div style="width: 44px; height: 44px; background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; border: 1px solid rgba(239, 68, 68, 0.2);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #fff;">{{ __('messages.leakage_report') ?? 'Leakage Analysis' }}</h3>
                        <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">{{ __('messages.why_we_lose') ?? 'Loss Reasons' }}</div>
                    </div>
                </div>

                <div style="height: 240px; position: relative;">
                    <canvas id="lossChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                        <div style="font-size: 24px; font-weight: 900; color: #fff;">{{ $lossStats->sum('deals_count') }}</div>
                        <div style="font-size: 10px; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Lost Deals</div>
                    </div>
                </div>

                <div style="margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    @foreach($lossStats->take(4) as $stat)
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); padding: 12px; border-radius: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">{{ Str::limit($stat->reason, 12) }}</span>
                            <span style="font-size: 12px; font-weight: 900; color: #fff;">{{ $stat->deals_count }}</span>
                        </div>
                        <div class="progress" style="height: 3px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                            <div class="progress-bar" style="width: {{ ($stat->deals_count / max($lossStats->sum('deals_count'), 1)) * 100 }}%; background: #ef4444; border-radius: 10px;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign ROI Matrix (Task 3) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="glass-card" style="border-radius: 32px; padding: 24px; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; background: rgba(14, 165, 233, 0.1); color: var(--brand-cyan); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; border: 1px solid rgba(14, 165, 233, 0.2);">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #fff;">{{ __('messages.campaign_matrix') ?? 'Marketing Performance Matrix' }}</h3>
                            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">{{ __('messages.roi_analysis') ?? 'ROI & Lead Quality' }}</div>
                        </div>
                    </div>

                    @if($goldenCampaign)
                    <div class="golden-banner" style="background: linear-gradient(135deg, rgba(255,215,0,0.1), rgba(255,215,0,0.05)); border: 1px solid rgba(255,215,0,0.2); padding: 8px 20px; border-radius: 100px; display: flex; align-items: center; gap: 10px; box-shadow: 0 0 20px rgba(255,215,0,0.1);">
                        <i class="fas fa-crown" style="color: #ffd700; font-size: 14px;"></i>
                        <span style="font-size: 12px; font-weight: 800; color: #ffd700; text-transform: uppercase; letter-spacing: 1px;">{{ __('messages.golden_campaign') ?? 'Golden' }}: {{ $goldenCampaign->name }}</span>
                    </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                        <thead>
                            <tr style="background: rgba(255,255,255,0.02);">
                                <th style="padding: 20px 16px; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 2px solid var(--glass-border); border-radius: 16px 0 0 0;">{{ __('messages.campaign') }}</th>
                                <th style="padding: 20px 16px; text-align: center; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 2px solid var(--glass-border);">{{ __('messages.spend') }}</th>
                                <th style="padding: 20px 16px; text-align: center; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 2px solid var(--glass-border);">{{ __('messages.leads') }}</th>
                                <th style="padding: 20px 16px; text-align: center; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 2px solid var(--glass-border);">CPL / CAC</th>
                                <th style="padding: 20px 16px; text-align: center; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 2px solid var(--glass-border);">{{ __('messages.revenue') }}</th>
                                <th style="padding: 20px 16px; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 2px solid var(--glass-border); border-radius: 0 16px 0 0;">ROI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaigns as $camp)
                            @php
                                $isGolden = $goldenCampaign && $camp->id == $goldenCampaign->id;
                            @endphp
                            <tr style="transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 16px; border-bottom: 1px solid var(--glass-border);">
                                    <div style="font-weight: 800; color: #fff; font-size: 14px;">{{ $camp->name }}</div>
                                    <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase;">{{ $camp->platform }}</div>
                                </td>
                                <td style="padding: 16px; text-align: center; border-bottom: 1px solid var(--glass-border);">
                                    <div style="font-weight: 700; color: #fff; font-size: 13px;">{{ number_format($camp->total_spend, 2) }}</div>
                                    <div style="font-size: 9px; color: var(--text-muted);">{{ $system_branding['system_currency_symbol'] }}</div>
                                </td>
                                <td style="padding: 16px; text-align: center; border-bottom: 1px solid var(--glass-border);">
                                    <div style="font-weight: 800; color: var(--brand-cyan); font-size: 14px;">{{ $camp->total_leads }}</div>
                                    <div style="font-size: 9px; color: var(--text-muted);">Total Leads</div>
                                </td>
                                <td style="padding: 16px; text-align: center; border-bottom: 1px solid var(--glass-border);">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                        <span class="badge" style="background: rgba(255,255,255,0.05); color: #fff; font-size: 10px;">CPL: {{ number_format($camp->cpl, 2) }}</span>
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 10px;">CAC: {{ number_format($camp->cac, 2) }}</span>
                                    </div>
                                </td>
                                <td style="padding: 16px; text-align: center; border-bottom: 1px solid var(--glass-border);">
                                    <div style="font-weight: 900; color: #fff; font-size: 14px;">{{ number_format($camp->won_revenue, 2) }}</div>
                                    <div style="font-size: 9px; color: var(--text-muted);">{{ $system_branding['system_currency_symbol'] }}</div>
                                </td>
                                <td style="padding: 16px; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; border-bottom: 1px solid var(--glass-border);">
                                    <div style="background: {{ $camp->roi >= 0 ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $camp->roi >= 0 ? '#10b981' : '#f87171' }}; padding: 6px 12px; border-radius: 8px; font-size: 14px; font-weight: 900; display: inline-block; border: 1px solid {{ $camp->roi >= 0 ? '#10b98133' : '#ef444433' }}; min-width: 80px; text-align: center;">
                                        {{ $camp->roi }}%
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('lossChart').getContext('2d');
        const lossData = @json($lossStats);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: lossData.map(s => s.reason),
                datasets: [{
                    data: lossData.map(s => s.deals_count),
                    backgroundColor: [
                        '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#10b981', '#06b6d4', '#3b82f6', '#6366f1', '#a855f7', '#ec4899'
                    ],
                    borderWidth: 0,
                    hoverOffset: 15,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false
                    }
                }
            }
        });
    });
</script>

<style>
    .glass-pill:hover { border-color: var(--brand-cyan) !important; box-shadow: 0 10px 40px rgba(14, 165, 233, 0.2) !important; }
    .glass-date-input::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }
    .funnel-card:hover { transform: translateY(-5px); border-color: rgba(255,255,255,0.15) !important; }
    
    [dir="rtl"] .funnel-connector { transform: translateY(-50%) rotate(180deg); }
    
    .progress {
        background: rgba(255,255,255,0.05) !important;
        overflow: hidden;
    }
    
    /* Animation for the star campaign */
    @keyframes pulse-golden {
        0% { box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(255, 215, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 215, 0, 0); }
    }
    .golden-banner { animation: pulse-golden 2s infinite; }
</style>
@endsection
