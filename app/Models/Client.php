<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'company', 'notes'
    ];

    public function jobs()
    {
        return $this->hasMany(ServiceJob::class);
    }

    public function activeJobs()
    {
        return $this->hasMany(ServiceJob::class)->whereIn('status', ['pending', 'in_progress']);
    }
}
