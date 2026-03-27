<?php

namespace App\Repositories\Interfaces;

use App\Models\Campaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CampaignRepositoryInterface
{
    public function allForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Campaign;
    public function create(array $data): Campaign;
    public function update(Campaign $campaign, array $data): Campaign;
    public function delete(Campaign $campaign): bool;
    public function getActiveForUser(int $userId): Collection;
    public function getStatsForCampaign(int $campaignId): array;
}
