<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'description',
        'price',
        'sku',
        'inventory',
        'is_active',
        'last_synced_at',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];
    public function feed()
    {
        return $this->belongsToMany(Feed::class);
    }
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
