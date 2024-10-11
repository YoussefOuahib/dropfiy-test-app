<?php

namespace App\Models;

use App\Enums\FeedStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Feed extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'last_synced_at', 'status'];
    
    protected $casts = [
        'last_synced_at' => 'datetime',
        'status' => FeedStatus::class,

    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
    public function isPending(): bool
    {
        return $this->status === FeedStatus::Pending;
    }

    public function isSynced(): bool
    {
        return $this->status === FeedStatus::Synced;
    }

    public function hasFailed(): bool
    {
        return $this->status === FeedStatus::Failed;
    }
    
    public function markAsSynced(): void
    {
        $this->update(['last_synced_at' => Carbon::now(),'status' => FeedStatus::Synced]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => FeedStatus::Failed]);
    }
}