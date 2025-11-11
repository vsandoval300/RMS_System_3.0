@php
    /** @var \Illuminate\Database\Eloquent\Model|null $record */
    $record = $getRecord();
    $logs = $record
        ? $record->auditLogs()->with('user')->latest()->get()
        : collect();
@endphp

<div class="flex flex-col gap-4">
    {{-- Título general del modal --}}
    <div class="mt-2">
        <h3 class="text-lg font-semibold">
            Audit info
        </h3>
    </div>

    {{-- 🔹 Burbuja de Change history con alto fijo y scroll interno --}}
    <div class="rounded-xl border border-gray-200/80 bg-white/60 px-4 py-3
                dark:border-white/10 dark:bg-gray-900/60">

        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            Change history
        </h4>

        @if ($logs->isEmpty())
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                No changes registered yet.
            </p>
        @else
            <div
                class="mt-2 space-y-2 text-xs pr-2 overflow-y-auto"
                style="max-height: 400px;"  {{-- 🔧 altura de la burbuja (ajusta a gusto) --}}
            >
                @foreach ($logs as $log)
                    <div class="rounded-md border border-gray-200/80 px-3 py-2 space-y-1 shadow-sm
                                dark:border-white/10">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">
                                {{ $log->event }}
                            </span>

                            <span class="text-gray-500 dark:text-gray-400">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                                @if ($log->user)
                                    · {{ $log->user->name }}
                                @endif
                            </span>
                        </div>

                        @if (!empty($log->changes))
                            <ul class="mt-1 list-disc list-inside space-y-0.5">
                                @foreach ($log->changes as $field => $values)
                                    <li>
                                        <span class="font-medium">{{ $field }}</span>:
                                        changed from ["{{ $values['old'] ?? '' }}"]
                                        to ["{{ $values['new'] ?? '' }}"]
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
