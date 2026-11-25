<?php

namespace App\Http\Controllers;

use App\Services\AdminReporting;
use App\Support\CampaignAttribution;
use App\Support\ReportPeriod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCampaignExportController
{
    public function __invoke(Request $request, AdminReporting $reports): StreamedResponse
    {
        abort_unless($request->user()->isAdmin() && $request->user()->isActive(), 403);

        $period = ReportPeriod::fromFilters($request->query());
        $source = CampaignAttribution::identifier($request->query('source')) ?? '';
        $campaigns = $reports->campaigns($period, $source);

        return response()->streamDownload(function () use ($campaigns, $period) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xef\xbb\xbf");
            fputcsv($file, ['من', 'إلى', 'المصدر', 'الوسيط', 'الحملة', 'الطلبات الواردة', 'الطلبات المحققة', 'ملغاة أو مرتجعة', 'المبيعات المحققة (ج.م)', 'الإنفاق المسجل (ج.م)', 'أيام الإنفاق المسجلة', 'العائد على الإنفاق', 'تكلفة الطلب المحقق (ج.م)'], escape: '');

            foreach ($campaigns as $campaign) {
                fputcsv($file, [...array_values($period->query()), $campaign->utm_source, $campaign->utm_medium,
                    $campaign->utm_campaign, $campaign->orders_count, $campaign->sales_count, $campaign->lost, $campaign->sales,
                    $campaign->spend, $campaign->spend_days, AdminReporting::ratio((float) $campaign->sales, (float) $campaign->spend),
                    $campaign->spend !== null ? AdminReporting::ratio((float) $campaign->spend, (int) $campaign->sales_count) : null], escape: '');
            }

            fclose($file);
        }, 'orq-campaigns-'.$period->from->toDateString().'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
