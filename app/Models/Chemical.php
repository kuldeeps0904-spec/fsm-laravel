<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemical extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_job_id', 'type', 'name', 'quantity', 'unit', 'concentration', 'cost', 'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost'     => 'decimal:2',
    ];

    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }
}
