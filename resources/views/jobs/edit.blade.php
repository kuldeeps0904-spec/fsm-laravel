@extends('layouts.app')
@section('title', 'Edit Job')
@section('page-title', 'Edit Job')

@section('content')
<div class="card">
    <div class="card-header"><i class="bi bi-pencil me-2 text-warning"></i>Edit: {{ $job->title }}</div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('jobs.update', $job) }}">
            @csrf @method('PATCH')
            @role('admin')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Client *</label>
                    <select name="client_id" class="form-select" required>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $job->client_id==$client->id?'selected':'' }}>
                            {{ $client->name }} @if($client->company)({{ $client->company }})@endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assign Technician</label>
                    <select name="technician_id" class="form-select">
                        <option value="">— Unassigned —</option>
                        @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ $job->technician_id==$tech->id?'selected':'' }}>
                            {{ $tech->user->name }} ({{ $tech->specialization ?? 'General' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Job Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ $job->title }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Service Type</label>
                    <input type="text" name="service_type" class="form-control" value="{{ $job->service_type }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $job->description }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['pending','in_progress','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $job->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        @foreach(['low','medium','high','urgent'] as $p)
                        <option value="{{ $p }}" {{ $job->priority==$p?'selected':'' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Scheduled Date/Time</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ $job->scheduled_at?->format('Y-m-d\TH:i') }}">
                </div>
                <div class="col-12">
                    <label class="form-label"><i class="bi bi-geo-alt me-1 text-danger"></i>Location (Click map to update)</label>
                    <div id="job-map" class="mb-2"></div>
                    <div class="row g-2">
                        <div class="col-md-4"><input type="text" id="lat" name="latitude" class="form-control form-control-sm" placeholder="Latitude" value="{{ $job->latitude }}" readonly></div>
                        <div class="col-md-4"><input type="text" id="lng" name="longitude" class="form-control form-control-sm" placeholder="Longitude" value="{{ $job->longitude }}" readonly></div>
                        <div class="col-md-4"><input type="text" name="location_address" class="form-control form-control-sm" placeholder="Address" value="{{ $job->location_address }}"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Admin Notes</label>
                    <textarea name="admin_notes" class="form-control" rows="2">{{ $job->admin_notes }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Technician Notes</label>
                    <textarea name="technician_notes" class="form-control" rows="2">{{ $job->technician_notes }}</textarea>
                </div>
            </div>
            @endrole
            @role('technician')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['pending','in_progress','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $job->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Your Notes</label>
                    <textarea name="technician_notes" class="form-control" rows="4">{{ $job->technician_notes }}</textarea>
                </div>
            </div>
            @endrole
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-warning text-white"><i class="bi bi-check-lg me-1"></i>Update Job</button>
                <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const map = L.map('job-map').setView([{{ $job->latitude ?? 20.5937 }}, {{ $job->longitude ?? 78.9629 }}], {{ $job->latitude ? 13 : 5 }});
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap contributors' }).addTo(map);
let marker;
const latInput = document.getElementById('lat');
const lngInput = document.getElementById('lng');
@if($job->latitude && $job->longitude)
marker = L.marker([{{ $job->latitude }}, {{ $job->longitude }}]).addTo(map);
@endif
map.on('click', function(e) {
    if (marker) map.removeLayer(marker);
    marker = L.marker(e.latlng).addTo(map);
    latInput.value = e.latlng.lat.toFixed(7);
    lngInput.value = e.latlng.lng.toFixed(7);
});
</script>
@endpush
