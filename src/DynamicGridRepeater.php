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
     * Nombre maximum de colonnes (plafond) pour l'affichage en grille.
     */
    protected int $maxGridColumns = 4;

    /**
     * Active/désactive l'ajustement automatique du nombre de colonnes.
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
     * Retourne dynamiquement le nombre de colonnes.
     *
     * La signature respecte celle attendue par Filament : getColumns(?string $breakpoint = null): array|string|int|null.
     */
    public function getColumns(?string $breakpoint = null): array|string|int|null
    {
        if ($this->autoGrid) {
            $items = $this->getState();
            if (is_array($items)) {
                $count = count($items);
                return min($this->maxGridColumns, max(1, $count));
            }
        }
        return parent::getColumns($breakpoint);
    }
}
