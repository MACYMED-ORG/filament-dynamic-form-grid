<?php

namespace Macymed\Filament\DynamicFormGrid;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;

class DynamicFormGrid extends Field
{
    // Utilise une vue personnalisée pour le rendu.
    protected string $view = 'filament-macymed-dynamic-form-grid::dynamic-form-grid';

    // Stocke la structure JSON et les blocs dynamiques.
    protected array $data = [];
    protected array $blocks = [];

    /**
     * Crée le composant en liant le nom (pour l'attribut du modèle).
     */
    public static function make(string $name): static
    {
        // On s'appuie sur Field::make() pour bénéficier du binding automatique.
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
        // Regénère le schéma en fonction des données.
        $this->schema($this->generateSchema());
        return $this;
    }

    /**
     * Définit les blocs dynamiques (par exemple renvoyés par FormRegistrationBuilder::getBlocksElements(...)).
     */
    public function blocks(array $blocks): static
    {
        $this->blocks = $blocks;
        $this->schema($this->generateSchema());
        return $this;
    }

    /**
     * Génère le schéma (tableau de composants) à partir du JSON.
     */
    protected function generateSchema(): array
    {
        $schema = [];

        if (empty($this->data) || !is_array($this->data)) {
            return $schema;
        }

        foreach ($this->data as $sectionItem) {
            if (!isset($sectionItem['type']) || $sectionItem['type'] !== 'section') {
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
                    // Crée le composant dynamique pour chaque bloc.
                    $matchingBlock = $this->createDynamicBlockComponent($item);
                    if ($matchingBlock instanceof Component) {
                        $columnFields[] = $matchingBlock;
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
                    ->schema([Grid::make(12)->schema($columnsSchema)]);
            }
        }

        return $schema;
    }

    /**
     * Crée un composant Filament à partir d'un bloc de données.
     * Ici, nous utilisons TextInput par défaut. Vous pourrez étendre cette logique pour gérer d'autres types.
     */
    protected function createDynamicBlockComponent(array $item): ?Component
    {
        $identifiant = $item['data']['identifiant'] ?? null;
        $label = $item['data']['label'] ?? 'Champ';

        if (!$identifiant) {
            return null;
        }

        return TextInput::make($identifiant)
            ->label($label)
            ->required($item['data']['required'] ?? false);
    }
}
