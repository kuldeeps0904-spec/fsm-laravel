@extends('layouts.app')
@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', 'Invoice')

@section('topbar-actions')
<a href="{{ route('invoices.download', $invoice) }}" class="btn btn-sm btn-danger">
    <i class="bi bi-file-pdf me-1"></i> Download PDF
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                @php 
                    $currentTotal = $invoice->job->total_charges_cost;
                    $isOutOfSync = abs($invoice->subtotal - $currentTotal) > 0.01;
                @endphp

                @if($isOutOfSync)
                <div class="alert alert-warning py-2 mb-4 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Note:</strong> Job charges have changed since this invoice was generated.</span>
                    <a href="{{ route('jobs.show', $invoice->job) }}" class="btn btn-sm btn-warning">Update Invoice</a>
                </div>
                @endif

                <!-- Invoice Header -->
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div style="width:36px;height:36px;background:#6366f1;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-bug text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">FieldServicePro</h5>
                                <small class="text-muted">Pest Control Services</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <h4 class="fw-bold text-primary mb-0">INVOICE</h4>
                        <code>{{ $invoice->invoice_number }}</code>
                        <div class="mt-1">
                            <span class="badge bg-{{ $invoice->status_badge }}">{{ ucfirst($invoice->status) }}</span>
                        </div>
                    </div>
                </div>
                <hr>

                <!-- Bill To / Job Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="text-muted small fw-semibold mb-1">BILL TO</p>
                        <h6 class="mb-0">{{ $invoice->job->client->name }}</h6>
                        <div class="small text-muted">{{ $invoice->job->client->company }}</div>
                        <div class="small text-muted">{{ $invoice->job->client->email }}</div>
                        <div class="small text-muted">{{ $invoice->job->client->phone }}</div>
                        <div class="small text-muted">{{ $invoice->job->client->address }}</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="small"><span class="text-muted">Issued:</span> {{ $invoice->issued_at?->format('d M Y') }}</div>
                        @if($invoice->due_date)
                        <div class="small"><span class="text-muted">Due:</span> {{ $invoice->due_date->format('d M Y') }}</div>
                        @endif
                        <div class="small"><span class="text-muted">Job #:</span> <code>{{ $invoice->job->job_number }}</code></div>
                        <div class="small"><span class="text-muted">Technician:</span> {{ $invoice->job->technician?->user?->name ?? '—' }}</div>
                    </div>
                </div>

                <!-- Job Description -->
                <div class="mb-3 p-3 bg-light rounded">
                    <strong>{{ $invoice->job->title }}</strong>
                    <div class="text-muted small">{{ $invoice->job->service_type }} &bull; {{ $invoice->job->scheduled_at?->format('d M Y') }}</div>
                </div>

                <!-- Charges & Resources Table -->
                @if($invoice->job->chemicals->isNotEmpty())
                <table class="table table-sm table-borderless mb-3">
                    <thead class="border-bottom">
                        <tr>
                            <th class="ps-0">Type</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th class="text-end pe-0">Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->job->chemicals as $item)
                        <tr class="border-bottom-0">
                            <td class="ps-0"><span class="badge bg-light text-muted fw-normal border small">{{ ucfirst(str_replace('_', ' ', $item->type)) }}</span></td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->quantity ?? '—' }}</td>
                            <td>{{ $item->unit ?? '—' }}</td>
                            <td class="text-end pe-0">₹{{ number_format($item->cost, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <!-- Totals -->
                <div class="d-flex justify-content-end">
                    <table style="min-width:260px">
                        <tr><td class="text-muted pe-4">Subtotal</td><td class="text-end">₹{{ number_format($invoice->subtotal, 2) }}</td></tr>
                        <tr><td class="text-muted pe-4">Tax ({{ $invoice->tax_percent }}%)</td><td class="text-end">₹{{ number_format($invoice->tax_amount, 2) }}</td></tr>
                        @if($invoice->discount > 0)
                        <tr><td class="text-muted pe-4">Discount</td><td class="text-end text-danger">-₹{{ number_format($invoice->discount, 2) }}</td></tr>
                        @endif
                        <tr>
                            <td colspan="2"><hr class="my-1"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold pe-4 fs-5">Total</td>
                            <td class="text-end fw-bold fs-5 text-primary">₹{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>

                @if($invoice->notes)
                <hr>
                <p class="text-muted small"><strong>Notes:</strong> {{ $invoice->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
