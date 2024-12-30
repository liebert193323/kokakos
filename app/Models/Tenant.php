<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'per_month',
        'price_per_semester',
        'price_per_year'
    ];

    protected $casts = [
        'per_month' => 'boolean',
        'price_per_semester' => 'integer',
        'price_per_year' => 'integer',
    ];

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}