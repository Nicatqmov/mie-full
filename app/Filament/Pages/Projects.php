<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Illuminate\Database\Eloquent\Builder;

class Projects extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Projects';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.projects';

    public ?Project $project = null;
    public $selectedEntity = null;

    public function mount(): void
    {
        $projectId = request()->query('project');
        if ($projectId) {
            $this->project = Project::with('entities.fields')->find($projectId);
            if ($this->project) {
                redirect()->route('filament.admin.pages.dashboard', ['project' => $projectId]);
            }
        }
    }

    public static function getSlug(): string
    {
        return 'projects';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public static function getNavigationItems(): array
    {
        $projectId = request()->query('project');
        if (!$projectId) {
            return [];
        }

        $project = Project::with('entities.fields')->find($projectId);
        if (!$project) {
            return [];
        }

        $items = [
            NavigationItem::make('Dashboard')
                ->icon('heroicon-o-home')
                ->url(route('filament.admin.pages.dashboard', ['project' => $projectId]))
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.dashboard')),
        ];

        foreach ($project->entities as $entity) {
            $items[] = NavigationItem::make($entity->name)
                ->url(route('filament.admin.pages.entity-data', ['project' => $projectId, 'entity' => $entity->id]))
                ->isActiveWhen(fn () => request()->query('entity') == $entity->id);
        }

        return $items;
    }
}
