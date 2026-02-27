@extends('layouts.app')
@section('title', $job->title)
@section('page-title', $job->title)

@section('topbar-actions')
<a href="{{ route('jobs.edit', $job) }}" class="btn btn-sm btn-warning text-white"><i class="bi bi-pencil me-1"></i>Edit</a>
@role('admin')
<div class="btn-group">
    @if($job->invoice)
    <a href="{{ route('invoices.show', $job->invoice) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-receipt me-1"></i>View Invoice</a>
    @endif
    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#invoiceModal">
        <i class="bi bi-receipt-cutoff me-1"></i>{{ $job->invoice ? 'Update Invoice' : 'Generate Invoice' }}
    </button>
</div>
@endrole
@endsection

@section('content')
<div class="row g-3">
    <!-- Job Details -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Job Details</div>
            <div class="card-body small">
                <div class="mb-2"><span class="text-muted">Job #:</span> <code>{{ $job->job_number }}</code></div>
                <div class="mb-2"><span class="text-muted">Client:</span> <a href="{{ route('clients.show', $job->client) }}">{{ $job->client->name }}</a></div>
                <div class="mb-2"><span class="text-muted">Technician:</span> {{ $job->technician?->user?->name ?? 'Unassigned' }}</div>
                <div class="mb-2"><span class="text-muted">Service:</span> {{ $job->service_type ?? '—' }}</div>
                <div class="mb-2"><span class="text-muted">Scheduled:</span> {{ $job->scheduled_at?->format('d M Y, H:i') ?? '—' }}</div>
                @if($job->completed_at)
                <div class="mb-2"><span class="text-muted">Completed:</span> {{ $job->completed_at->format('d M Y, H:i') }}</div>
                @endif
                <hr>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge status-{{ $job->status }}">{{ ucfirst(str_replace('_',' ',$job->status)) }}</span>
                    <span class="badge bg-{{ $job->priority_badge }}">{{ ucfirst($job->priority) }} Priority</span>
                </div>
                @if($job->location_address || $job->latitude)
                <hr>
                <div><i class="bi bi-geo-alt text-danger me-1"></i>{{ $job->location_address ?? 'Geo-tagged' }}</div>
                @if($job->latitude)
                <div id="job-map" class="mt-2"></div>
                @endif
                @endif
                @if($job->admin_notes)
                <hr>
                <div class="text-muted">Admin Notes:</div>
                <div>{{ $job->admin_notes }}</div>
                @endif
                @if($job->technician_notes)
                <hr>
                <div class="text-muted">Technician Notes:</div>
                <div>{{ $job->technician_notes }}</div>
                @endif
            </div>
        </div>

        <!-- Quick Status Update (technician) -->
        @role('technician')
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-sliders me-2"></i>Update Status</div>
            <div class="card-body">
                <form method="POST" action="{{ route('jobs.update', $job) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            @foreach(['pending','in_progress','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $job->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="technician_notes" class="form-control form-control-sm" rows="3">{{ $job->technician_notes }}</textarea>
                    </div>
                    <button class="btn btn-primary btn-sm w-100">Update</button>
                </form>
            </div>
        </div>
        @endrole
    </div>

    <div class="col-lg-8">
        @if($job->description)
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-file-text me-2 text-primary"></i>Description</div>
            <div class="card-body">{{ $job->description }}</div>
        </div>
        @endif

        <!-- Before/After Images -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-images me-2 text-primary"></i>Before/After Images</span>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#uploadForm">
                    <i class="bi bi-upload me-1"></i>Upload
                </button>
            </div>
            <div class="collapse px-3 pt-2" id="uploadForm">
                <form method="POST" action="{{ route('job-images.store', $job) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="before">Before</option>
                                <option value="after">After</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Images</label>
                            <input type="file" name="images[]" class="form-control form-control-sm" multiple accept="image/*">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Caption</label>
                            <input type="text" name="caption" class="form-control form-control-sm" placeholder="Optional">
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary mt-2 mb-3"><i class="bi bi-upload me-1"></i>Upload Images</button>
                </form>
            </div>
            <div class="card-body">
                @if($job->beforeImages->isNotEmpty())
                <p class="fw-semibold text-muted small mb-2"><i class="bi bi-camera me-1"></i>BEFORE</p>
                <div class="img-gallery mb-3">
                    @foreach($job->beforeImages as $img)
                    <div class="img-thumb">
                        <img src="{{ Storage::url($img->image_path) }}" alt="{{ $img->caption }}">
                        <form method="POST" action="{{ route('job-images.destroy', $img) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="delete-btn" type="submit" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
                @if($job->afterImages->isNotEmpty())
                <p class="fw-semibold text-muted small mb-2"><i class="bi bi-camera-fill me-1"></i>AFTER</p>
                <div class="img-gallery">
                    @foreach($job->afterImages as $img)
                    <div class="img-thumb">
                        <img src="{{ Storage::url($img->image_path) }}" alt="{{ $img->caption }}">
                        <form method="POST" action="{{ route('job-images.destroy', $img) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="delete-btn" type="submit" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
                @if($job->images->isEmpty())
                <p class="text-muted small text-center py-2">No images uploaded yet.</p>
                @endif
            </div>
        </div>

        <!-- Service Charges & Resources -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-cash-stack me-2 text-primary"></i>Service Charges & Resources</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead><tr><th>Type</th><th>Item/Description</th><th>Qty</th><th>Unit</th><th>Cost (₹)</th><th></th></tr></thead>
                        <tbody>
                            @foreach($job->chemicals as $item)
                            <tr>
                                <td><span class="badge bg-light text-dark small border">{{ ucfirst(str_replace('_', ' ', $item->type)) }}</span></td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity ?? '—' }}</td>
                                <td>{{ $item->unit ?? '—' }}</td>
                                <td>₹{{ number_format($item->cost, 2) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('chemicals.destroy', $item) }}" onsubmit="return confirm('Remove?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-1 border-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @if($job->chemicals->isNotEmpty())
                            <tr class="table-light fw-bold">
                                <td colspan="4" class="text-end">Total Charges:</td>
                                <td class="text-primary">₹{{ number_format($job->total_charges_cost, 2) }}</td>
                                <td></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- Add cost item form -->
                <div class="p-3 border-top bg-light bg-opacity-50">
                    <p class="small fw-semibold text-muted mb-2">Add New Charge or Resource</p>
                    <form method="POST" action="{{ route('chemicals.store', $job) }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-2">
                                <select name="type" class="form-select form-select-sm" required>
                                    <option value="service_fee">Service Fee</option>
                                    <option value="chemical">Chemical</option>
                                    <option value="equipment">Equipment</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-3"><input type="text" name="name" class="form-control form-control-sm" placeholder="Description/Name *" required></div>
                            <div class="col-md-1"><input type="number" step="0.01" name="quantity" class="form-control form-control-sm" placeholder="Qty"></div>
                            <div class="col-md-2">
                                <select name="unit" class="form-select form-select-sm">
                                    <option value="">No Unit</option>
                                    <option value="ml">ml</option>
                                    <option value="L">L</option>
                                    <option value="g">g</option>
                                    <option value="kg">kg</option>
                                    <option value="units">units</option>
                                    <option value="hrs">hrs</option>
                                </select>
                            </div>
                            <div class="col-md-3"><input type="number" step="0.01" name="cost" class="form-control form-control-sm" placeholder="Cost ₹ *" required></div>
                            <div class="col-md-1"><button class="btn btn-primary btn-sm w-100"><i class="bi bi-plus-lg"></i></button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@role('admin')
<!-- Invoice Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Generate Invoice</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('invoices.generate', $job) }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Subtotal (₹) *</label>
                            <input type="number" step="0.01" name="subtotal" class="form-control" value="{{ $job->total_charges_cost }}" required>
                            <div class="form-text">Auto-calculated from service charges.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tax %</label>
                            <input type="number" step="0.01" name="tax_percent" class="form-control" value="18">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Discount (₹)</label>
                            <input type="number" step="0.01" name="discount" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['draft','sent','paid','overdue'] as $s)
                                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" value="{{ now()->addDays(7)->format('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Visible on invoice..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-receipt me-1"></i>Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endrole
@endsection

@push('scripts')
<script>
@if($job->latitude && $job->longitude)
const map = L.map('job-map').setView([{{ $job->latitude }}, {{ $job->longitude }}], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);
L.marker([{{ $job->latitude }}, {{ $job->longitude }}])
    .addTo(map)
    .bindPopup("{{ $job->location_address ?? $job->title }}")
    .openPopup();
@endif
</script>
@endpush
