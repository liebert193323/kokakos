<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintResponse extends Model
{
    protected $fillable = [
        'complaint_id',
        'response',
        'status',
        'handler_id',
        'responded_at'
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
}
