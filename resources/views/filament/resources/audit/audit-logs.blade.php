@php
    $logs = $record
        ? $record->auditLogs()
            ->with('user')
            ->latest()
            ->get()
        : collect();
@endphp

<div class="flex flex-col h-[80vh]">

    {{-- HEADER --}}
    <div class="px-6 pt-6 pb-4 shrink-0">

        <h2 class="text-lg font-semibold">
            Audit info
        </h2>

        <p class="text-sm text-gray-500 dark:text-gray-400">
            Change history
        </p>

    </div>

    {{-- BODY --}}
    <div class="flex-1 overflow-y-auto min-h-0 px-6 pb-6">

        <div class="space-y-3">

            @foreach ($logs as $log)

                <div class="rounded-xl border border-gray-200
                            dark:border-white/10
                            p-4">

                    <div class="flex items-start justify-between gap-4">

                        <div class="min-w-0 flex-1">

                            <div class="text-sm font-medium break-words">
                                {{ $log->event }}
                            </div>

                        </div>

                        <div class="shrink-0 text-xs text-gray-500 whitespace-nowrap">

                            {{ $log->created_at->format('d/m/Y H:i') }}

                            @if ($log->user)
                                · {{ $log->user->name }}
                            @endif

                        </div>

                    </div>

                    @if (!empty($log->changes))

                        <ul class="mt-3 space-y-2">

                            @foreach ($log->changes as $field => $values)

                                <li class="text-xs break-words leading-5">

                                    <span class="font-semibold">
                                        {{ $field }}
                                    </span>

                                    changed from

                                    <span class="font-mono">
                                        [{{ $values['old'] ?? '' }}]
                                    </span>

                                    to

                                    <span class="font-mono">
                                        [{{ $values['new'] ?? '' }}]
                                    </span>

                                </li>

                            @endforeach

                        </ul>

                    @endif

                </div>

            @endforeach

        </div>

    </div>

</div>