<div x-data="gridBuilder({{ $getState() ? json_encode($getState()) : '[]' }})" x-init="init()">
    <div class="space-y-4">
        <!-- Itération sur les lignes -->
        <template x-for="(row, rowIndex) in grid" :key="rowIndex">
            <div class="border p-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold">Row <span x-text="rowIndex + 1"></span></h3>
                    <button type="button" x-on:click="removeRow(rowIndex)" class="text-red-500">Remove Row</button>
                </div>
                <div class="grid gap-4 mt-4" :class="'grid-cols-' + row.columns.length">
                    <!-- Itération sur les colonnes -->
                    <template x-for="(column, colIndex) in row.columns" :key="colIndex">
                        <div class="border p-2">
                            <div class="flex justify-between items-center">
                                <h4 class="font-semibold">Column <span x-text="colIndex + 1"></span></h4>
                                <button type="button" x-on:click="removeColumn(rowIndex, colIndex)" class="text-red-500">Remove Column</button>
                            </div>
                            <div class="mt-2 space-y-2">
                                <!-- Itération sur les blocs dans la colonne -->
                                <template x-for="(block, blockIndex) in column.blocks" :key="blockIndex">
                                    <div class="border p-1 flex justify-between items-center">
                                        <span x-text="block.type"></span>
                                        <button type="button" x-on:click="removeBlock(rowIndex, colIndex, blockIndex)" class="text-red-500">X</button>
                                    </div>
                                </template>
                                <button type="button" x-on:click="addBlock(rowIndex, colIndex)" class="text-blue-500 mt-2">Add Block</button>
                            </div>
                        </div>
                    </template>
                </div>
                <button type="button" x-on:click="addColumn(rowIndex)" class="mt-2 text-blue-500">Add Column</button>
            </div>
        </template>
        <button type="button" x-on:click="addRow()" class="mt-4 text-green-500">Add Row</button>
    </div>
    <!-- Champ caché qui contient la valeur JSON de la grille -->
    <input type="hidden" name="{{ $getStatePath() }}" x-model="jsonGrid">
</div>

<script>
    function gridBuilder(initialData) {
        return {
            grid: initialData,
            jsonGrid: JSON.stringify(initialData),
            init() {
                // Met à jour le champ caché lorsque la grille change (avec $watch pour les changements profonds)
                this.$watch('grid', value => {
                    this.jsonGrid = JSON.stringify(value);
                }, { deep: true });
            },
            addRow() {
                this.grid.push({
                    columns: [
                        { blocks: [] }
                    ]
                });
            },
            removeRow(rowIndex) {
                this.grid.splice(rowIndex, 1);
            },
            addColumn(rowIndex) {
                this.grid[rowIndex].columns.push({ blocks: [] });
            },
            removeColumn(rowIndex, colIndex) {
                this.grid[rowIndex].columns.splice(colIndex, 1);
            },
            addBlock(rowIndex, colIndex) {
                // Exemple : ajouter un bloc de type 'text' (vous pourrez l'adapter)
                this.grid[rowIndex].columns[colIndex].blocks.push({
                    type: 'text',
                    content: ''
                });
            },
            removeBlock(rowIndex, colIndex, blockIndex) {
                this.grid[rowIndex].columns[colIndex].blocks.splice(blockIndex, 1);
            },
        }
    }
</script>
