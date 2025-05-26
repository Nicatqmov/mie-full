<?php

namespace App\Filament\Resources\EntityDataResource\Pages;

use App\Filament\Resources\EntityDataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntityData extends ListRecords
{
    protected static string $resource = EntityDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 