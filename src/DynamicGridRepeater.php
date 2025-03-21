<?php

namespace Macymed\Filament\DynamicFormGrid;

use Filament\Forms\Components\Repeater;

class DynamicGridRepeater extends Repeater
{
    /**
     * Active ou désactive l'ajustement automatique du nombre de colonnes.
     */
    protected bool $autoGrid = false;

    /**
     * Nombre maximum de colonnes autorisées (plafond).
     */
    protected int $maxGridColumns = 4;

    /**
     * Active ou désactive l'autoGrid.
     *
     * @param bool $condition
     * @param int $maxColumns Nombre maximum de colonnes.
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
     * La signature doit être compatible avec :
     * public function getGridColumns(?string $breakpoint = null): array|string|int|null
     */
    public function getGridColumns(?string $breakpoint = null): array|string|int|null
    {
        if ($this->autoGrid) {
            $items = $this->getState();
            if (is_array($items)) {
                // Le nombre de colonnes est égal au nombre d'items, minimum 1 et maximum $maxGridColumns.
                $columnsCount = min($this->maxGridColumns, max(1, count($items)));
                // On peut aussi mettre à jour la propriété gridColumns pour que la méthode grid() le prenne en compte.
                $this->gridColumns = $columnsCount;
                return $columnsCount;
            }
        }

        return parent::getGridColumns($breakpoint);
    }
}
