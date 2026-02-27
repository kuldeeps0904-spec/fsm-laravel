@extends('layouts.app')
@section('title', $client->name)
@section('page-title', $client->name)

@section('topbar-actions')
<a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-warning text-white">
    <i class="bi bi-pencil me-1"></i> Edit
</a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="user-avatar" style="width:50px;height:50px;font-size:1.2rem;border-radius:12px;background:#6366f1;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">
                        {{ strtoupper(substr($client->name,0,2)) }}
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $client->name }}</h5>
                        <small class="text-muted">{{ $client->company ?? 'Individual' }}</small>
                    </div>
                </div>
                <hr>
                <div class="d-flex flex-column gap-2">
                    @if($client->phone)
                    <div><i class="bi bi-telephone me-2 text-primary"></i>{{ $client->phone }}</div>
                    @endif
                    @if($client->email)
                    <div><i class="bi bi-envelope me-2 text-primary"></i>{{ $client->email }}</div>
                    @endif
                    @if($client->address)
                    <div><i class="bi bi-geo-alt me-2 text-primary"></i>{{ $client->address }}</div>
                    @endif
                </div>
                @if($client->notes)
                <hr>
                <p class="text-muted small mb-0"><i class="bi bi-sticky me-1"></i>{{ $client->notes }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-briefcase me-2 text-primary"></i>Jobs ({{ $client->jobs->count() }})</span>
                @role('admin')
                <a href="{{ route('jobs.create') }}?client_id={{ $client->id }}" class="btn btn-sm btn-primary">+ New Job</a>
                @endrole
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Job #</th><th>Title</th><th>Technician</th><th>Status</th><th>Invoice</th><th></th></tr></thead>
                    <tbody>
                        @forelse($client->jobs as $job)
                        <tr>
                            <td><code class="small">{{ $job->job_number }}</code></td>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->technician?->user?->name ?? '—' }}</td>
                            <td><span class="badge status-{{ $job->status }}">{{ ucfirst(str_replace('_',' ',$job->status)) }}</span></td>
                            <td>
                                @if($job->invoice)
                                <span class="badge bg-{{ $job->invoice->status_badge }}">{{ ucfirst($job->invoice->status) }}</span>
                                @else <span class="text-muted">None</span>@endif
                            </td>
                            <td><a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No jobs yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
