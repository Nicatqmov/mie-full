<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        $projects = $user->projects;
        return view('admin.layouts.pages.apis.index', compact('token', 'projects'));
    }
}
