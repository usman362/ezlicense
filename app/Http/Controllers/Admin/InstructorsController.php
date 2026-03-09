<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;

class InstructorsController extends Controller
{
    public function index()
    {
        $instructors = InstructorProfile::with('user')->orderByDesc('created_at')->paginate(50);
        return view('admin.instructors.index', ['instructors' => $instructors]);
    }
}
