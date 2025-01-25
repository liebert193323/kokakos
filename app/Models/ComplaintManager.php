<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintManager extends Model
{
    protected $fillable = ['name', 'email', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}