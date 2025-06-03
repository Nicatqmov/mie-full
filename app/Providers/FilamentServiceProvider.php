<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Panel;
use App\Filament\Pages\EntityData;
use App\Filament\Pages\EntityData\Create;
use App\Filament\Pages\EntityData\Edit;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;

class FilamentServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Filament::registerPanel(function (Panel $panel) {
            $panel
                ->id('admin')
                ->path('admin')
                ->login()
                ->pages([
                    EntityData::class,
                    Create::class,
                    Edit::class,
                ])
                ->middleware([
                    EncryptCookies::class,
                    AddQueuedCookiesToResponse::class,
                    StartSession::class,
                    ShareErrorsFromSession::class,
                    VerifyCsrfToken::class,
                    SubstituteBindings::class,
                ])
                ->authMiddleware([
                    Authenticate::class, // Filament's built-in auth check
                ])
                ->canAccess(fn () => true); // allow access always (for dev)
        });
    }
}
