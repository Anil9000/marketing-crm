<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignStat;
use App\Models\Contact;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $stats = [
            'total_contacts'   => Contact::where('user_id', $userId)->count(),
            'active_campaigns' => Campaign::where('user_id', $userId)->where('status', 'active')->count(),
            'total_campaigns'  => Campaign::where('user_id', $userId)->count(),
            'total_segments'   => Segment::where('user_id', $userId)->count(),
            'avg_open_rate'    => $this->getAverageOpenRate($userId),
            'total_sent'       => CampaignStat::whereHas('campaign', fn($q) => $q->where('user_id', $userId))->sum('sent_count'),
        ];

        $recentCampaigns = Campaign::with('stats')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Chart data: campaigns per month (last 6 months)
        $chartData = $this->getMonthlyChartData($userId);

        return view('dashboard.index', compact('stats', 'recentCampaigns', 'chartData'));
    }

    private function getAverageOpenRate(int $userId): float
    {
        $result = CampaignStat::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
            ->where('sent_count', '>', 0)
            ->selectRaw('AVG(opens / sent_count * 100) as avg_rate')
            ->value('avg_rate');

        return round((float) $result, 1);
    }

    private function getMonthlyChartData(int $userId): array
    {
        $months = collect(range(5, 0))->map(function ($i) {
            return now()->subMonths($i);
        });

        $campaignCounts = Campaign::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("strftime('%Y', created_at) as year, strftime('%m', created_at) as month, COUNT(*) as count")
            ->groupByRaw("strftime('%Y', created_at), strftime('%m', created_at)")
            ->get()
            ->keyBy(fn($r) => "{$r->year}-" . ltrim($r->month, '0'));

        $labels = [];
        $data   = [];

        foreach ($months as $month) {
            $labels[] = $month->format('M Y');
            $key      = $month->format('Y') . '-' . (int) $month->format('n');
            $data[]   = $campaignCounts->get($key)?->count ?? 0;
        }

        return compact('labels', 'data');
    }
}
