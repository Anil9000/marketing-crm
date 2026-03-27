<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignStat;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('role', 'admin')->first();
        $manager  = User::where('role', 'marketing_manager')->first();
        $segments = Segment::all();

        $campaigns = [
            [
                'name'        => 'Summer Sale 2025',
                'type'        => 'email',
                'status'      => 'completed',
                'subject'     => 'Up to 50% off — Summer Sale is here!',
                'budget'      => 5000.00,
                'spent'       => 4200.00,
                'sent_at'     => now()->subDays(30),
                'stats'       => ['sent_count' => 12400, 'opens' => 3100, 'clicks' => 620, 'conversions' => 87, 'bounces' => 124, 'unsubscribes' => 18],
            ],
            [
                'name'        => 'Q4 Product Launch',
                'type'        => 'email',
                'status'      => 'active',
                'subject'     => 'Introducing our newest product line',
                'budget'      => 8000.00,
                'spent'       => 1200.00,
                'sent_at'     => now()->subDays(5),
                'stats'       => ['sent_count' => 8900, 'opens' => 2450, 'clicks' => 392, 'conversions' => 54, 'bounces' => 89, 'unsubscribes' => 12],
            ],
            [
                'name'        => 'Newsletter October 2025',
                'type'        => 'email',
                'status'      => 'completed',
                'subject'     => 'Monthly news & updates from the team',
                'budget'      => 1000.00,
                'spent'       => 800.00,
                'sent_at'     => now()->subDays(60),
                'stats'       => ['sent_count' => 15200, 'opens' => 4864, 'clicks' => 912, 'conversions' => 45, 'bounces' => 304, 'unsubscribes' => 67],
            ],
            [
                'name'        => 'SMS Flash Sale',
                'type'        => 'sms',
                'status'      => 'completed',
                'subject'     => null,
                'budget'      => 2000.00,
                'spent'       => 1800.00,
                'sent_at'     => now()->subDays(45),
                'stats'       => ['sent_count' => 5000, 'opens' => 4200, 'clicks' => 1890, 'conversions' => 234, 'bounces' => 50, 'unsubscribes' => 22],
            ],
            [
                'name'        => 'Black Friday Push Notifications',
                'type'        => 'push_notification',
                'status'      => 'scheduled',
                'subject'     => 'Black Friday deals start NOW!',
                'budget'      => 3000.00,
                'spent'       => 0,
                'scheduled_at'=> now()->addDays(30),
                'stats'       => ['sent_count' => 0, 'opens' => 0, 'clicks' => 0, 'conversions' => 0, 'bounces' => 0, 'unsubscribes' => 0],
            ],
            [
                'name'        => 'LinkedIn B2B Campaign',
                'type'        => 'social_media',
                'status'      => 'draft',
                'subject'     => 'Grow your business with Marketing CRM',
                'budget'      => 10000.00,
                'spent'       => 0,
                'stats'       => ['sent_count' => 0, 'opens' => 0, 'clicks' => 0, 'conversions' => 0, 'bounces' => 0, 'unsubscribes' => 0],
            ],
            [
                'name'        => 'Welcome Email Series',
                'type'        => 'email',
                'status'      => 'active',
                'subject'     => 'Welcome aboard! Here\'s how to get started',
                'budget'      => 500.00,
                'spent'       => 120.00,
                'sent_at'     => now()->subDays(10),
                'stats'       => ['sent_count' => 2340, 'opens' => 1872, 'clicks' => 936, 'conversions' => 187, 'bounces' => 23, 'unsubscribes' => 5],
            ],
            [
                'name'        => 'Re-engagement Campaign',
                'type'        => 'email',
                'status'      => 'paused',
                'subject'     => 'We miss you — here\'s 20% off to come back',
                'budget'      => 2000.00,
                'spent'       => 600.00,
                'sent_at'     => now()->subDays(20),
                'stats'       => ['sent_count' => 3200, 'opens' => 448, 'clicks' => 96, 'conversions' => 12, 'bounces' => 64, 'unsubscribes' => 28],
            ],
            [
                'name'        => 'Holiday Season 2025',
                'type'        => 'email',
                'status'      => 'draft',
                'subject'     => 'Happy Holidays from Marketing CRM',
                'budget'      => 6000.00,
                'spent'       => 0,
                'stats'       => ['sent_count' => 0, 'opens' => 0, 'clicks' => 0, 'conversions' => 0, 'bounces' => 0, 'unsubscribes' => 0],
            ],
            [
                'name'        => 'Webinar Invitation',
                'type'        => 'email',
                'status'      => 'completed',
                'subject'     => 'Join our free marketing masterclass',
                'budget'      => 1500.00,
                'spent'       => 1200.00,
                'sent_at'     => now()->subDays(90),
                'stats'       => ['sent_count' => 7800, 'opens' => 3042, 'clicks' => 1092, 'conversions' => 215, 'bounces' => 78, 'unsubscribes' => 31],
            ],
        ];

        foreach ($campaigns as $index => $campaignData) {
            $statsData = $campaignData['stats'];
            unset($campaignData['stats']);

            $campaign = Campaign::create(array_merge($campaignData, [
                'user_id'    => $index % 2 === 0 ? $admin->id : $manager->id,
                'segment_id' => $segments->isNotEmpty() ? $segments->random()->id : null,
                'frequency'  => 'one_time',
                'content'    => $this->getSampleContent($campaignData['type']),
            ]));

            CampaignStat::create(array_merge($statsData, ['campaign_id' => $campaign->id]));
        }

        $this->command->info('10 campaigns seeded.');
    }

    private function getSampleContent(string $type): string
    {
        if ($type === 'email') {
            return '<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
  <div style="background: #6366f1; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
    <h1 style="color: white; margin: 0;">Marketing CRM</h1>
  </div>
  <div style="background: white; padding: 30px; border: 1px solid #e2e8f0;">
    <h2 style="color: #1e293b;">Hello {{ first_name }},</h2>
    <p style="color: #64748b; line-height: 1.6;">Thank you for being a valued subscriber. We have exciting news to share with you.</p>
    <div style="text-align: center; margin: 30px 0;">
      <a href="#" style="background: #6366f1; color: white; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: bold;">Learn More</a>
    </div>
  </div>
  <div style="background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-radius: 0 0 8px 8px;">
    <a href="{{ unsubscribe_url }}" style="color: #94a3b8;">Unsubscribe</a>
  </div>
</body>
</html>';
        }

        return 'Your exclusive deal is waiting. Reply STOP to unsubscribe.';
    }
}
