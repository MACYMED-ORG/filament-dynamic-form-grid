<?php

namespace Macymed\Filament\DynamicFormGrid;


use Filament\Forms\Components\Field;

class GridBuilder extends Field
{
    // La vue utilisée pour afficher l'éditeur de grille.
    protected string $view = 'filament-macymed-dynamic-grid::grid-builder';

    // La valeur par défaut est une chaîne JSON (ici, une grille vide).
    protected $default = '[]';

    /**
     * Crée le composant en liant le nom (pour le binding avec le modèle).
     */
    public static function make(string $name): static
    {
        return parent::make($name)->default('[]');
    }

    /**
     * On surcharge getChildComponents() pour que Filament n’itère pas sur des "enfants".
     *
     * Cela évite que le formulaire essaie de traiter des vues (notamment notre éditeur) comme des composants Filament.
     */
    public function getChildComponents(): array
    {
        return [];
    }
}