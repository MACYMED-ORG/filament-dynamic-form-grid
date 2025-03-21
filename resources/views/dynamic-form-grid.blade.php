<div {{ $attributes->merge($getExtraAttributes()) }}>
    @if ($label = $getLabel())
        <label class="block font-medium text-sm text-gray-700">{{ $label }}</label>
    @endif

    <div class="mt-2 space-y-4">
        @foreach ($getChildComponents() as $child)
            {{-- Chaque enfant est une instance de Component, on appelle son render() --}}
            {!! $child->render() !!}
        @endforeach
    </div>
</div>
