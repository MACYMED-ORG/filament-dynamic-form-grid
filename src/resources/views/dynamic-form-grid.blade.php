{{-- resources/views/dynamic-form-grid.blade.php --}}
<div {{ $attributes->merge($getExtraAttributes()) }}>
    @if ($label = $getLabel())
        <label class="block font-medium text-sm text-gray-700">{{ $label }}</label>
    @endif

    <div class="mt-2 space-y-4">
        @foreach ($getChildComponents() as $child)
            {{-- On s'assure que chaque enfant est bien un composant et on appelle render() --}}
            @if($child instanceof \Filament\Forms\Components\Component)
                {!! $child->render() !!}
            @endif
        @endforeach
    </div>
</div>
