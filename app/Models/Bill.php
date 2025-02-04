<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'user_id',  // Ganti tenant_id jadi user_id
        'room_number', // Tambah field untuk nomor kamar
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
            if (empty($bill->amount)) {
                $user = User::find($bill->user_id);
                if ($user) {
                    // Set amount berdasarkan kategori pembayaran user
                    $bill->amount = $user->per_month ? 
                        $user->price_per_semester : 
                        $user->price_per_year;
                    $bill->payment_category = $user->per_month ? 'semester' : 'year';

                    // Ambil dan set nomor kamar
                    $room = Room::where('user_id', $user->id)->first();
                    if ($room) {
                        $bill->room_number = $room->number;
                    }
                }
            }
        });
    }

    // Relasi ke User (bukan ke Tenant)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tambahan relasi ke Room jika diperlukan
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_number', 'number');
        return $this->room ? $this->room->number : null;
    }

    public function payments()
{
    return $this->hasMany(Payment::class, 'bill_id'); // Pastikan 'bill_id' sesuai dengan foreign key di tabel payments
}
    
}