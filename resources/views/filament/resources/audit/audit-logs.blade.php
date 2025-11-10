@php
    /** @var \Illuminate\Database\Eloquent\Model|null $record */
    $record = $getRecord();
    $logs = $record
        ? $record->auditLogs()->with('user')->latest()->get() // quitamos el limit(15)
        : collect();
@endphp

<div class="mt-4">
    <h4 class="text-sm font-semibold text-gray-300">
        Change history
    </h4>

    @if ($logs->isEmpty())
        <p class="mt-2 text-xs text-gray-400">
            No changes registered yet.
        </p>
    @else
        <div
            class="mt-2 space-y-2 text-xs pr-2 overflow-y-auto"
            style="max-height: 200px;"
        >
            @foreach ($logs as $log)
                <div class="rounded-md border border-gray-700 px-3 py-2 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">
                            {{ $log->event }}
                        </span>

                        <span class="text-gray-400">
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
                                    "{{ $values['old'] ?? '' }}" →
                                    "{{ $values['new'] ?? '' }}"
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

