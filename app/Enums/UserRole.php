<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin            = 'admin';
    case MarketingManager = 'marketing_manager';
    case Viewer           = 'viewer';

    public function label(): string
    {
        return match($this) {
            self::Admin            => 'Administrator',
            self::MarketingManager => 'Marketing Manager',
            self::Viewer           => 'Viewer',
        };
    }

    public function canManageCampaigns(): bool
    {
        return in_array($this, [self::Admin, self::MarketingManager]);
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
