@php
    $name = trim($getState());

    $parts = preg_split('/\s+/', $name) ?: [];

    $first = mb_substr($parts[0] ?? '', 0, 1);
    $last = mb_substr($parts[count($parts) - 1] ?? '', 0, 1);

    $initials = mb_strtoupper($first . ($last !== $first ? $last : ''));
@endphp

<div class="flex items-center gap-x-3">
    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-500 text-xs font-semibold text-white">
        {{ $initials }}
    </div>

    <span>
        {{ $name }}
    </span>
</div>