@extends('layouts.app')
@section('title', 'New Job')
@section('page-title', 'New Job')

@section('content')
<div class="card">
    <div class="card-header"><i class="bi bi-briefcase me-2 text-primary"></i>Create New Job</div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('jobs.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Client *</label>
                    <select name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                        <option value="">— Select Client —</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', request('client_id'))==$client->id?'selected':'' }}>
                            {{ $client->name }} @if($client->company)({{ $client->company }})@endif
                        </option>
                        @endforeach
                    </select>
                    @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assign Technician</label>
                    <select name="technician_id" class="form-select">
                        <option value="">— Unassigned —</option>
                        @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ old('technician_id')==$tech->id?'selected':'' }}>
                            {{ $tech->user->name }} ({{ $tech->specialization ?? 'General' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Job Title *</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="e.g. Annual Termite Inspection">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Service Type</label>
                    <input type="text" name="service_type" class="form-control" value="{{ old('service_type') }}" placeholder="e.g. Termite, Rodent">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        @foreach(['pending','in_progress','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status','pending')==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Priority *</label>
                    <select name="priority" class="form-select" required>
                        @foreach(['low','medium','high','urgent'] as $p)
                        <option value="{{ $p }}" {{ old('priority','medium')==$p?'selected':'' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Scheduled Date/Time</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
                </div>

                <!-- Geo-tag section -->
                <div class="col-12">
                    <label class="form-label"><i class="bi bi-geo-alt me-1 text-danger"></i>Location (Click map to pin)</label>
                    <div id="job-map" class="mb-2"></div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" id="lat" name="latitude" class="form-control form-control-sm" placeholder="Latitude" value="{{ old('latitude') }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="lng" name="longitude" class="form-control form-control-sm" placeholder="Longitude" value="{{ old('longitude') }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="location_address" class="form-control form-control-sm" placeholder="Address description" value="{{ old('location_address') }}">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Admin Notes</label>
                    <textarea name="admin_notes" class="form-control" rows="2">{{ old('admin_notes') }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Job</button>
                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const map = L.map('job-map').setView([20.5937, 78.9629], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker;
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');

    if (latInput.value && lngInput.value) {
        const ll = [parseFloat(latInput.value), parseFloat(lngInput.value)];
        marker = L.marker(ll).addTo(map);
        map.setView(ll, 13);
    }

    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        latInput.value = e.latlng.lat.toFixed(7);
        lngInput.value = e.latlng.lng.toFixed(7);
    });
</script>
@endpush
