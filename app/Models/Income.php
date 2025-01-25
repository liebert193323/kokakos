<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model 
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'payment_id', // Ganti bill_id menjadi payment_id sesuai resource
        'amount',
        'type',     // Tambahkan field type
        'date',     // Tambahkan field date
        'description' // Tambahkan field description
    ];

    // Tambahkan casting untuk date
    protected $casts = [
        'date' => 'datetime'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payment()  // Ganti relasi dari bill ke payment
    {
        return $this->belongsTo(Payment::class);
    }
}