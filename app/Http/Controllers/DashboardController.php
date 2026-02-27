<?php

namespace App\Http\Controllers;

use App\Models\ServiceJob;
use App\Models\Client;
use App\Models\Technician;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $stats = [
                'total_jobs'      => ServiceJob::count(),
                'pending_jobs'    => ServiceJob::where('status', 'pending')->count(),
                'in_progress'     => ServiceJob::where('status', 'in_progress')->count(),
                'completed_jobs'  => ServiceJob::where('status', 'completed')->count(),
                'total_clients'   => Client::count(),
                'total_technicians' => Technician::count(),
                'total_revenue'   => Invoice::where('status', 'paid')->sum('total_amount'),
            ];
            $recentJobs = ServiceJob::with(['client', 'technician.user'])
                ->latest()->take(8)->get();
        } else {
            $technician = $user->technician;
            $stats = [
                'total_jobs'     => ServiceJob::where('technician_id', $technician?->id)->count(),
                'pending_jobs'   => ServiceJob::where('technician_id', $technician?->id)->where('status', 'pending')->count(),
                'in_progress'    => ServiceJob::where('technician_id', $technician?->id)->where('status', 'in_progress')->count(),
                'completed_jobs' => ServiceJob::where('technician_id', $technician?->id)->where('status', 'completed')->count(),
            ];
            $recentJobs = ServiceJob::with(['client'])
                ->where('technician_id', $technician?->id)
                ->latest()->take(8)->get();
        }

        return view('dashboard', compact('stats', 'recentJobs'));
    }
}
