<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorDocument;
use App\Models\InstructorProfile;
use Illuminate\Http\Request;

class InstructorsController extends Controller
{
    public function index(Request $request)
    {
        $query = InstructorProfile::with(['user', 'documents']);

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('verification')) {
            $query->where('verification_status', $status);
        }

        if ($request->input('active') === '1') {
            $query->where('is_active', true);
        } elseif ($request->input('active') === '0') {
            $query->where('is_active', false);
        }

        $instructors = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.instructors.index', ['instructors' => $instructors]);
    }

    public function updateVerification(Request $request, InstructorProfile $instructorProfile)
    {
        $request->validate([
            'verification_status' => 'required|in:pending,documents_submitted,verified,rejected',
            'admin_notes'         => 'nullable|string|max:1000',
        ]);

        $instructorProfile->update([
            'verification_status' => $request->input('verification_status'),
            'admin_notes'         => $request->input('admin_notes'),
        ]);

        $name = $instructorProfile->user->name ?? 'Instructor';
        return redirect()->back()->with('message', "{$name}'s verification status updated to " . ucfirst($request->input('verification_status')) . ".");
    }

    public function toggleActive(InstructorProfile $instructorProfile)
    {
        $instructorProfile->is_active = ! $instructorProfile->is_active;
        $instructorProfile->save();

        $name = $instructorProfile->user->name ?? 'Instructor';
        return redirect()->back()->with('message', "{$name}'s profile has been " . ($instructorProfile->is_active ? 'activated' : 'deactivated') . ".");
    }

    /**
     * Update a document's verification status (verify or reject).
     */
    public function updateDocumentStatus(Request $request, InstructorDocument $instructorDocument)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
        ]);

        $data = ['status' => $request->input('status')];
        if ($request->input('status') === 'verified') {
            $data['verified_at'] = now();
        } else {
            $data['verified_at'] = null;
        }

        $instructorDocument->update($data);

        return redirect()->back()->with('message', 'Document ' . $request->input('status') . ' successfully.');
    }
}
