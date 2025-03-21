<?php
namespace Macymed\Filament\DynamicFormGrid;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasName;
use Filament\Forms\Components\Concerns\HasState;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

use Illuminate\Database\Eloquent\Model;
use Closure;

class DynamicFormGrid extends Field
{
    
    protected string $view = 'macymed-dynamic-form-grid::dynamic-form-grid';

    protected array $data = [];
    protected array $blocks = [];
    protected string $fieldName;

   

    public static function make(string $name): static
    {
        $static = parent::make($name);
        $static->schema($static->generateSchema());
        return $static;
    }
    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     $this->default([]);
    //     $this->schema($this->generateSchema());
        
    // }

    public function data($data): static
    {
        $this->data = $data;

        // Mettre à jour le schéma après avoir changé les données
        $this->schema($this->generateSchema());

        return $this;
    }

    public function blocks(array $blocks): static
    {
        $this->blocks = $blocks;

        // Mettre à jour le schéma après avoir changé les blocs
        $this->schema($this->generateSchema());

        return $this;
    }

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

            foreach ($columns as $columnIndex => $column) {
                $columnItems = $column['data']['items'] ?? [];
                $columnFields = [];

                foreach ($columnItems as $item) {
                    // Crée les blocs dynamiques à partir des données de l'élément
                    $matchingBlock = $this->createDynamicBlockComponent($item);

                    if ($matchingBlock) {
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

    protected function createDynamicBlockComponent(array $item): ?Component
    {
        // Crée un composant dynamique en fonction du type de bloc (par exemple : champ texte, champ sélection)
        $identifiant = $item['data']['identifiant'] ?? null;
        $label = $item['data']['label'] ?? 'Champ';

        if (!$identifiant) {
            return null;
        }

        // Cas génériques pour chaque type de champ
        return TextInput::make($identifiant)
            ->label($label)
            ->required($item['data']['required'] ?? false);
    }

    // Surcharger la méthode getChildComponents de Component
    public function getChildComponents(): array
    {
        return $this->schema;
    }
}
