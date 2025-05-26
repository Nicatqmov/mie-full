<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller
{
    public function index(){
        $projects = Auth::user()->projects;

        // Fetch recent activity (example: last 5 projects created)
        $recentProjects = $projects->sortByDesc('created_at')->take(5);

        return view('admin.layouts.pages.dashboard.index',compact('projects','recentProjects'));
    }
}
