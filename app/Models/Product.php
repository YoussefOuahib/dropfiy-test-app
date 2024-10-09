<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'sku',
        'inventory',
        'is_active',
        'last_synced_at',
        'sync_status'
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
}
