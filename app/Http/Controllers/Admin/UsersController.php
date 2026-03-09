<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('created_at')->paginate(50);
        return view('admin.users.index', ['users' => $users]);
    }
}
