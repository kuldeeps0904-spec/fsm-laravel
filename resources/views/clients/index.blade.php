@extends('layouts.app')
@section('title', 'Clients')
@section('page-title', 'Clients')

@section('topbar-actions')
<a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg me-1"></i> Add Client
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-people me-2 text-primary"></i>All Clients ({{ $clients->total() }})</span>
        <form class="d-flex gap-2" method="GET">
            <input type="search" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-outline-secondary">Go</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Name</th><th>Company</th><th>Phone</th><th>Email</th><th>Jobs</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td>{{ ($clients->currentPage() - 1) * $clients->perPage() + $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $client->name }}</td>
                    <td>{{ $client->company ?? '—' }}</td>
                    <td>{{ $client->phone ?? '—' }}</td>
                    <td>{{ $client->email ?? '—' }}</td>
                    <td><span class="badge bg-primary">{{ $client->jobs_count }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Delete this client?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No clients found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
    <div class="card-footer">{{ $clients->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
