<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectAdminController extends Controller
{
    public function show(Project $project)
    {
        // Redirect to Filament admin panel for this project
        return redirect()->route('filament.admin.resources.dynamic-entities.index', [
            'project' => $project->id,
            'entity' => $project->entities->first()->id,
        ]);
    }
} 