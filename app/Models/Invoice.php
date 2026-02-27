<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_job_id', 'invoice_number', 'subtotal', 'tax_percent',
        'tax_amount', 'discount', 'total_amount', 'status', 'notes',
        'due_date', 'issued_at', 'pdf_path'
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'tax_percent'  => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'discount'     => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date'     => 'date',
        'issued_at'    => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($invoice) {
            $invoice->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            $invoice->issued_at = now();
        });
    }

    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft'   => 'secondary',
            'sent'    => 'info',
            'paid'    => 'success',
            'overdue' => 'danger',
            default   => 'secondary',
        };
    }
}
