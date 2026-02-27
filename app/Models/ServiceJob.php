<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_number', 'client_id', 'technician_id', 'title', 'description',
        'status', 'priority', 'service_type', 'scheduled_at', 'completed_at',
        'latitude', 'longitude', 'location_address', 'technician_notes', 'admin_notes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'latitude'     => 'decimal:7',
        'longitude'    => 'decimal:7',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($job) {
            $year = date('Y');
            $count = static::whereYear('created_at', $year)->count();
            $number = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $job->job_number = "JOB-{$year}-{$number}";
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function images()
    {
        return $this->hasMany(JobImage::class);
    }

    public function beforeImages()
    {
        return $this->hasMany(JobImage::class)->where('type', 'before');
    }

    public function afterImages()
    {
        return $this->hasMany(JobImage::class)->where('type', 'after');
    }

    public function chemicals()
    {
        return $this->hasMany(Chemical::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending'     => 'warning',
            'in_progress' => 'info',
            'completed'   => 'success',
            'cancelled'   => 'danger',
            default       => 'secondary',
        };
    }

    public function getPriorityBadgeAttribute()
    {
        return match($this->priority) {
            'low'    => 'secondary',
            'medium' => 'primary',
            'high'   => 'warning',
            'urgent' => 'danger',
            default  => 'secondary',
        };
    }

    public function getTotalChargesCostAttribute()
    {
        return $this->chemicals->sum('cost');
    }
}
