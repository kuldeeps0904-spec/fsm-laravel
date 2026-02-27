<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TechnicianController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:admin',
        ];
    }

    public function index(Request $request)
    {
        $query = Technician::with('user')->withCount('jobs');
        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        $technicians = $query->join('users', 'technicians.user_id', '=', 'users.id')
            ->orderBy('users.name', 'asc')
            ->select('technicians.*')
            ->paginate(15);
        return view('technicians.index', compact('technicians'));
    }

    public function create()
    {
        return view('technicians.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'password'       => 'required|min:8|confirmed',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole('technician');

            Technician::create([
                'user_id'        => $user->id,
                'phone'          => $request->phone,
                'specialization' => $request->specialization,
                'license_number' => $request->license_number,
                'is_active'      => true,
            ]);
        });

        return redirect()->route('technicians.index')->with('success', 'Technician created successfully!');
    }

    public function show(Technician $technician)
    {
        $technician->load(['user', 'jobs.client']);
        return view('technicians.show', compact('technician'));
    }

    public function edit(Technician $technician)
    {
        $technician->load('user');
        return view('technicians.edit', compact('technician'));
    }

    public function update(Request $request, Technician $technician)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,'.$technician->user_id,
            'phone'          => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'is_active'      => 'boolean',
        ]);

        DB::transaction(function () use ($request, $technician) {
            $technician->user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);
            $technician->update([
                'phone'          => $request->phone,
                'specialization' => $request->specialization,
                'license_number' => $request->license_number,
                'is_active'      => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('technicians.index')->with('success', 'Technician updated successfully!');
    }

    public function destroy(Technician $technician)
    {
        $technician->delete();
        return redirect()->route('technicians.index')->with('success', 'Technician removed successfully!');
    }
}
