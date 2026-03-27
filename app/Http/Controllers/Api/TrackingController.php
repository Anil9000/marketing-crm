<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CampaignService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TrackingController extends Controller
{
    public function __construct(private readonly CampaignService $campaignService) {}

    /**
     * Tracking pixel endpoint — returns a 1x1 transparent GIF.
     */
    public function open(string $token): Response
    {
        $this->campaignService->trackOpen($token);

        // 1x1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel, 200, [
            'Content-Type'  => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
        ]);
    }

    /**
     * Click tracking — redirect to the original URL after logging the click.
     */
    public function click(string $token): \Illuminate\Http\RedirectResponse
    {
        $url = $this->campaignService->trackClick($token);

        if (!$url) {
            return redirect('/');
        }

        return redirect($url);
    }
}
