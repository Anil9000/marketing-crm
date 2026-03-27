<?php

namespace App\Services;

use App\Enums\CampaignStatus;
use App\Models\AbTest;
use App\Models\Campaign;
use App\Models\CampaignStat;
use App\Models\EmailEvent;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use Illuminate\Support\Str;

class CampaignService
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaignRepository
    ) {}

    public function getAllForUser(int $userId, array $filters = [], int $perPage = 15)
    {
        return $this->campaignRepository->allForUser($userId, $filters, $perPage);
    }

    public function create(int $userId, array $data): Campaign
    {
        $data['user_id'] = $userId;

        $campaign = $this->campaignRepository->create($data);

        // Create A/B test record if enabled
        if (!empty($data['ab_test_enabled']) && $data['ab_test_enabled']) {
            AbTest::create([
                'campaign_id'       => $campaign->id,
                'variant_a_subject' => $data['variant_a_subject'] ?? $data['subject'] ?? '',
                'variant_a_content' => $data['variant_a'] ?? $data['content'] ?? '',
                'variant_b_subject' => $data['variant_b_subject'] ?? '',
                'variant_b_content' => $data['variant_b'] ?? '',
            ]);
        }

        return $campaign;
    }

    public function update(Campaign $campaign, array $data): Campaign
    {
        return $this->campaignRepository->update($campaign, $data);
    }

    public function delete(Campaign $campaign): bool
    {
        return $this->campaignRepository->delete($campaign);
    }

    public function sendCampaign(Campaign $campaign): bool
    {
        if (!in_array($campaign->status, [CampaignStatus::Draft, CampaignStatus::Scheduled])) {
            return false;
        }

        // Mark as active and record sent_at
        $campaign->update([
            'status'  => CampaignStatus::Active,
            'sent_at' => now(),
        ]);

        // In a real app this would dispatch a queued job to send emails
        // For now we update stats with placeholder logic
        $segment      = $campaign->segment;
        $contactCount = $segment ? $segment->contact_count : 0;

        CampaignStat::updateOrCreate(
            ['campaign_id' => $campaign->id],
            ['sent_count'  => $contactCount]
        );

        return true;
    }

    public function getCampaignStats(int $campaignId): array
    {
        return $this->campaignRepository->getStatsForCampaign($campaignId);
    }

    public function trackOpen(string $token): void
    {
        $event = EmailEvent::where('tracking_token', $token)
            ->where('event_type', 'sent')
            ->first();

        if (!$event) {
            return;
        }

        // Record open event
        EmailEvent::create([
            'campaign_id'    => $event->campaign_id,
            'contact_id'     => $event->contact_id,
            'event_type'     => 'open',
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'tracking_token' => $token,
        ]);

        // Increment stats
        CampaignStat::where('campaign_id', $event->campaign_id)
            ->increment('opens');
    }

    public function trackClick(string $token): ?string
    {
        $event = EmailEvent::where('tracking_token', $token)->first();

        if (!$event) {
            return null;
        }

        EmailEvent::create([
            'campaign_id'    => $event->campaign_id,
            'contact_id'     => $event->contact_id,
            'event_type'     => 'click',
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'tracking_token' => $token,
            'metadata'       => $event->metadata,
        ]);

        CampaignStat::where('campaign_id', $event->campaign_id)
            ->increment('clicks');

        // Return redirect URL from metadata
        return $event->metadata['url'] ?? null;
    }

    public function getAnalyticsOverview(int $userId): array
    {
        $campaigns = Campaign::where('user_id', $userId)->withCount([])->get();

        return [
            'total_campaigns'    => Campaign::where('user_id', $userId)->count(),
            'active_campaigns'   => Campaign::where('user_id', $userId)->where('status', 'active')->count(),
            'total_contacts'     => \App\Models\Contact::where('user_id', $userId)->count(),
            'avg_open_rate'      => $this->calculateAverageOpenRate($userId),
            'total_sent'         => CampaignStat::whereHas('campaign', fn($q) => $q->where('user_id', $userId))->sum('sent_count'),
            'total_opens'        => CampaignStat::whereHas('campaign', fn($q) => $q->where('user_id', $userId))->sum('opens'),
            'total_clicks'       => CampaignStat::whereHas('campaign', fn($q) => $q->where('user_id', $userId))->sum('clicks'),
            'total_conversions'  => CampaignStat::whereHas('campaign', fn($q) => $q->where('user_id', $userId))->sum('conversions'),
        ];
    }

    private function calculateAverageOpenRate(int $userId): float
    {
        $stats = CampaignStat::whereHas('campaign', fn($q) => $q->where('user_id', $userId))
            ->where('sent_count', '>', 0)
            ->selectRaw('AVG(opens / sent_count * 100) as avg_rate')
            ->value('avg_rate');

        return round((float) $stats, 2);
    }
}
