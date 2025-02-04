<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'payment_id',
    'amount',
    'type',
    'date',
    'description',

    ];

    // Relasi ke Payment
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    // Relasi ke User (Tenant)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
