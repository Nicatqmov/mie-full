<?php

namespace App\Filament\Resources\EntityDataResource\Pages;

use App\Filament\Resources\EntityDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntityData extends EditRecord
{
    protected static string $resource = EntityDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 