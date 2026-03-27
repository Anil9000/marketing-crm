<?php

namespace App\Enums;

enum CampaignType: string
{
    case Email            = 'email';
    case Sms              = 'sms';
    case PushNotification = 'push_notification';
    case SocialMedia      = 'social_media';

    public function label(): string
    {
        return match($this) {
            self::Email            => 'Email',
            self::Sms              => 'SMS',
            self::PushNotification => 'Push Notification',
            self::SocialMedia      => 'Social Media',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Email            => 'bi-envelope',
            self::Sms              => 'bi-chat-text',
            self::PushNotification => 'bi-bell',
            self::SocialMedia      => 'bi-share',
        };
    }
}
