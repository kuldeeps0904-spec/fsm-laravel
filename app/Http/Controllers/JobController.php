<?php

namespace App\Http\Controllers;

use App\Models\ServiceJob;
use App\Models\Client;
use App\Models\Technician;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class JobController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:admin', only: ['create', 'store', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ServiceJob::with(['client', 'technician.user']);

        if ($user->hasRole('technician')) {
            $query->where('technician_id', $user->technician?->id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('job_number', 'like', "%{$request->search}%")
                  ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$request->search}%"));
            });
        }
        if ($request->technician_id && $user->hasRole('admin')) {
            $query->where('technician_id', $request->technician_id);
        }

        // Professional Sorting: 
        // 1. Group by Activity (Pending/In Progress first, then Completed/Cancelled)
        // 2. Priority (Urgent > High > Medium > Low)
        // 3. Status Order (Pending > In Progress)
        // 4. Date (Earliest scheduled first)
        $jobs = $query->orderByRaw("
            CASE 
                WHEN status IN ('pending', 'in_progress') THEN 1 
                ELSE 2 
            END ASC
        ")->orderByRaw("
            CASE 
                WHEN priority = 'urgent' THEN 1 
                WHEN priority = 'high' THEN 2 
                WHEN priority = 'medium' THEN 3 
                WHEN priority = 'low' THEN 4 
                ELSE 5 
            END ASC
        ")->orderByRaw("
            CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'in_progress' THEN 2 
                WHEN status = 'completed' THEN 3 
                WHEN status = 'cancelled' THEN 4 
                ELSE 5 
            END ASC
        ")->orderBy('scheduled_at', 'asc')
        ->paginate(15);
        $technicians = $user->hasRole('admin') ? Technician::with('user')->where('is_active', true)->get() : collect();

        return view('jobs.index', compact('jobs', 'technicians'));
    }

    public function create()
    {
        $clients     = Client::orderBy('name')->get();
        $technicians = Technician::with('user')->where('is_active', true)->get();
        return view('jobs.create', compact('clients', 'technicians'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'technician_id'    => 'nullable|exists:technicians,id',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'status'           => 'required|in:pending,in_progress,completed,cancelled',
            'priority'         => 'required|in:low,medium,high,urgent',
            'service_type'     => 'nullable|string|max:100',
            'scheduled_at'     => 'nullable|date',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'location_address' => 'nullable|string|max:500',
            'admin_notes'      => 'nullable|string',
        ]);

        $job = ServiceJob::create($data);
        return redirect()->route('jobs.show', $job)->with('success', 'Job created successfully!');
    }

    public function show(ServiceJob $job)
    {
        $user = auth()->user();
        if ($user->hasRole('technician') && $job->technician_id !== $user->technician?->id) {
            abort(403);
        }
        $job->load(['client', 'technician.user', 'images', 'chemicals', 'invoice']);
        return view('jobs.show', compact('job'));
    }

    public function edit(ServiceJob $job)
    {
        $user = auth()->user();
        if ($user->hasRole('technician') && $job->technician_id !== $user->technician?->id) {
            abort(403);
        }

        $clients     = Client::orderBy('name')->get();
        $technicians = Technician::with('user')->where('is_active', true)->get();
        return view('jobs.edit', compact('job', 'clients', 'technicians'));
    }

    public function update(Request $request, ServiceJob $job)
    {
        $user = auth()->user();
        if ($user->hasRole('technician') && $job->technician_id !== $user->technician?->id) {
            abort(403);
        }

        $isTech = $user->hasRole('technician');

        if ($isTech) {
            // Technicians can only update status and notes
            $data = $request->validate([
                'status'            => 'required|in:pending,in_progress,completed,cancelled',
                'technician_notes'  => 'nullable|string',
            ]);
            if ($data['status'] === 'completed' && !$job->completed_at) {
                $data['completed_at'] = now();
            }
        } else {
            $data = $request->validate([
                'client_id'        => 'required|exists:clients,id',
                'technician_id'    => 'nullable|exists:technicians,id',
                'title'            => 'required|string|max:255',
                'description'      => 'nullable|string',
                'status'           => 'required|in:pending,in_progress,completed,cancelled',
                'priority'         => 'required|in:low,medium,high,urgent',
                'service_type'     => 'nullable|string|max:100',
                'scheduled_at'     => 'nullable|date',
                'latitude'         => 'nullable|numeric|between:-90,90',
                'longitude'        => 'nullable|numeric|between:-180,180',
                'location_address' => 'nullable|string|max:500',
                'admin_notes'      => 'nullable|string',
                'technician_notes' => 'nullable|string',
            ]);
            if (($data['status'] ?? '') === 'completed' && !$job->completed_at) {
                $data['completed_at'] = now();
            }
        }

        $job->update($data);
        return redirect()->route('jobs.show', $job)->with('success', 'Job updated successfully!');
    }

    public function destroy(ServiceJob $job)
    {
        $job->delete();
        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully!');
    }
}
