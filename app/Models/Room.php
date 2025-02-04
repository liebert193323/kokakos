<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Room extends Model
{
    protected $fillable = [
        'user_id',
        'number',
        'capacity',
        'status',
        'payment_category',
        'price_per_semester',
        'price_per_year',
        'rent_start_date',
        'rent_end_date'
    ];

    protected $casts = [
        'rent_start_date' => 'datetime',
        'rent_end_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($room) {
            $room->status = $room->user_id ? 'occupied' : 'available';

        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function scopeActiveBills(Builder $query)
    {
        return $query->where('status', 'occupied')
            ->whereNotNull('rent_end_date')
            ->where('rent_end_date', '>', now())
            ->whereNotNull('user_id');
    }

    public function scopeExpiredBills(Builder $query)
    {
        return $query->where('status', 'occupied')
            ->whereNotNull('rent_end_date')
            ->where('rent_end_date', '<=', now())
            ->whereNotNull('user_id');
    }

    public function shouldShowInBills(): bool
    {
        return $this->status === 'occupied' 
            && $this->rent_end_date 
            && $this->rent_end_date->isFuture() 
            && $this->user_id;
    }

    public function getRemainingDays(): int
    {
        return $this->rent_end_date
            ? max(0, now()->diffInDays($this->rent_end_date, false))
            : 0;
    }

    public function getFormattedRentStartDateAttribute()
    {
        return $this->rent_start_date ? $this->rent_start_date->format('d-m-Y') : '-';
    }

    public function getFormattedRentEndDateAttribute()
    {
        return $this->rent_end_date ? $this->rent_end_date->format('d-m-Y') : '-';
    }
}
