<?php

namespace Macymed\Filament\DynamicFormGrid;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;

class DynamicFormGrid extends Field
{
    // La vue utilisée pour ce composant (le nom du namespace doit correspondre à celui défini dans le service provider)
    protected string $view = 'filament-macymed-dynamic-form-grid::dynamic-form-grid';

    protected array $data = [];
    protected array $blocks = [];

    /**
     * Crée une instance du composant en liant le nom du champ.
     */
    public static function make(string $name): static
    {
        // Utilisation de la méthode parent::make() pour créer le composant Field
        $static = parent::make($name);
        // Génère le schéma initial en fonction des données (s'il y en a déjà)
        $static->schema($static->generateSchema());
        return $static;
    }

    /**
     * Définit les données JSON (la structure) du formulaire dynamique.
     */
    public function data($data): static
    {
        $this->data = $data;
        // Reconstruit le schéma en fonction des nouvelles données
        $this->schema($this->generateSchema());
        return $this;
    }

    /**
     * Définit les blocs dynamiques (ex. renvoyés par FormRegistrationBuilder::getBlocksElements(...)).
     */
    public function blocks(array $blocks): static
    {
        $this->blocks = $blocks;
        // Reconstruit le schéma pour prendre en compte les blocs
        $this->schema($this->generateSchema());
        return $this;
    }

    /**
     * Construit le schéma (tableau de composants) à partir des données JSON.
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

            // S'assurer qu'il y a au moins une colonne
            if ($columnCount === 0) {
                continue;
            }

            foreach ($columns as $column) {
                $columnItems = $column['data']['items'] ?? [];
                $columnFields = [];

                foreach ($columnItems as $item) {
                    // Création du composant dynamique à partir de l'item
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
     * Ici, on utilise TextInput en exemple.  
     * Si besoin, la logique peut être étendue pour gérer différents types de blocs.
     */
    protected function createDynamicBlockComponent(array $item): ?Component
    {
        $identifiant = $item['data']['identifiant'] ?? null;
        $label = $item['data']['label'] ?? 'Champ';

        if (!$identifiant) {
            return null;
        }

        // Exemple générique : on crée un TextInput
        return TextInput::make($identifiant)
            ->label($label)
            ->required($item['data']['required'] ?? false);
    }

    /**
     * Retourne les composants enfants pour le rendu.
     */
    public function getChildComponents(): array
    {
        // Utilise getSchema() qui est géré par la classe Field
        return $this->getSchema();
    }
}
