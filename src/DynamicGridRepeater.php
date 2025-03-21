<?php

namespace Macymed\Filament\DynamicFormGrid;

use Filament\Forms\Components\Repeater;

class DynamicGridRepeater extends Repeater
{
    /**
     * Indique si l'ajustement automatique du nombre de colonnes est activé.
     */
    protected bool $autoGrid = false;

    /**
     * Nombre maximum de colonnes autorisées (plafond).
     */
    protected int $maxGridColumns = 4;

    /**
     * Active ou désactive l'autoGrid et fixe le maximum de colonnes.
     *
     * @param bool $condition
     * @param int  $maxColumns Nombre maximum de colonnes.
     * @return $this
     */
    public function autoGrid(bool $condition = true, int $maxColumns = 4): static
    {
        $this->autoGrid = $condition;
        $this->maxGridColumns = $maxColumns;
        return $this;
    }

    /**
     * Surcharge de getGridColumns pour renvoyer dynamiquement le nombre de colonnes.
     *
     * La méthode doit retourner array|string|int|null.
     */
    public function getGridColumns(?string $breakpoint = null): array|string|int|null
    {
        if ($this->autoGrid) {
            $items = $this->getState();
            if (is_array($items)) {
                // Calculer le nombre de colonnes en fonction du nombre d'items,
                // avec au minimum 1 et au maximum $maxGridColumns.
                $columnsCount = min($this->maxGridColumns, max(1, count($items)));
                // Met à jour gridColumns sous forme de tableau (par exemple, pour le breakpoint 'lg').
                $this->gridColumns = ['lg' => $columnsCount];

                if ($breakpoint !== null) {
                    return $this->gridColumns[$breakpoint] ?? null;
                }
                return $this->gridColumns;
            }
        }
        return parent::getGridColumns($breakpoint);
    }
}
