<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Campaign;

class AbTestSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = Campaign::all();

        if ($campaigns->isEmpty()) {
            return;
        }

        $campaignIds = $campaigns->pluck('id');

        $abTests = [
            [
                'campaign_id'       => $campaignIds->get(0),
                'variant_a_subject' => 'Summer Sale — Up to 50% Off Everything',
                'variant_a_content' => $this->buildContent(
                    'Save big this summer with our exclusive sale.',
                    'Shop the Sale'
                ),
                'variant_b_subject' => '☀️ Summer Sale — Up to 50% Off Everything!',
                'variant_b_content' => $this->buildContent(
                    '☀️ Don\'t miss out — our biggest summer sale is live now!',
                    'Grab Your Deal Now ☀️'
                ),
                'winner'            => 'b',
                'variant_a_opens'   => 1240,
                'variant_b_opens'   => 1580,
                'variant_a_clicks'  => 248,
                'variant_b_clicks'  => 364,
            ],
            [
                'campaign_id'       => $campaignIds->get(1, $campaignIds->first()),
                'variant_a_subject' => 'Introducing our newest product line',
                'variant_a_content' => $this->buildContent(
                    'We are excited to introduce our brand-new product line.',
                    'Learn More'
                ),
                'variant_b_subject' => 'Have you seen our newest products yet?',
                'variant_b_content' => $this->buildContent(
                    'Our newest products are flying off the shelves — see what all the fuss is about.',
                    'See Products Now'
                ),
                'winner'            => 'b',
                'variant_a_opens'   => 980,
                'variant_b_opens'   => 1020,
                'variant_a_clicks'  => 147,
                'variant_b_clicks'  => 214,
            ],
            [
                'campaign_id'       => $campaignIds->get(2, $campaignIds->first()),
                'variant_a_subject' => 'Your loyalty rewards are waiting — claim them now',
                'variant_a_content' => $this->buildContent(
                    'You have loyalty rewards available. Log in to claim them.',
                    'Claim Rewards'
                ),
                'variant_b_subject' => '🎁 Your rewards are expiring soon!',
                'variant_b_content' => $this->buildContent(
                    '🎁 Heads up — your loyalty rewards expire at the end of the month. Don\'t let them go to waste!',
                    'Use My Rewards'
                ),
                'winner'            => null,
                'variant_a_opens'   => 620,
                'variant_b_opens'   => 890,
                'variant_a_clicks'  => 93,
                'variant_b_clicks'  => 178,
            ],
            [
                'campaign_id'       => $campaignIds->get(0),
                'variant_a_subject' => 'Check out our latest updates',
                'variant_a_content' => $this->buildContent(
                    'Here is what\'s new at our platform this month.',
                    'View Updates'
                ),
                'variant_b_subject' => '{{first_name}}, check out our latest updates',
                'variant_b_content' => $this->buildContent(
                    'Hi {{first_name}}, here is what\'s new — personalised just for you.',
                    'View My Updates'
                ),
                'winner'            => null,
                'variant_a_opens'   => 0,
                'variant_b_opens'   => 0,
                'variant_a_clicks'  => 0,
                'variant_b_clicks'  => 0,
            ],
        ];

        foreach ($abTests as $test) {
            DB::table('ab_tests')->insert(array_merge($test, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Build minimal HTML email content for a variant.
     */
    private function buildContent(string $body, string $cta): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;max-width:600px;margin:40px auto;color:#333;">
  <p>{$body}</p>
  <p style="text-align:center;margin:30px 0;">
    <a href="#" style="background:#6366f1;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;">{$cta}</a>
  </p>
  <p style="font-size:12px;color:#888;text-align:center;">
    <a href="#">Unsubscribe</a>
  </p>
</body>
</html>
HTML;
    }
}
