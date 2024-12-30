<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
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
                $tenant = Tenant::find($bill->tenant_id);
                if ($tenant) {
                    $bill->amount = $tenant->per_month ? 
                        $tenant->price_per_semester : 
                        $tenant->price_per_year;
                    $bill->payment_category = $tenant->per_month ? 'semester' : 'year';
                }
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}