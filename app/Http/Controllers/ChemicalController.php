<?php

namespace App\Http\Controllers;

use App\Models\ServiceJob;
use App\Models\Chemical;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ChemicalController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function store(Request $request, ServiceJob $job)
    {
        $data = $request->validate([
            'type'          => 'required|in:chemical,service_fee,equipment,other',
            'name'          => 'required|string|max:255',
            'quantity'      => 'nullable|numeric|min:0',
            'unit'          => 'nullable|string|max:20',
            'concentration' => 'nullable|string|max:100',
            'cost'          => 'required|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);
        $data['service_job_id'] = $job->id;
        Chemical::create($data);
        return back()->with('success', 'Cost item added successfully!');
    }

    public function update(Request $request, Chemical $chemical)
    {
        $data = $request->validate([
            'type'          => 'required|in:chemical,service_fee,equipment,other',
            'name'          => 'required|string|max:255',
            'quantity'      => 'nullable|numeric|min:0',
            'unit'          => 'nullable|string|max:20',
            'concentration' => 'nullable|string|max:100',
            'cost'          => 'required|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);
        $chemical->update($data);
        return back()->with('success', 'Cost item updated!');
    }

    public function destroy(Chemical $chemical)
    {
        $chemical->delete();
        return back()->with('success', 'Cost item removed.');
    }
}
