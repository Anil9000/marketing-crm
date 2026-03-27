<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case Draft     = 'draft';
    case Scheduled = 'scheduled';
    case Active    = 'active';
    case Paused    = 'paused';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft     => 'Draft',
            self::Scheduled => 'Scheduled',
            self::Active    => 'Active',
            self::Paused    => 'Paused',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft     => 'bg-secondary',
            self::Scheduled => 'bg-info',
            self::Active    => 'bg-success',
            self::Paused    => 'bg-warning',
            self::Completed => 'bg-primary',
            self::Cancelled => 'bg-danger',
        };
    }
}
