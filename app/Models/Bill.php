<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'room_number', 
        'description',
        'amount',
        'status',
        'payment_category',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            if (empty($bill->amount) && $bill->user) {
                $bill->amount = $bill->user->per_month 
                    ? $bill->user->price_per_semester 
                    : $bill->user->price_per_year;

                $bill->payment_category = $bill->user->per_month ? 'semester' : 'year';

                if ($bill->user->rooms->isNotEmpty()) {
                    $bill->room_number = $bill->user->rooms->first()->number;
                }
            }
        });
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Room
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_number', 'number');
    }

    // Relasi ke Payment
    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }
}
