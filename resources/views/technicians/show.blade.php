@extends('layouts.app')
@section('title', $technician->user->name)
@section('page-title', $technician->user->name)

@section('topbar-actions')
<a href="{{ route('technicians.edit', $technician) }}" class="btn btn-sm btn-warning text-white">
    <i class="bi bi-pencil me-1"></i> Edit
</a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="user-avatar mx-auto mb-3" style="width:56px;height:56px;font-size:1.3rem;border-radius:50%;background:#6366f1;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">
                    {{ strtoupper(substr($technician->user->name,0,2)) }}
                </div>
                <h5>{{ $technician->user->name }}</h5>
                <p class="text-muted small">{{ $technician->specialization ?? 'Technician' }}</p>
                @if($technician->is_active)
                <span class="badge bg-success">Active</span>
                @else
                <span class="badge bg-secondary">Inactive</span>
                @endif
                <hr>
                <div class="text-start d-flex flex-column gap-2 small">
                    <div><i class="bi bi-envelope me-2 text-primary"></i>{{ $technician->user->email }}</div>
                    @if($technician->phone)<div><i class="bi bi-telephone me-2 text-primary"></i>{{ $technician->phone }}</div>@endif
                    @if($technician->license_number)<div><i class="bi bi-card-text me-2 text-primary"></i>{{ $technician->license_number }}</div>@endif
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col"><div class="fw-bold text-primary">{{ $technician->jobs->count() }}</div><small class="text-muted">Total</small></div>
                    <div class="col"><div class="fw-bold text-warning">{{ $technician->jobs->where('status','pending')->count() }}</div><small class="text-muted">Pending</small></div>
                    <div class="col"><div class="fw-bold text-success">{{ $technician->jobs->where('status','completed')->count() }}</div><small class="text-muted">Done</small></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-briefcase me-2 text-primary"></i>Assigned Jobs</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Job #</th><th>Title</th><th>Client</th><th>Scheduled</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($technician->jobs as $job)
                        <tr>
                            <td><code class="small">{{ $job->job_number }}</code></td>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->client->name }}</td>
                            <td>{{ $job->scheduled_at?->format('d M Y') ?? '—' }}</td>
                            <td><span class="badge status-{{ $job->status }}">{{ ucfirst(str_replace('_',' ',$job->status)) }}</span></td>
                            <td><a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No jobs assigned.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
