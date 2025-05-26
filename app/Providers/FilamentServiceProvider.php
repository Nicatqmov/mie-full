<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use App\Models\Project;
use App\Models\Entity;
use App\Filament\Pages\EntityData;
use App\Filament\Pages\EntityData\Create;
use App\Filament\Pages\EntityData\Edit;

class FilamentServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Filament::serving(function () {
            Filament::registerPages([
                EntityData::class,
                Create::class,
                Edit::class,
            ]);
        });
    }
} 