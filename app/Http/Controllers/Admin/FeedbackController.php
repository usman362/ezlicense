<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $query = UserFeedback::with('user:id,name,email,role')->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $feedback = $query->paginate(30)->withQueryString();

        $stats = [
            'total' => UserFeedback::count(),
            'new' => UserFeedback::where('status', 'new')->count(),
            'reviewing' => UserFeedback::where('status', 'reviewing')->count(),
            'resolved' => UserFeedback::where('status', 'resolved')->count(),
        ];

        return view('admin.feedback.index', [
            'feedback' => $feedback,
            'stats' => $stats,
            'categories' => UserFeedback::CATEGORIES,
        ]);
    }

    public function update(Request $request, UserFeedback $feedback): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,reviewing,resolved,archived',
            'admin_response' => 'nullable|string|max:2000',
        ]);

        $update = ['status' => $validated['status']];

        if (! empty($validated['admin_response']) && $validated['admin_response'] !== $feedback->admin_response) {
            $update['admin_response'] = $validated['admin_response'];
            $update['responded_by_user_id'] = Auth::id();
            $update['responded_at'] = now();
        }

        $feedback->update($update);

        return back()->with('message', 'Feedback updated.');
    }
}
