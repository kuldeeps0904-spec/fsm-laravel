@extends('layouts.app')
@section('title', 'Technicians')
@section('page-title', 'Technicians')

@section('topbar-actions')
<a href="{{ route('technicians.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg me-1"></i> Add Technician
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-person-badge me-2 text-primary"></i>All Technicians ({{ $technicians->total() }})</span>
        <form class="d-flex gap-2" method="GET">
            <input type="search" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-outline-secondary">Go</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Specialization</th><th>License</th><th>Jobs</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($technicians as $tech)
                <tr>
                    <td>{{ ($technicians->currentPage() - 1) * $technicians->perPage() + $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $tech->user->name }}</td>
                    <td>{{ $tech->user->email }}</td>
                    <td>{{ $tech->phone ?? '—' }}</td>
                    <td>{{ $tech->specialization ?? '—' }}</td>
                    <td><code class="small">{{ $tech->license_number ?? '—' }}</code></td>
                    <td><span class="badge bg-primary">{{ $tech->jobs_count }}</span></td>
                    <td>
                        @if($tech->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('technicians.show', $tech) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('technicians.edit', $tech) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('technicians.destroy', $tech) }}" onsubmit="return confirm('Delete technician account?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No technicians found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($technicians->hasPages())
    <div class="card-footer">{{ $technicians->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
