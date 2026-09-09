@props(['value' => null])

<x-filament::input.wrapper disabled>
    <input
        type="text"
        class="fi-input"
        value="{{ $value ?? '—' }}"
        disabled
        readonly
        tabindex="-1"
        style="border: none; outline: none; box-shadow: none; background: transparent; appearance: none;"
    />
</x-filament::input.wrapper>
