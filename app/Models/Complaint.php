<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use illuminate\Support\Facades\Auth;

class Complaint extends Model
{
    protected $fillable = [
        'title',
        'description',
        'tenant_name',
        'room_number',
        'photo',
        'status',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($complaint) {
            // Mengatur author_id berdasarkan ID pengguna yang sedang login
            $complaint->user_id = Auth::id();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}