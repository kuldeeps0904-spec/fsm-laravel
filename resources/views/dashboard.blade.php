@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('topbar-actions')
    @role('admin')
    <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> New Job
    </a>
    @endrole
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #6366f1, #818cf8);">
            <div class="icon"><i class="bi bi-briefcase-fill"></i></div>
            <h3>{{ $stats['total_jobs'] }}</h3>
            <p>Total Jobs</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
            <div class="icon"><i class="bi bi-hourglass-split"></i></div>
            <h3>{{ $stats['pending_jobs'] }}</h3>
            <p>Pending</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">
            <div class="icon"><i class="bi bi-activity"></i></div>
            <h3>{{ $stats['in_progress'] }}</h3>
            <p>In Progress</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #34d399);">
            <div class="icon"><i class="bi bi-check2-circle"></i></div>
            <h3>{{ $stats['completed_jobs'] }}</h3>
            <p>Completed</p>
        </div>
    </div>
</div>

@role('admin')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card h-100" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
            <div class="icon"><i class="bi bi-people-fill"></i></div>
            <h3>{{ $stats['total_clients'] }}</h3>
            <p>Total Clients</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card h-100" style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);">
            <div class="icon"><i class="bi bi-person-badge-fill"></i></div>
            <h3>{{ $stats['total_technicians'] }}</h3>
            <p>Technicians</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card h-100" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
            <div class="icon"><i class="bi bi-currency-rupee"></i></div>
            <h3>₹{{ number_format($stats['total_revenue'], 0) }}</h3>
            <p>Revenue (Paid)</p>
        </div>
    </div>
</div>
@endrole

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2 text-primary"></i>Recent Jobs</span>
        <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Job #</th>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Technician</th>
                    <th>Scheduled</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentJobs as $job)
                <tr>
                    <td><code class="small">{{ $job->job_number }}</code></td>
                    <td class="fw-500">{{ $job->title }}</td>
                    <td>{{ $job->client->name }}</td>
                    <td>{{ $job->technician?->user?->name ?? '—' }}</td>
                    <td>{{ $job->scheduled_at?->format('d M, H:i') ?? '—' }}</td>
                    <td>
                        <span class="badge status-{{ $job->status }}">
                            {{ ucfirst(str_replace('_',' ',$job->status)) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $job->priority_badge }}">{{ ucfirst($job->priority) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No jobs yet. <a href="{{ route('jobs.create') }}">Create one</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
