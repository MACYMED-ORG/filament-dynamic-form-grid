<?php

namespace Macymed\Filament\DynamicFormGrid;

use Illuminate\Support\ServiceProvider;

class DynamicFormGridServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Charge les vues depuis le dossier resources/views du package sous le namespace indiqué.
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-macymed-dynamic-form-grid');

        // Publication optionnelle pour personnaliser les vues localement.
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-macymed-dynamic-form-grid'),
        ], 'filament-dynamic-form-grid-views');
    }

    public function register()
    {
        //
    }
}
