<?php

namespace Macymed\Filament\DynamicFormGrid;

use Illuminate\Support\ServiceProvider;

class DynamicFormGridServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Charge les vues du package et les rend accessibles via le namespace 'filament-macymed-dynamic-form-grid'
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-macymed-dynamic-form-grid');

        // Publier les vues si besoin (optionnel)
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-macymed-dynamic-form-grid'),
        ], 'filament-dynamic-form-grid-views');
    }

    public function register()
    {
        //
    }
}
