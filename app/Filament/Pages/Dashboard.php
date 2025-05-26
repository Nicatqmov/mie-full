<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public ?Project $project = null;

    public function mount(): void
    {
        $projectId = request()->query('project');
        if ($projectId) {
            $this->project = Project::with('entities.fields')->find($projectId);
        }
    }

    public function getTitle(): string
    {
        return $this->project ? $this->project->name . ' Dashboard' : 'Dashboard';
    }

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
} 