<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_number', 
        'bill_id',
        'amount',
        'payment_category',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Bill
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    // Mendapatkan Nomor Kamar dari Bill
    public function getRoomNumberAttribute()
    {
        return $this->bill ? $this->bill->room_number : null;
    }

    // Pastikan user_id diambil dari bill jika kosong
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->user_id && $payment->bill) {
                $payment->user_id = $payment->bill->user_id;
            }
        }); // ✅ Tambahkan tanda kurung kurawal & titik koma yang hilang
    } // ✅ Tambahkan penutup method boot()
}
