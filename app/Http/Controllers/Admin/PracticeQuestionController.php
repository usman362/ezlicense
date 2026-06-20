<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PracticeQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PracticeQuestionController extends Controller
{
    public function index(Request $request)
    {
        $q = PracticeQuestion::query()->orderBy('section')->orderBy('sort_order')->orderBy('id');

        if ($section = $request->query('section')) {
            $q->where('section', $section);
        }
        if ($search = trim((string) $request->query('q'))) {
            $q->where('question', 'like', "%{$search}%");
        }

        $questions = $q->paginate(20)->withQueryString();

        $stats = [
            'general'     => PracticeQuestion::where('section', PracticeQuestion::SECTION_GENERAL)->count(),
            'road_safety' => PracticeQuestion::where('section', PracticeQuestion::SECTION_ROAD_SAFETY)->count(),
        ];

        return view('admin.pages.practice-questions.index', compact('questions', 'stats'));
    }

    public function create()
    {
        return view('admin.pages.practice-questions.form', [
            'question' => new PracticeQuestion(['is_active' => true, 'options' => ['', '', '', '']]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        PracticeQuestion::create($data);

        return redirect()->route('admin.practice-questions.index')->with('message', 'Question added.');
    }

    public function edit(PracticeQuestion $practiceQuestion)
    {
        return view('admin.pages.practice-questions.form', ['question' => $practiceQuestion]);
    }

    public function update(Request $request, PracticeQuestion $practiceQuestion)
    {
        $data = $this->validateData($request, $practiceQuestion);
        $practiceQuestion->update($data);

        return redirect()->route('admin.practice-questions.index')->with('message', 'Question updated.');
    }

    public function destroy(PracticeQuestion $practiceQuestion)
    {
        if ($practiceQuestion->image_path && ! str_starts_with($practiceQuestion->image_path, 'http')) {
            try { Storage::disk('spaces')->delete($practiceQuestion->image_path); } catch (\Throwable $e) {}
        }
        $practiceQuestion->delete();

        return back()->with('message', 'Question deleted.');
    }

    public function toggle(PracticeQuestion $practiceQuestion)
    {
        $practiceQuestion->update(['is_active' => ! $practiceQuestion->is_active]);

        return back()->with('message', 'Question ' . ($practiceQuestion->is_active ? 'activated' : 'deactivated') . '.');
    }

    private function validateData(Request $request, ?PracticeQuestion $existing = null): array
    {
        $validated = $request->validate([
            'section'       => ['required', 'in:general,road_safety'],
            'question'      => ['required', 'string', 'max:1000'],
            'options'       => ['required', 'array', 'min:2', 'max:5'],
            'options.*'     => ['nullable', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'min:0'],
            'explanation'   => ['nullable', 'string', 'max:1000'],
            'sort_order'    => ['nullable', 'integer', 'min:0', 'max:9999'],
            'image'         => ['nullable', 'image', 'max:4096'],
            'remove_image'  => ['nullable', 'boolean'],
        ]);

        // Drop empty options, re-index, and keep the correct answer pointing at the right option.
        $options = array_values(array_filter($validated['options'], fn ($o) => trim((string) $o) !== ''));
        abort_if(count($options) < 2, 422, 'At least two options are required.');

        $correct = (int) $validated['correct_index'];
        $correct = min($correct, count($options) - 1);

        $out = [
            'section'       => $validated['section'],
            'question'      => $validated['question'],
            'options'       => $options,
            'correct_index' => $correct,
            'explanation'   => $validated['explanation'] ?? null,
            'is_active'     => $request->boolean('is_active'),
            'sort_order'    => $validated['sort_order'] ?? 0,
        ];

        // Image handling
        if ($request->boolean('remove_image') && $existing && $existing->image_path) {
            if (! str_starts_with($existing->image_path, 'http')) {
                try { Storage::disk('spaces')->delete($existing->image_path); } catch (\Throwable $e) {}
            }
            $out['image_path'] = null;
        }
        if ($request->hasFile('image')) {
            if ($existing && $existing->image_path && ! str_starts_with($existing->image_path, 'http')) {
                try { Storage::disk('spaces')->delete($existing->image_path); } catch (\Throwable $e) {}
            }
            $out['image_path'] = $request->file('image')->store('practice-questions', 'spaces');
        }

        return $out;
    }
}
