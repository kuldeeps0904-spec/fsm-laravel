<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Technician extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'phone', 'specialization', 'license_number', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(ServiceJob::class);
    }

    public function pendingJobs()
    {
        return $this->hasMany(ServiceJob::class)->where('status', 'pending');
    }

    public function getNameAttribute()
    {
        return $this->user->name;
    }
}
