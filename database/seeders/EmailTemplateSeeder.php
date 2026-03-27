<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // email_templates table has: id, user_id, name, category, subject, html_content, thumbnail, is_public, timestamps, soft_deletes
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->warn('No admin user found — skipping EmailTemplateSeeder.');
            return;
        }

        $templates = [
            [
                'user_id'      => $admin->id,
                'name'         => 'Welcome Email',
                'subject'      => 'Welcome to {{company_name}} — Let\'s Get Started!',
                'category'     => 'onboarding',
                'html_content' => $this->welcomeTemplate(),
                'is_public'    => true,
                'thumbnail'    => null,
            ],
            [
                'user_id'      => $admin->id,
                'name'         => 'Monthly Newsletter',
                'subject'      => '{{month}} Newsletter — What\'s New at {{company_name}}',
                'category'     => 'newsletter',
                'html_content' => $this->newsletterTemplate(),
                'is_public'    => true,
                'thumbnail'    => null,
            ],
            [
                'user_id'      => $admin->id,
                'name'         => 'Promotional Offer',
                'subject'      => 'Exclusive Offer for You — {{discount}}% Off This Week Only!',
                'category'     => 'promotional',
                'html_content' => $this->promotionalTemplate(),
                'is_public'    => true,
                'thumbnail'    => null,
            ],
            [
                'user_id'      => $admin->id,
                'name'         => 'Re-engagement Campaign',
                'subject'      => 'We Miss You, {{first_name}} — Here\'s What You\'ve Missed',
                'category'     => 're-engagement',
                'html_content' => $this->reengagementTemplate(),
                'is_public'    => false,
                'thumbnail'    => null,
            ],
            [
                'user_id'      => $admin->id,
                'name'         => 'Event Invitation',
                'subject'      => 'You\'re Invited — {{event_name}} on {{event_date}}',
                'category'     => 'event',
                'html_content' => $this->eventTemplate(),
                'is_public'    => true,
                'thumbnail'    => null,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('email_templates')->insert(array_merge($template, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('5 email templates seeded.');
    }

    private function welcomeTemplate(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
  .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
  .header { background: #6366f1; padding: 40px 30px; text-align: center; }
  .header h1 { color: #fff; margin: 0; font-size: 28px; }
  .body { padding: 30px; color: #333; line-height: 1.6; }
  .btn { display: inline-block; background: #6366f1; color: #fff; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-weight: bold; margin: 20px 0; }
  .footer { background: #f4f4f4; padding: 20px 30px; text-align: center; font-size: 12px; color: #888; }
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Welcome to {{company_name}}!</h1>
    </div>
    <div class="body">
      <p>Hi {{first_name}},</p>
      <p>We're absolutely thrilled to have you join us. Your account is all set up and ready to go.</p>
      <ul>
        <li>Complete your profile</li>
        <li>Explore our features</li>
        <li>Connect with your team</li>
      </ul>
      <p style="text-align:center;"><a href="{{login_url}}" class="btn">Get Started Now</a></p>
      <p>Best regards,<br>The {{company_name}} Team</p>
    </div>
    <div class="footer">
      <p>&copy; 2024 {{company_name}}. All rights reserved.</p>
      <p><a href="#">Unsubscribe</a></p>
    </div>
  </div>
</body>
</html>
HTML;
    }

    private function newsletterTemplate(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
  .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
  .header { background: #0f172a; padding: 30px; }
  .header h1 { color: #6366f1; margin: 0; font-size: 22px; }
  .header span { color: #94a3b8; font-size: 14px; display: block; margin-top: 4px; }
  .hero { background: #6366f1; padding: 40px 30px; color: #fff; text-align: center; }
  .hero h2 { font-size: 26px; margin: 0 0 10px; }
  .body { padding: 30px; color: #333; line-height: 1.7; }
  .article { border-bottom: 1px solid #e5e7eb; padding: 20px 0; }
  .article h3 { color: #1e293b; margin: 0 0 8px; }
  .btn { display: inline-block; background: #6366f1; color: #fff; padding: 10px 22px; border-radius: 6px; text-decoration: none; font-size: 14px; }
  .footer { background: #f4f4f4; padding: 20px 30px; text-align: center; font-size: 12px; color: #888; }
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>{{company_name}}</h1>
      <span>{{month}} Newsletter</span>
    </div>
    <div class="hero">
      <h2>What's New This Month</h2>
      <p>Your monthly roundup of updates, tips, and news.</p>
    </div>
    <div class="body">
      <p>Hi {{first_name}},</p>
      <div class="article">
        <h3>Feature Spotlight</h3>
        <p>{{content}}</p>
        <a href="#" class="btn">Read More</a>
      </div>
      <div class="article">
        <h3>Community Highlights</h3>
        <p>See what our community has been building this month.</p>
        <a href="#" class="btn">Explore</a>
      </div>
    </div>
    <div class="footer">
      <p>&copy; 2024 {{company_name}}. <a href="#">Unsubscribe</a></p>
    </div>
  </div>
</body>
</html>
HTML;
    }

    private function promotionalTemplate(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
  .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
  .hero { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); padding: 50px 30px; text-align: center; color: #fff; }
  .discount-badge { font-size: 72px; font-weight: 900; line-height: 1; }
  .body { padding: 30px; color: #333; text-align: center; }
  .promo-box { background: #f3f4f6; border: 2px dashed #6366f1; border-radius: 8px; padding: 15px; margin: 20px 0; }
  .promo-code { font-size: 28px; font-weight: bold; color: #6366f1; letter-spacing: 4px; }
  .btn { display: inline-block; background: #6366f1; color: #fff; padding: 14px 36px; border-radius: 6px; text-decoration: none; font-weight: bold; }
  .footer { background: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #888; }
</style>
</head>
<body>
  <div class="container">
    <div class="hero">
      <div class="discount-badge">{{discount}}%</div>
      <p>Exclusive Offer — This Week Only!</p>
    </div>
    <div class="body">
      <p>Hi {{first_name}}, use this code at checkout:</p>
      <div class="promo-box">
        <div class="promo-code">{{promo_code}}</div>
      </div>
      <a href="{{shop_url}}" class="btn">Shop Now</a>
      <p style="color:#ef4444;font-size:13px;">Expires: {{expiry_date}}</p>
    </div>
    <div class="footer">
      <p>&copy; 2024 {{company_name}}. <a href="#">Unsubscribe</a></p>
    </div>
  </div>
</body>
</html>
HTML;
    }

    private function reengagementTemplate(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
  .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
  .header { background: #1e293b; padding: 40px 30px; text-align: center; }
  .header h1 { color: #fff; margin: 0 0 8px; }
  .header p { color: #94a3b8; margin: 0; }
  .body { padding: 30px; color: #333; }
  .btn-row { text-align: center; padding: 20px 0; }
  .btn { display: inline-block; background: #6366f1; color: #fff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: bold; }
  .footer { background: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #888; }
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>We Miss You, {{first_name}}!</h1>
      <p>Here's what you've been missing</p>
    </div>
    <div class="body">
      <p>{{updates}}</p>
      <div class="btn-row">
        <a href="{{login_url}}" class="btn">Come Back</a>
      </div>
    </div>
    <div class="footer">
      <p>&copy; 2024 {{company_name}}. <a href="#">Unsubscribe</a></p>
    </div>
  </div>
</body>
</html>
HTML;
    }

    private function eventTemplate(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
  .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
  .header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 50px 30px; text-align: center; color: #fff; }
  .invite-label { font-size: 12px; font-weight: 600; letter-spacing: 3px; text-transform: uppercase; opacity: 0.8; }
  .header h1 { font-size: 28px; margin: 10px 0; }
  .body { padding: 30px; color: #333; text-align: center; }
  .btn { display: inline-block; background: #6366f1; color: #fff; padding: 14px 36px; border-radius: 6px; text-decoration: none; font-weight: bold; }
  .footer { background: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #888; }
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="invite-label">You're Invited</div>
      <h1>{{event_name}}</h1>
      <p>{{event_date}} &bull; {{event_location}}</p>
    </div>
    <div class="body">
      <p>Hi {{first_name}}, join us for <strong>{{event_name}}</strong>.</p>
      <p><a href="{{rsvp_url}}" class="btn">RSVP Now</a></p>
    </div>
    <div class="footer">
      <p>&copy; 2024 {{company_name}}. <a href="#">Unsubscribe</a></p>
    </div>
  </div>
</body>
</html>
HTML;
    }
}
