@props(['value' => null])

<x-filament::input.wrapper disabled>
    <span class="fi-input">{{ $value ?? '—' }}</span>
</x-filament::input.wrapper>
