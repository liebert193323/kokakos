<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    protected $fillable = [
        'name',
        'number',
        'tenant_id',
        'capacity',
        'status',
        'payment_category',
        'price_per_semester',
        'price_per_year',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Set default name if not provided
        static::creating(function ($room) {
            if (!$room->name) {
                $room->name = $room->number ?? 'Room ' . rand(1000, 9999);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}