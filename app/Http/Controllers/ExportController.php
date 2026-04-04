<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function leads(Request $request)
    {
        $user = Auth::user();
        $query = Lead::with(['company', 'employee']);

        if (!$user->is_admin) {
            $query->where('company_id', $user->company_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $leads = $query->latest()->get();
        $filename = 'leads_' . now()->format('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['#', __('messages.name'), __('messages.phone'), __('messages.email'), __('messages.status'), __('messages.priority'), __('messages.company'), __('messages.added_by'), __('messages.date')]);
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->id, $lead->name, $lead->phone ?? '', $lead->email ?? '',
                    $lead->status_ar, $lead->priority_ar, $lead->company->name ?? '',
                    $lead->employee->name, $lead->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function campaigns(Request $request)
    {
        $user = Auth::user();
        $query = Campaign::with(['company', 'employee']);

        if (!$user->is_admin) {
            $query->where('company_id', $user->company_id);
        }

        $campaigns = $query->latest()->get();
        $filename = 'campaigns_' . now()->format('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($campaigns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['#', __('messages.name'), __('messages.budget'), __('messages.platforms'), __('messages.start_date'), __('messages.end_date'), __('messages.status'), __('messages.reach'), __('messages.impressions'), __('messages.clicks'), __('messages.leads_count')]);
            foreach ($campaigns as $c) {
                fputcsv($file, [
                    $c->id, $c->name, $c->budget . ' ' . $c->currency,
                    $c->platforms_ar, $c->start_date->format('Y-m-d'),
                    $c->end_date?->format('Y-m-d') ?? '', $c->status_ar,
                    $c->reach, $c->impressions, $c->clicks, $c->leads_generated,
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
