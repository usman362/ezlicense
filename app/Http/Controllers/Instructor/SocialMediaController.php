<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaSubmission;
use App\Models\User;
use App\Notifications\NewSocialMediaSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * Instructor "Marketing / Social Media" — upload a short testimonial video +
 * photos when a learner passes their test. Submissions land in the admin panel
 * to be posted on the company's social media.
 */
class SocialMediaController extends Controller
{
    public function index()
    {
        $submissions = SocialMediaSubmission::where('instructor_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('instructor.pages.social-media', [
            'submissions' => $submissions,
            'categories'  => SocialMediaSubmission::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'learner_name' => ['nullable', 'string', 'max:120'],
            'category'     => ['required', 'in:' . implode(',', array_keys(SocialMediaSubmission::CATEGORIES))],
            'test_date'    => ['nullable', 'date'],
            'caption'      => ['nullable', 'string', 'max:1500'],
            // Short testimonial video (<= ~45s enforced client-side) + optional photos.
            'video'        => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:81920'], // 80 MB
            'photos'       => ['nullable', 'array', 'max:6'],
            'photos.*'     => ['file', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ], [
            'video.mimetypes' => 'The video must be an MP4, MOV or WEBM file.',
            'video.max'       => 'The video is too large (max 80 MB). Please upload a shorter clip.',
        ]);

        if (! $request->hasFile('video') && ! $request->hasFile('photos')) {
            return back()->withInput()->withErrors(['video' => 'Please upload a video or at least one photo.']);
        }

        $user = Auth::user();
        $dir = 'social-media/' . $user->id;
        $storeOpts = ['disk' => 'spaces', 'visibility' => 'private'];

        $videoPath = null;
        $photoPaths = [];
        try {
            if ($request->hasFile('video')) {
                $videoPath = $request->file('video')->store($dir, $storeOpts);
            }
            foreach ((array) $request->file('photos', []) as $photo) {
                $photoPaths[] = $photo->store($dir, $storeOpts);
            }
        } catch (\Throwable $e) {
            Log::warning('Social media upload failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['video' => 'Upload failed — please try again.']);
        }

        $submission = SocialMediaSubmission::create([
            'instructor_id'         => $user->id,
            'instructor_profile_id' => $user->instructorProfile?->id,
            'learner_name'          => $data['learner_name'] ?? null,
            'category'              => $data['category'],
            'test_date'             => $data['test_date'] ?? null,
            'caption'               => $data['caption'] ?? null,
            'video_path'            => $videoPath,
            'photo_paths'           => $photoPaths ?: null,
            'status'                => SocialMediaSubmission::STATUS_PENDING,
        ]);

        // Alert admins that new marketing material is ready to review/post.
        try {
            foreach (User::where('role', User::ROLE_ADMIN)->get() as $admin) {
                $admin->notify(new NewSocialMediaSubmission($submission));
            }
        } catch (\Throwable $e) {
            Log::warning('Social media admin alert failed: ' . $e->getMessage());
        }

        return redirect()->route('instructor.social-media')
            ->with('message', 'Thanks! Your submission has been sent to our team for the socials. 🎉');
    }

    public function destroy(SocialMediaSubmission $submission)
    {
        // Instructors may only remove their OWN submissions, and only while still pending.
        if ((int) $submission->instructor_id !== (int) Auth::id()) {
            abort(403);
        }
        if ($submission->status !== SocialMediaSubmission::STATUS_PENDING) {
            return back()->withErrors(['submission' => 'This submission is already being handled and can no longer be removed.']);
        }

        $this->deleteMedia($submission);
        $submission->delete();

        return back()->with('message', 'Submission removed.');
    }

    private function deleteMedia(SocialMediaSubmission $submission): void
    {
        try {
            $paths = array_filter(array_merge([$submission->video_path], (array) $submission->photo_paths));
            if ($paths) {
                Storage::disk('spaces')->delete($paths);
            }
        } catch (\Throwable $e) {
            Log::warning('Social media media delete failed: ' . $e->getMessage());
        }
    }
}
