<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = [
        'tenant_id',
        'complaint_manager_id',
        'complaint',
        'status'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function complaintManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'complaint_manager_id');
    }
}