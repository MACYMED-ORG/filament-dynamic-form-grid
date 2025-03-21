<?php

namespace Macymed\Filament\DynamicFormGrid;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DynamicFormGridServiceProvider extends PackageServiceProvider
{
    public static string $name = 'macymed-dynamic-form-grid';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews();
    }

    public function packageBooted(): void
    {
        // Enregistrer le composant avec Filament
        $this->app->bind(DynamicFormGrid::class, function () {
            return new DynamicFormGrid('data');
        });
    }
}