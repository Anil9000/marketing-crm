<?php

namespace App\Http\Controllers\Web;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Segment;
use App\Services\CampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignWebController extends Controller
{
    public function __construct(private readonly CampaignService $campaignService) {}

    public function index(Request $request): View
    {
        $campaigns = $this->campaignService->getAllForUser(
            $request->user()->id,
            $request->only(['status', 'type', 'search', 'date_from', 'date_to'])
        );

        $types    = CampaignType::cases();
        $statuses = CampaignStatus::cases();

        return view('campaigns.index', compact('campaigns', 'types', 'statuses'));
    }

    public function create(Request $request): View
    {
        $segments = Segment::where('user_id', $request->user()->id)->get();
        $types    = CampaignType::cases();

        return view('campaigns.create', compact('segments', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required',
            'subject' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'budget'  => 'nullable|numeric|min:0',
        ]);

        $this->campaignService->create($request->user()->id, $request->all());

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    public function show(Request $request, Campaign $campaign): View
    {
        $campaign->load(['stats', 'segment', 'abTest']);
        $stats = $this->campaignService->getCampaignStats($campaign->id);

        return view('campaigns.show', compact('campaign', 'stats'));
    }

    public function edit(Request $request, Campaign $campaign): View
    {
        $segments = Segment::where('user_id', $request->user()->id)->get();
        $types    = CampaignType::cases();
        $statuses = CampaignStatus::cases();

        return view('campaigns.edit', compact('campaign', 'segments', 'types', 'statuses'));
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required',
        ]);

        $this->campaignService->update($campaign, $request->all());

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->campaignService->delete($campaign);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted.');
    }

    public function calendar(Request $request): View
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year',  now()->year);

        // Clamp month to valid range
        if ($month < 1)  { $month = 12; $year--; }
        if ($month > 12) { $month = 1;  $year++; }

        $current = \Carbon\Carbon::create($year, $month, 1);

        $prevDate = $current->copy()->subMonth();
        $nextDate = $current->copy()->addMonth();

        $startOfMonth = $current->copy()->startOfMonth();
        $endOfMonth   = $current->copy()->endOfMonth();

        $campaigns = Campaign::where('user_id', $request->user()->id)
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$startOfMonth, $endOfMonth])
            ->with('segment')
            ->get();

        $campaignsByDate = $campaigns->groupBy(fn($c) => $c->scheduled_at->format('Y-m-d'));

        $upcomingCampaigns = $campaigns->filter(
            fn($c) => $c->scheduled_at->isFuture()
        )->sortBy('scheduled_at')->values();

        return view('campaigns.calendar', [
            'prevMonth'       => $prevDate->month,
            'prevYear'        => $prevDate->year,
            'nextMonth'       => $nextDate->month,
            'nextYear'        => $nextDate->year,
            'currentMonth'    => $current->format('F Y'),
            'calendarYear'    => $year,
            'calendarMonthNum'=> $month,
            'campaignsByDate' => $campaignsByDate,
            'upcomingCampaigns' => $upcomingCampaigns,
        ]);
    }
}
