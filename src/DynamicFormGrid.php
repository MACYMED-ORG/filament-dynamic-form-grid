<?php

namespace Macymed\Filament\DynamicFormGrid;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Html;
use Filament\Forms\Components\Component;
use Illuminate\View\View;

class DynamicFormGrid extends Field
{
    // On utilise une vue personnalisée pour le rendu.
    protected string $view = 'filament-macymed-dynamic-form-grid::dynamic-form-grid';

    // Ces propriétés stockent la structure JSON et la liste des blocs dynamiques.
    protected array $data = [];
    protected array $blocks = [];

    /**
     * Crée le composant en liant le nom (pour le binding automatique avec le modèle).
     */
    public static function make(string $name): static
    {
        // Field::make() gère le binding automatique sur l'attribut du modèle.
        $static = parent::make($name);
        $static->schema($static->generateSchema());
        return $static;
    }

    /**
     * Définit la structure JSON du formulaire dynamique.
     */
    public function data($data): static
    {
        $this->data = $data;
        // Regénère le schéma en fonction des nouvelles données.
        $this->schema($this->generateSchema());
        return $this;
    }

    /**
     * Définit la liste des blocs dynamiques.
     *
     * Chaque bloc doit contenir au moins un identifiant et un composant.
     */
    public function blocks(array $blocks): static
    {
        $this->blocks = $blocks;
        $this->schema($this->generateSchema());
        return $this;
    }

    /**
     * Génère le schéma (tableau de composants) à partir de la structure JSON.
     */
    protected function generateSchema(): array
    {
        $schema = [];

        if (empty($this->data) || !is_array($this->data)) {
            return $schema;
        }

        foreach ($this->data as $sectionItem) {
            if (($sectionItem['type'] ?? null) !== 'section') {
                continue;
            }

            $sectionData = $sectionItem['data'] ?? [];
            $columns = $sectionData['columns'] ?? [];
            $columnsSchema = [];
            $columnCount = count($columns);
            if ($columnCount === 0) {
                continue;
            }

            foreach ($columns as $column) {
                $columnItems = $column['data']['items'] ?? [];
                $columnFields = [];

                foreach ($columnItems as $item) {
                    $component = $this->createDynamicBlockComponent($item);
                    if ($component instanceof Component) {
                        $columnFields[] = $component;
                    }
                }

                $span = 12 / $columnCount;
                if (!empty($columnFields)) {
                    $columnsSchema[] = Grid::make()
                        ->schema($columnFields)
                        ->columnSpan($span);
                }
            }

            if (!empty($columnsSchema)) {
                $schema[] = Section::make()
                    ->schema([Grid::make()->schema($columnsSchema)]);
            }
        }

        return $schema;
    }

    /**
     * Crée un composant Filament à partir d’un bloc de données.
     *
     * – Si un bloc correspondant est trouvé dans $this->blocks (via l’identifiant), on retourne son composant.
     * – Si ce composant est déjà une vue, on l’enveloppe dans un composant Html.
     * – Sinon, on crée un composant TextInput générique.
     */
    protected function createDynamicBlockComponent(array $item): ?Component
    {
        $identifiant = $item['data']['identifiant'] ?? null;
        $label = $item['data']['label'] ?? 'Champ';

        if (!$identifiant) {
            return null;
        }

        // Recherche dans la liste des blocs fournis.
        foreach ($this->blocks as $block) {
            // Si le bloc est un objet possédant les méthodes getId() et getComponent().
            if (method_exists($block, 'getId') && $block->getId() === $identifiant) {
                $comp = $block->getComponent();
                if ($comp instanceof Component) {
                    return $comp;
                }
                if ($comp instanceof View) {
                    return Html::make($identifiant)->html($comp->render());
                }
            }
            // Si le bloc est un tableau contenant 'data' et 'component'.
            if (
                is_array($block)
                && isset($block['data']['identifiant'])
                && $block['data']['identifiant'] === $identifiant
                && isset($block['component'])
            ) {
                $comp = $block['component'];
                if ($comp instanceof Component) {
                    return $comp;
                }
                if ($comp instanceof View) {
                    return Html::make($identifiant)->html($comp->render());
                }
            }
        }

        // Par défaut, crée un TextInput générique.
        return TextInput::make($identifiant)
            ->label($label)
            ->required($item['data']['required'] ?? false);
    }
}
