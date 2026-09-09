@props(['value' => null])

<x-filament::input.wrapper disabled>
    <input type="text" class="fi-input" value="{{ $value ?? '—' }}" disabled readonly tabindex="-1" />
</x-filament::input.wrapper>
