@extends('layouts.app')
@section('title', 'Jobs')
@section('page-title', 'Jobs')

@section('topbar-actions')
@role('admin')
<a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg me-1"></i> New Job
</a>
@endrole
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <form class="d-flex flex-wrap gap-2 align-items-center" method="GET">
            <input type="search" name="search" class="form-control form-control-sm" style="max-width:200px" placeholder="Search..." value="{{ request('search') }}">
            <select name="status" class="form-select form-select-sm" style="max-width:160px">
                <option value="">All Statuses</option>
                @foreach(['pending','in_progress','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            @role('admin')
            <select name="technician_id" class="form-select form-select-sm" style="max-width:180px">
                <option value="">All Technicians</option>
                @foreach($technicians as $t)
                <option value="{{ $t->id }}" {{ request('technician_id')==$t->id?'selected':'' }}>{{ $t->user->name }}</option>
                @endforeach
            </select>
            @endrole
            <button class="btn btn-sm btn-outline-secondary">Filter</button>
            @if(request()->hasAny(['search','status','technician_id']))
            <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-link text-muted">Clear</a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Job #</th><th>Title</th><th>Client</th><th>Technician</th><th>Service Type</th><th>Scheduled</th><th>Priority</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                <tr>
                    <td><code class="small">{{ $job->job_number }}</code></td>
                    <td class="fw-semibold" style="max-width:180px">{{ $job->title }}</td>
                    <td>{{ $job->client->name }}</td>
                    <td>{{ $job->technician?->user?->name ?? '<span class="text-muted">Unassigned</span>' }}</td>
                    <td>{{ $job->service_type ?? '—' }}</td>
                    <td>{{ $job->scheduled_at?->format('d M, H:i') ?? '—' }}</td>
                    <td><span class="badge bg-{{ $job->priority_badge }}">{{ ucfirst($job->priority) }}</span></td>
                    <td><span class="badge status-{{ $job->status }}">{{ ucfirst(str_replace('_',' ',$job->status)) }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('jobs.edit', $job) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                            @role('admin')
                            <form method="POST" action="{{ route('jobs.destroy', $job) }}" onsubmit="return confirm('Delete this job?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                            @endrole
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No jobs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jobs->hasPages())
    <div class="card-footer">{{ $jobs->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
