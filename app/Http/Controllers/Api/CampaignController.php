<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\StoreCampaignRequest;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CampaignStatResource;
use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CampaignController extends Controller
{
    public function __construct(private readonly CampaignService $campaignService) {}

    /**
     * List campaigns with filters and pagination.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $campaigns = $this->campaignService->getAllForUser(
            $request->user()->id,
            $request->only(['status', 'type', 'search', 'date_from', 'date_to']),
            (int) $request->get('per_page', 15)
        );

        return CampaignResource::collection($campaigns);
    }

    /**
     * Create a new campaign.
     */
    public function store(StoreCampaignRequest $request): JsonResponse
    {
        $campaign = $this->campaignService->create($request->user()->id, $request->validated());

        return response()->json(new CampaignResource($campaign), 201);
    }

    /**
     * Show a single campaign.
     */
    public function show(Campaign $campaign): JsonResponse
    {
        $this->authorizeOwner($campaign);

        $campaign->load(['stats', 'segment', 'abTest', 'user']);

        return response()->json(new CampaignResource($campaign));
    }

    /**
     * Update a campaign.
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign): JsonResponse
    {
        $this->authorizeOwner($campaign);

        $updated = $this->campaignService->update($campaign, $request->validated());

        return response()->json(new CampaignResource($updated));
    }

    /**
     * Delete a campaign.
     */
    public function destroy(Campaign $campaign): JsonResponse
    {
        $this->authorizeOwner($campaign);

        $this->campaignService->delete($campaign);

        return response()->json(['message' => 'Campaign deleted successfully.']);
    }

    /**
     * Get campaign statistics.
     */
    public function stats(Campaign $campaign): JsonResponse
    {
        $this->authorizeOwner($campaign);

        $stats = $this->campaignService->getCampaignStats($campaign->id);

        return response()->json(['data' => $stats]);
    }

    /**
     * Send / activate a campaign.
     */
    public function send(Campaign $campaign): JsonResponse
    {
        $this->authorizeOwner($campaign);

        $success = $this->campaignService->sendCampaign($campaign);

        if (!$success) {
            return response()->json([
                'message' => 'Campaign cannot be sent in its current status.',
            ], 422);
        }

        return response()->json([
            'message'  => 'Campaign has been activated and is being sent.',
            'campaign' => new CampaignResource($campaign->fresh(['stats'])),
        ]);
    }

    private function authorizeOwner(Campaign $campaign): void
    {
        if ($campaign->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have access to this campaign.');
        }
    }
}
