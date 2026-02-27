<?php

namespace App\Http\Controllers;

use App\Models\ServiceJob;
use App\Models\JobImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class JobImageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function store(Request $request, ServiceJob $job)
    {
        $request->validate([
            'images'    => 'required|array',
            'images.*'  => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'type'      => 'required|in:before,after',
            'caption'   => 'nullable|string|max:255',
        ]);

        foreach ($request->file('images') as $image) {
            $path = $image->store("job-images/{$job->id}", 'public');
            JobImage::create([
                'service_job_id' => $job->id,
                'type'           => $request->type,
                'image_path'     => $path,
                'caption'        => $request->caption,
            ]);
        }

        return back()->with('success', ucfirst($request->type) . ' images uploaded successfully!');
    }

    public function destroy(JobImage $jobImage)
    {
        Storage::disk('public')->delete($jobImage->image_path);
        $jobImage->delete();
        return back()->with('success', 'Image deleted.');
    }
}
