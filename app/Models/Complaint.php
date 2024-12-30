<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    // Relasi ke penghuni (Many to One)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'user_id', 'keluhan', 'status',
    ];
}
