{{-- resources/views/dynamic-form-grid.blade.php --}}
<div class="macymed-dynamic-form-grid">
    @php
        $schema = $this->generateFormSchema();
    @endphp

    @if(!empty($schema))
        <div>
            @foreach($schema as $section)
                {{ $section }}
            @endforeach
        </div>
    @else
        <div class="p-4 text-center text-gray-500">
            Aucune donnée de formulaire trouvée.
        </div>
    @endif
</div>