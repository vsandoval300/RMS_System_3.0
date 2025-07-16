@php
    $docs = $record->operativeDocs ?? collect();
@endphp

<div x-data="{ open: false }" class="relative">
    <button
        x-on:click="open = !open"
        class="text-sm text-blue-400 hover:underline"
    >
        {{ $docs->count() }} document(s)
    </button>

    <template x-if="open">
        <div class="mt-2 space-y-1 p-3 rounded-lg bg-gray-800 border border-gray-700 shadow-lg text-sm">
            @if ($docs->count())
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-400 border-b border-gray-600">
                            <th class="py-1 pr-2">Description</th>
                            <th class="py-1 pr-2">Start</th>
                            <th class="py-1 pr-2">End</th>
                            <th class="py-1">PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($docs as $doc)
                            <tr class="border-b border-gray-700">
                                <td class="py-1 pr-2">{{ $doc->description }}</td>
                                <td class="py-1 pr-2">{{ $doc->inception_date?->format('Y-m-d') }}</td>
                                <td class="py-1 pr-2">{{ $doc->expiration_date?->format('Y-m-d') }}</td>
                                <td class="py-1">
                                    @if ($doc->document_path)
                                        <a href="{{ Storage::url($doc->document_path) }}" target="_blank" class="text-blue-400 hover:underline">View</a>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-xs text-gray-400 italic">No documents found.</p>
            @endif
        </div>
    </template>
</div>
