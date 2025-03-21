<div {{ $attributes->merge($getExtraAttributes()) }}>
    @if ($label = $getLabel())
        <label class="block font-medium text-sm text-gray-700">{{ $label }}</label>
    @endif

    <div class="mt-2 space-y-4">
        {!! $getChildComponentContainer() !!}
    </div>
</div>
