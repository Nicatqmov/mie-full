<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    public ?Project $project = null;

    protected function getStats(): array
    {
        if (!$this->project) {
            return [];
        }

        return [
            Stat::make('Total Entities', $this->project->entities->count())
                ->description('Number of entities in this project')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
            Stat::make('Total Fields', $this->project->entities->sum(function ($entity) {
                return $entity->fields->count();
            }))
                ->description('Total number of fields across all entities')
                ->descriptionIcon('heroicon-m-table-cells')
                ->color('warning'),
            Stat::make('Latest Entity', $this->project->entities->sortByDesc('created_at')->first()?->name ?? 'None')
                ->description('Most recently created entity')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
} 