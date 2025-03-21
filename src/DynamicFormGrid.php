<?php

namespace Macymed\Filament\DynamicFormGrid;

// use Filament\Support\Components\Component;
use Filament\Forms\Components\Component; // Changez cette importation
// use Filament\Support\Components\Component; // Commentez ou supprimez cette ligne
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Closure;
class DynamicFormGrid extends Component
{
    protected string $view = 'macymed-dynamic-form-grid::dynamic-form-grid';
    
    protected array $data = [];
    
    protected array $blocks = [];
    
    protected Model|Closure|string|null $model = null;
    
    public function __construct()
    {
        // Constructeur vide
    }
    
    public static function make(): static
    {
        return app(static::class);
    }
    
    public function data($data): static
    {
        $this->data = $data;
        
        return $this;
    }
    
    public function blocks(array $blocks): static
    {
        $this->blocks = $blocks;
        
        return $this;
    }
    
    public function model(Model|Closure|string|null $model = null ): static
    {
        $this->model = $model;
        
        return $this;
    }
    
    // public function render(): View
    // {
    //     return view($this->view, [
    //         'data' => $this->data,
    //         'blocks' => $this->blocks,
    //         'model' => $this->model,
    //     ]);
    // }
     // Cette méthode génère le schéma à utiliser par le composant
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
             
             // Calculer le nombre total de colonnes dans cette section
             $columnCount = count($columns);
             
             foreach ($columns as $columnIndex => $column) {
                 $columnItems = $column['data']['items'] ?? [];
                 $columnFields = [];
                 
                 foreach ($columnItems as $item) {
                     // Trouver le block correspondant dans les blocks fournis
                     $matchingBlock = $this->findMatchingBlock($item);
                     
                     if ($matchingBlock) {
                         $columnFields[] = $matchingBlock;
                     }
                 }
                 
                 // Calculer la largeur de la colonne en fonction du nombre total de colonnes
                 $span = 12 / $columnCount;
                 
                 if (!empty($columnFields)) {
                     $columnsSchema[] = Grid::make()
                         ->schema($columnFields)
                         ->columnSpan($span);
                 }
             }
             
             if (!empty($columnsSchema)) {
                 $schema[] = Section::make()
                     ->schema([
                         Grid::make(12)
                             ->schema($columnsSchema)
                     ]);
             }
         }
         
         return $schema;
     }
     
     protected function findMatchingBlock(array $item): ?Component
     {
         if (!isset($item['data']['identifiant'])) {
             return null;
         }
         
         $identifier = $item['data']['identifiant'];
         
         // Parcourir les blocks fournis pour trouver celui correspondant à l'identifiant
         foreach ($this->blocks as $block) {
             if (is_object($block) && method_exists($block, 'getId')) {
                 // Si c'est déjà un objet Component avec une méthode getId
                 if ($block->getId() === $identifier) {
                     return $block;
                 }
             } elseif (is_array($block) && isset($block['data']['identifiant']) && $block['data']['identifiant'] === $identifier) {
                 // Si c'est un tableau avec un identifiant
                 // Ici vous pourriez créer un composant à partir des données du block
                 // Ou simplement retourner le block tel quel s'il est déjà un Component
                 if (isset($block['component']) && $block['component'] instanceof Component) {
                     return $block['component'];
                 }
             }
         }
         
         return null;
     }
     
     // Surcharger la méthode getChildComponents de Component
     public function getChildComponents(): array
     {
         return $this->schema;
     }
}