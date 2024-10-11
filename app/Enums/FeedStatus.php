<?php

namespace App\Enums;

enum FeedStatus: string
{
    case Pending = 'pending';
    case Synced = 'synced';
    case Failed = 'failed';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Pending',
            self::Synced => 'Synced',
            self::Failed => 'Failed',
        };
    }
}