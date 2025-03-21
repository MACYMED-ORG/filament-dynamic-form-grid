<?php

namespace Macymed\Filament\DynamicFormGrid;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;

class DynamicFormGrid extends Field
{
    // On utilise une vue personnalisée.
    protected string $view = 'filament-macymed-dynamic-form-grid::dynamic-form-grid';

    // Ces propriétés stockent la structure JSON et la liste des blocs (instances de Block fournies par le développeur)
    protected array $data = [];
    protected array $blocks = [];

    /**
     * Crée le composant en liant le nom (pour le binding sur le modèle).
     */
    public static function make(string $name): static
    {
        // Field::make() gère la liaison automatique avec l'attribut du modèle.
        $static = parent::make($name);
        $static->schema($static->generateSchema());
        return $static;
    }

    /**
     * Définit la structure JSON qui décrit le formulaire dynamique.
     *
     * La structure doit être un tableau d'éléments, par exemple :
     * [
     *   [
     *     "type" => "section",
     *     "data" => [
     *         "columns" => [
     *             [
     *                "data" => [
     *                    "items" => [
     *                        [ "type" => "field-text", "data" => [ "identifiant" => "nom", "label" => "Nom", "required" => true ] ],
     *                        ...
     *                    ]
     *                ]
     *             ],
     *             // éventuellement d'autres colonnes
     *         ]
     *     ]
     *   ],
     *   // éventuellement d'autres sections
     * ]
     */
    public function data($data): static
    {
        $this->data = $data;
        $this->schema($this->generateSchema());
        return $this;
    }

    /**
     * Définit la liste des blocs dynamiques.
     *
     * Ces blocs sont fournis par exemple par FormRegistrationBuilder::getBlocksElements($form, $mpEvent)
     * et doivent contenir, pour chaque bloc, au moins un identifiant et un composant (accessible via une méthode getComponent() ou une clé 'component').
     */
    public function blocks(array $blocks): static
    {
        $this->blocks = $blocks;
        $this->schema($this->generateSchema());
        return $this;
    }

    /**
     * Génère le schéma du formulaire à partir de la structure JSON.
     *
     * Le schéma retourné est un tableau d'instances de Component (Section, Grid, TextInput, etc.).
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

                // Calcul du span en fonction du nombre de colonnes.
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
     * Crée un composant Filament à partir d'un bloc de données.
     *
     * Si un bloc correspondant est trouvé dans $this->blocks (via l'identifiant), on retourne son composant.
     * Sinon, on crée un composant générique (TextInput dans cet exemple).
     */
    protected function createDynamicBlockComponent(array $item): ?Component
    {
        $identifiant = $item['data']['identifiant'] ?? null;
        $label = $item['data']['label'] ?? 'Champ';
        if (!$identifiant) {
            return null;
        }

        // Recherche dans la liste des blocs fournis
        foreach ($this->blocks as $block) {
            // On s'attend à ce que le bloc ait une méthode getId() et getComponent().
            if (method_exists($block, 'getId') && $block->getId() === $identifiant) {
                $component = $block->getComponent();
                if ($component instanceof Component) {
                    return $component;
                }
            }
            // Si le bloc est un tableau et contient 'identifiant' et 'component'
            if (is_array($block)
                && isset($block['data']['identifiant'])
                && $block['data']['identifiant'] === $identifiant
                && isset($block['component'])
                && $block['component'] instanceof Component) {
                return $block['component'];
            }
        }

        // Si aucun bloc spécifique n'est trouvé, on crée un TextInput générique.
        return TextInput::make($identifiant)
            ->label($label)
            ->required($item['data']['required'] ?? false);
    }
}
