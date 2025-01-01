<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant; // Pastikan import Tenant

class Income extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'bill_id', 'amount'];

    // Relasi dengan Tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Relasi dengan Bill
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
