<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignStat;
use App\Models\Contact;
use App\Models\EmailEvent;
use App\Services\CampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct(private readonly CampaignService $campaignService) {}

    /**
     * Overall analytics overview for the authenticated user.
     */
    public function overview(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $data = $this->campaignService->getAnalyticsOverview($userId);

        // Monthly campaigns trend (last 6 months)
        $trend = Campaign::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("strftime('%Y', created_at) as year, strftime('%m', created_at) as month, COUNT(*) as count")
            ->groupByRaw("strftime('%Y', created_at), strftime('%m', created_at)")
            ->orderByRaw('year ASC, month ASC')
            ->get()
            ->map(fn($row) => [
                'label' => date('M Y', mktime(0, 0, 0, (int)$row->month, 1, (int)$row->year)),
                'count' => $row->count,
            ]);

        $data['monthly_trend'] = $trend;

        // Contacts growth (last 6 months)
        $contactGrowth = Contact::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("strftime('%Y', created_at) as year, strftime('%m', created_at) as month, COUNT(*) as count")
            ->groupByRaw("strftime('%Y', created_at), strftime('%m', created_at)")
            ->orderByRaw('year ASC, month ASC')
            ->get()
            ->map(fn($row) => [
                'label' => date('M Y', mktime(0, 0, 0, (int)$row->month, 1, (int)$row->year)),
                'count' => $row->count,
            ]);

        $data['contact_growth'] = $contactGrowth;

        return response()->json(['data' => $data]);
    }

    /**
     * Campaign-level analytics breakdown.
     */
    public function campaigns(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $campaigns = Campaign::where('user_id', $userId)
            ->with('stats')
            ->whereHas('stats', fn($q) => $q->where('sent_count', '>', 0))
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($campaign) => [
                'id'              => $campaign->id,
                'name'            => $campaign->name,
                'type'            => $campaign->type->value,
                'status'          => $campaign->status->value,
                'sent_count'      => $campaign->stats->sent_count ?? 0,
                'opens'           => $campaign->stats->opens       ?? 0,
                'clicks'          => $campaign->stats->clicks      ?? 0,
                'conversions'     => $campaign->stats->conversions ?? 0,
                'open_rate'       => $campaign->open_rate,
                'click_rate'      => $campaign->click_rate,
                'sent_at'         => $campaign->sent_at?->toISOString(),
            ]);

        // Campaign type distribution
        $typeDistribution = Campaign::where('user_id', $userId)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(fn($row) => [$row->type => $row->count]);

        return response()->json([
            'data' => [
                'campaigns'         => $campaigns,
                'type_distribution' => $typeDistribution,
            ],
        ]);
    }

    /**
     * Email-specific engagement analytics.
     */
    public function emails(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Aggregate email event counts
        $events = EmailEvent::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
            ->selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->pluck('count', 'event_type');

        // Email events over time (last 30 days)
        $timeline = EmailEvent::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, event_type, COUNT(*) as count')
            ->groupByRaw('DATE(created_at), event_type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        // Geographic distribution (top 10 countries based on IP - placeholder)
        $geographic = EmailEvent::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
            ->where('event_type', 'open')
            ->selectRaw('ip_address, COUNT(*) as count')
            ->groupBy('ip_address')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        return response()->json([
            'data' => [
                'event_totals' => $events,
                'timeline'     => $timeline,
                'geographic'   => $geographic,
                'funnel'       => [
                    'sent'        => $events['sent']        ?? 0,
                    'opened'      => $events['open']        ?? 0,
                    'clicked'     => $events['click']       ?? 0,
                    'converted'   => 0, // Placeholder for conversion tracking
                    'unsubscribed'=> $events['unsubscribe'] ?? 0,
                ],
            ],
        ]);
    }
}
