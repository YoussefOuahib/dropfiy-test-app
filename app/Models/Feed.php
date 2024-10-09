<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Feed extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'last_synced_at', 'last_submitted_at'];
    
    protected $casts = [
        'last_submitted_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopeNeedsSubmission($query)
    {
        $submissionThreshold = config('google_merchant.submission_threshold', 24);
        return $query->where(function($q) use ($submissionThreshold) {
            $q->whereNull('last_submitted_at')
              ->orWhere('last_submitted_at', '<=', now()->subHours($submissionThreshold));
        });
    }

    public function markAsSubmitted(): void
    {
        $this->update(['last_submitted_at' => now()]);
    }

    public function markAsSynced(): void
    {
        $this->update(['last_synced_at' => now()]);
    }
}