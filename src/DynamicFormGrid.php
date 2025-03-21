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
    // Spécifiez la vue de rendu du composant (vérifiez que le namespace correspond à celui défini dans le service provider)
    protected string $view = 'filament-macymed-dynamic-form-grid::dynamic-form-grid';

    // Ces propriétés stockent la structure JSON et la liste des blocs dynamiques
    protected array $data = [];
    protected array $blocks = [];

    /**
     * Crée le composant en liant le nom (pour le binding automatique sur le modèle).
     */
    public static function make(string $name): static
    {
        // On s'appuie sur Field::make() pour bénéficier du binding sur l'attribut du modèle.
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
        $this->schema($this->generateSchema());
        return $this;
    }

    /**
     * Définit la liste des blocs dynamiques.
     * Ces blocs proviennent par exemple de FormRegistrationBuilder::getBlocksElements($form, $mpEvent)
     * et doivent permettre, via leur identifiant, de récupérer un composant Filament.
     */
    public function blocks(array $blocks): static
    {
        $this->blocks = $blocks;
        $this->schema($this->generateSchema());
        return $this;
    }

    /**
     * Génère le schéma (tableau de composants) à partir de la structure JSON.
     *
     * La hiérarchie est construite de la manière suivante :
     * Section → Grid (pour les colonnes) → Grid (pour chaque colonne, avec columnSpan) → composants.
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
     * - Si un bloc correspondant est trouvé dans $this->blocks (via l’identifiant), on récupère son composant.
     * - Si ce composant est une instance de View, on l’enveloppe dans un Html.
     * - Sinon, on crée un composant TextInput générique.
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
            // Si le bloc est directement une instance de View, enveloppez-le.
            if ($block instanceof View) {
                return Html::make($identifiant)
                    ->html($block->render());
            }
            // Si le bloc est un objet qui a une méthode getId() et getComponent()
            if (is_object($block) && method_exists($block, 'getId') && $block->getId() === $identifiant) {
                $comp = $block->getComponent();
                if ($comp instanceof Component) {
                    return $comp;
                }
                if ($comp instanceof View) {
                    return Html::make($identifiant)
                        ->html($comp->render());
                }
            }
            // Si le bloc est un tableau contenant 'data' et 'component'
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
                    return Html::make($identifiant)
                        ->html($comp->render());
                }
            }
        }

        // Par défaut, crée un TextInput générique.
        return TextInput::make($identifiant)
            ->label($label)
            ->required($item['data']['required'] ?? false);
    }

    /**
     * Surcharge de getChildComponents pour renvoyer explicitement le schéma.
     */
    public function getChildComponents(): array
    {
        return $this->getSchema();
    }
}
