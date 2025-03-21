<?php

namespace Macymed\Filament\DynamicGrid;

use Filament\Forms\Components\Field;

class GridBuilder extends Field
{
    // On précise la vue qui rendra le composant.
    protected string $view = 'filament-macymed-dynamic-grid::grid-builder';

    // La valeur par défaut est une chaîne JSON représentant une grille vide.
    protected $default = '[]';

    /**
     * Crée le composant et définit la valeur par défaut.
     *
     * @param string $name Le nom du champ (pour le binding avec le modèle).
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->default('[]');
    }
}
