<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignStat;
use App\Models\Contact;
use App\Models\EmailEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsWebController extends Controller
{
    public function index(Request $request): View
    {
        $userId   = $request->user()->id;
        $from     = $request->get('from', now()->subDays(30)->format('Y-m-d'));
        $to       = $request->get('to',   now()->format('Y-m-d'));
        $typeFilter = $request->get('type');

        // Base campaign query closure with optional filters
        $campaignScope = function ($q) use ($userId, $from, $to, $typeFilter) {
            $q->where('user_id', $userId)
              ->whereDate('created_at', '>=', $from)
              ->whereDate('created_at', '<=', $to);
            if ($typeFilter) {
                $q->where('type', $typeFilter);
            }
        };

        $overview = [
            'total_sent'        => CampaignStat::whereHas('campaign', $campaignScope)->sum('sent_count'),
            'total_opens'       => CampaignStat::whereHas('campaign', $campaignScope)->sum('opens'),
            'total_clicks'      => CampaignStat::whereHas('campaign', $campaignScope)->sum('clicks'),
            'total_conversions' => CampaignStat::whereHas('campaign', $campaignScope)->sum('conversions'),
            'total_bounces'     => CampaignStat::whereHas('campaign', $campaignScope)->sum('bounces'),
            'avg_open_rate'     => $this->avgOpenRate($userId, $from, $to, $typeFilter),
            'avg_click_rate'    => $this->avgClickRate($userId, $from, $to, $typeFilter),
            'avg_bounce_rate'   => $this->avgBounceRate($userId, $from, $to, $typeFilter),
        ];

        // Campaign stats table data
        $campaignQuery = Campaign::with('stats')
            ->where('user_id', $userId)
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        if ($typeFilter) {
            $campaignQuery->where('type', $typeFilter);
        }

        $campaignStats = $campaignQuery->latest()->take(10)->get()
            ->map(fn($c) => [
                'campaign'    => $c,
                'sent'        => $c->stats->sent_count  ?? 0,
                'opens'       => $c->stats->opens       ?? 0,
                'clicks'      => $c->stats->clicks      ?? 0,
                'conversions' => $c->stats->conversions ?? 0,
                'open_rate'   => $c->stats && ($c->stats->sent_count ?? 0) > 0
                                    ? round((float) $c->stats->opens / $c->stats->sent_count * 100, 1) : 0,
                'click_rate'  => $c->stats && ($c->stats->sent_count ?? 0) > 0
                                    ? round((float) $c->stats->clicks / $c->stats->sent_count * 100, 1) : 0,
            ]);

        // Timeline data for chart (filtered date range)
        $timeline = EmailEvent::whereHas('campaign', $campaignScope)
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw('DATE(created_at) as date, event_type, COUNT(*) as count')
            ->groupByRaw('DATE(created_at), event_type')
            ->orderBy('date')
            ->get();

        // Type distribution (filtered by date)
        $typeDistQuery = Campaign::where('user_id', $userId)
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        $typeDistribution = $typeDistQuery->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        return view('analytics.index', compact('overview', 'campaignStats', 'timeline', 'typeDistribution'));
    }

    private function avgOpenRate(int $userId, string $from, string $to, ?string $type): float
    {
        $r = CampaignStat::whereHas('campaign', fn($q) => $this->applyCampaignFilters($q, $userId, $from, $to, $type))
            ->where('sent_count', '>', 0)
            ->selectRaw('AVG(CAST(opens AS REAL) / sent_count * 100) as r')
            ->value('r');
        return round((float) $r, 1);
    }

    private function avgClickRate(int $userId, string $from, string $to, ?string $type): float
    {
        $r = CampaignStat::whereHas('campaign', fn($q) => $this->applyCampaignFilters($q, $userId, $from, $to, $type))
            ->where('sent_count', '>', 0)
            ->selectRaw('AVG(CAST(clicks AS REAL) / sent_count * 100) as r')
            ->value('r');
        return round((float) $r, 1);
    }

    private function avgBounceRate(int $userId, string $from, string $to, ?string $type): float
    {
        $r = CampaignStat::whereHas('campaign', fn($q) => $this->applyCampaignFilters($q, $userId, $from, $to, $type))
            ->where('sent_count', '>', 0)
            ->selectRaw('AVG(CAST(bounces AS REAL) / sent_count * 100) as r')
            ->value('r');
        return round((float) $r, 1);
    }

    private function applyCampaignFilters($q, int $userId, string $from, string $to, ?string $type): void
    {
        $q->where('user_id', $userId)
          ->whereDate('created_at', '>=', $from)
          ->whereDate('created_at', '<=', $to);
        if ($type) {
            $q->where('type', $type);
        }
    }
}
