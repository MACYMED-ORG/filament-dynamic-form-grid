<?php

namespace Macymed\Filament\DynamicFormGrid;

use Illuminate\Support\ServiceProvider;

class DynamicFormGridServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Charge les vues du package sous le namespace indiqué
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-macymed-dynamic-form-grid');

        // Optionnel : publication des vues pour une personnalisation locale
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-macymed-dynamic-form-grid'),
        ], 'filament-dynamic-form-grid-views');
    }

    public function register()
    {
        // Ici, rien de particulier à enregistrer
    }
}
