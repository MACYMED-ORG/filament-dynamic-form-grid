<?php

namespace Macymed\Filament\DynamicFormGrid;

use Filament\Forms\Components\Repeater;

class DynamicGridRepeater extends Repeater
{
    /**
     * Active ou désactive l'ajustement automatique du nombre de colonnes.
     *
     * @var bool
     */
    protected bool $autoGrid = false;

    /**
     * Nombre maximum de colonnes (plafond) pour l'affichage en grille.
     *
     * @var int
     */
    protected int $maxGridColumns = 4;

    /**
     * Active l'auto-grid.
     *
     * @param bool $condition
     * @param int  $maxColumns Nombre maximum de colonnes (ex. 4)
     * @return $this
     */
    public function autoGrid(bool $condition = true, int $maxColumns = 4): static
    {
        $this->autoGrid = $condition;
        $this->maxGridColumns = $maxColumns;
        return $this;
    }

    /**
     * Retourne dynamiquement le nombre de colonnes à afficher.
     *
     * Si l'auto-grid est activé, le nombre de colonnes sera égal au nombre d'items,
     * borné entre 1 et le maximum défini ($maxGridColumns). Sinon, on renvoie la valeur
     * par défaut ou celle définie via ->columns().
     *
     * @return int|null
     */
    public function getColumns(): ?int
    {
        if ($this->autoGrid) {
            // getState() retourne les items actuels du repeater
            $items = $this->getState();
            if (is_array($items)) {
                $count = count($items);
                return min($this->maxGridColumns, max(1, $count));
            }
        }
        return parent::getColumns();
    }
}
