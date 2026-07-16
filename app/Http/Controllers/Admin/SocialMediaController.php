<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Admin review of instructor-submitted marketing material. Admin previews the
 * video/photos (via signed Spaces URLs), copies the suggested caption, and
 * marks the submission approved/posted once it's been shared on social media.
 */
class SocialMediaController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = SocialMediaSubmission::with('instructor:id,name,email')
            ->latest();

        if ($status && array_key_exists($status, $this->statusOptions())) {
            $query->where('status', $status);
        }

        return view('admin.social-media.index', [
            'submissions'   => $query->paginate(20)->withQueryString(),
            'status'        => $status,
            'statusOptions' => $this->statusOptions(),
            'pendingCount'  => SocialMediaSubmission::where('status', SocialMediaSubmission::STATUS_PENDING)->count(),
        ]);
    }

    public function show(SocialMediaSubmission $socialMedium)
    {
        $submission = $socialMedium->load('instructor:id,name,email,phone', 'reviewer:id,name');

        return view('admin.social-media.show', [
            'submission'   => $submission,
            'videoUrl'     => $this->tempUrl($submission->video_path),
            'photoUrls'    => collect($submission->photo_paths ?? [])
                                ->map(fn ($p) => $this->tempUrl($p))
                                ->filter()
                                ->values()
                                ->all(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function updateStatus(Request $request, SocialMediaSubmission $socialMedium)
    {
        $data = $request->validate([
            'status'      => ['required', 'in:' . implode(',', array_keys($this->statusOptions()))],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $wasPosted = $socialMedium->status === SocialMediaSubmission::STATUS_POSTED;

        $socialMedium->update([
            'status'              => $data['status'],
            'admin_notes'         => $data['admin_notes'] ?? $socialMedium->admin_notes,
            'reviewed_by_user_id' => Auth::id(),
            'posted_at'           => $data['status'] === SocialMediaSubmission::STATUS_POSTED
                                        ? ($socialMedium->posted_at ?? now())
                                        : $socialMedium->posted_at,
        ]);

        // Newly marked "posted" → let the instructor know their win is live.
        if (! $wasPosted && $data['status'] === SocialMediaSubmission::STATUS_POSTED && $socialMedium->instructor) {
            try {
                $socialMedium->instructor->notify(new \App\Notifications\SocialMediaSubmissionPosted($socialMedium));
            } catch (\Throwable $e) {
                Log::warning('Social media posted notification failed: ' . $e->getMessage());
            }
        }

        return back()->with('message', 'Submission updated.');
    }

    public function destroy(SocialMediaSubmission $socialMedium)
    {
        try {
            $paths = array_filter(array_merge([$socialMedium->video_path], (array) $socialMedium->photo_paths));
            if ($paths) {
                Storage::disk('spaces')->delete($paths);
            }
        } catch (\Throwable $e) {
            Log::warning('Social media media delete failed: ' . $e->getMessage());
        }

        $socialMedium->delete();

        return redirect()->route('admin.social-media.index')->with('message', 'Submission deleted.');
    }

    private function tempUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        try {
            return Storage::disk('spaces')->temporaryUrl($path, now()->addMinutes(60));
        } catch (\Throwable $e) {
            Log::warning('Social media temporaryUrl failed: ' . $e->getMessage());
            return null;
        }
    }

    private function statusOptions(): array
    {
        return [
            SocialMediaSubmission::STATUS_PENDING  => 'Pending',
            SocialMediaSubmission::STATUS_APPROVED => 'Approved',
            SocialMediaSubmission::STATUS_POSTED   => 'Posted',
            SocialMediaSubmission::STATUS_REJECTED => 'Rejected',
        ];
    }
}
