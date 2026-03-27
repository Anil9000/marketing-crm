<?php

namespace App\Repositories;

use App\Models\Campaign;
use App\Models\CampaignStat;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CampaignRepository implements CampaignRepositoryInterface
{
    public function allForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Campaign::with(['stats', 'segment'])
            ->where('user_id', $userId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById(int $id): ?Campaign
    {
        return Campaign::with(['stats', 'segment', 'user', 'abTest'])->find($id);
    }

    public function create(array $data): Campaign
    {
        $data['budget'] = (float) ($data['budget'] ?? 0);
        $data['spent']  = (float) ($data['spent']  ?? 0);

        $campaign = Campaign::create($data);

        // Create empty stats record
        CampaignStat::create(['campaign_id' => $campaign->id]);

        return $campaign->load(['stats', 'segment']);
    }

    public function update(Campaign $campaign, array $data): Campaign
    {
        $campaign->update($data);
        return $campaign->fresh(['stats', 'segment']);
    }

    public function delete(Campaign $campaign): bool
    {
        return $campaign->delete();
    }

    public function getActiveForUser(int $userId): Collection
    {
        return Campaign::with('stats')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get();
    }

    public function getStatsForCampaign(int $campaignId): array
    {
        $stats = CampaignStat::where('campaign_id', $campaignId)->first();

        if (!$stats) {
            return [
                'opens'           => 0,
                'clicks'          => 0,
                'conversions'     => 0,
                'bounces'         => 0,
                'unsubscribes'    => 0,
                'sent_count'      => 0,
                'open_rate'       => 0.0,
                'click_rate'      => 0.0,
                'conversion_rate' => 0.0,
            ];
        }

        return [
            'opens'           => $stats->opens,
            'clicks'          => $stats->clicks,
            'conversions'     => $stats->conversions,
            'bounces'         => $stats->bounces,
            'unsubscribes'    => $stats->unsubscribes,
            'sent_count'      => $stats->sent_count,
            'open_rate'       => $stats->open_rate,
            'click_rate'      => $stats->click_rate,
            'conversion_rate' => $stats->conversion_rate,
        ];
    }
}
