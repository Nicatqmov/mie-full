<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class FilamentController extends Controller
{
    public function redirectToFilament(Project $project)
    {
        return redirect()->route('filament.admin.resources.dynamic-models.index', ['project' => $project->id]);
    }
} 