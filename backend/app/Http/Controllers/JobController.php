<?php

namespace App\Http\Controllers;

use App\Mail\NewJobApplicationMail;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    /**
     * List all active jobs
     */
    public function index()
    {
        $jobs = JobListing::active()
            ->notExpired()
            ->latest()
            ->paginate(12);

        return view('jobs.index', compact('jobs'));
    }

    /**
     * Show job details
     */
    public function show($slug)
    {
        $job = JobListing::where('slug', $slug)
            ->active()
            ->firstOrFail();

        return view('jobs.show', compact('job'));
    }

    /**
     * Show application form
     */
    public function apply($slug)
    {
        $job = JobListing::where('slug', $slug)
            ->active()
            ->notExpired()
            ->firstOrFail();

        return view('jobs.apply', compact('job'));
    }

    /**
     * Submit application
     */
    public function submitApplication(Request $request, $slug)
    {
        $job = JobListing::where('slug', $slug)
            ->active()
            ->notExpired()
            ->firstOrFail();

        $validated = $request->validate([
            // Personal
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            // Education
            'education_level' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|string|max:10',
            'major' => 'nullable|string|max:255',
            'certifications' => 'nullable|string|max:2000',
            // Experience
            'years_of_experience' => 'nullable|integer|min:0|max:50',
            'current_job' => 'nullable|string|max:255',
            'previous_experience' => 'nullable|string|max:3000',
            'skills' => 'nullable|string|max:2000',
            // Extra
            'cover_letter' => 'nullable|string|max:5000',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Handle CV file upload
        if ($request->hasFile('cv_file')) {
            $validated['cv_file'] = $request->file('cv_file')->store('job-applications/cvs', 'public');
        }

        $validated['job_id'] = $job->id;
        $validated['status'] = 'pending';

        $application = JobApplication::create($validated);

        // Send email notification to admin
        try {
            $adminEmail = Setting::getValue('admin_email', 'medicamodrug@gmail.com');
            $application->load('job');
            Mail::to($adminEmail)->send(new NewJobApplicationMail($application));
        } catch (\Throwable $e) {
            Log::error('Job application email notification failed: ' . $e->getMessage());
        }

        return redirect()->route('jobs.success', $job->slug);
    }

    /**
     * Application success page
     */
    public function success($slug)
    {
        $job = JobListing::where('slug', $slug)->firstOrFail();
        return view('jobs.success', compact('job'));
    }
}
