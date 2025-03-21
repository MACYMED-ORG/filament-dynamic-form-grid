<?php

namespace Macymed\Filament\DynamicFormGrid;

use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DynamicFormGridServiceProvider extends ServiceProvider
{
    public static string $name = 'macymed-dynamic-form-grid';

    public function boot(): void
    {
        // Chargement des vues avec un préfixe unique
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'filament-macymed-dynamic-form-grid');

        // // Publication des vues avec un chemin de destination unique
        // $this->publishes([
        //     __DIR__ . '/Resources/views' => resource_path('views/vendor/filament-macymed-phone-number'),
        // ], 'filament-macymed-phone-number-views');

        // // Publication de la configuration
        // $this->publishes([
        //     __DIR__ . '/config/filament-macymed-phone-number.php' => config_path('filament-macymed-phone-number.php'),
        // ], 'filament-macymed-phone-number-config');
    }

    public function register(): void
    {
        // Fusion de la configuration
        // $this->mergeConfigFrom(
        //     __DIR__ . '/config/filament-macymed-phone-number.php', 'filament-macymed-phone-number'
        // );
    }
    // public function configurePackage(Package $package): void
    // {
    //     $package
    //         ->name(static::$name)
    //         ->hasViews();
    // }

    // public function packageBooted(): void
    // {
    //     // // Enregistrer le composant avec Filament
    //     // $this->app->bind(DynamicFormGrid::class, function () {
    //     //     return new DynamicFormGrid('data');
    //     // });
    // }
}