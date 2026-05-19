@php
    $logs = $record
        ? $record->auditLogs()
            ->with('user')
            ->latest()
            ->get()
        : collect();
@endphp

<div class="px-6 py-4">

    {{-- HEADER --}}
    <div class="mb-4">

        <h2 class="text-lg font-semibold">
            Audit info
        </h2>

        <p class="text-sm text-gray-500 dark:text-gray-400">
            Change history
        </p>

    </div>

    {{-- BODY --}}
    <div class="space-y-4 overflow-y-auto pr-2" style="max-height: 65vh;">

        @foreach ($logs as $log)

            <div
                class="rounded-xl border border-gray-200 dark:border-white/10
                       bg-white dark:bg-gray-900
                       shadow-sm">

                {{-- TOP --}}
                <div
                    class="flex items-start justify-between gap-4
                           px-4 py-3 border-b border-gray-100
                           dark:border-white/5">

                    <div class="font-semibold text-sm">

                        {{ ucfirst($log->event) }}

                    </div>

                    <div class="text-xs text-gray-500 whitespace-nowrap">

                        {{ $log->created_at->format('d/m/Y H:i') }}

                        @if ($log->user)
                            · {{ $log->user->name }}
                        @endif

                    </div>

                </div>

                {{-- CHANGES --}}
                @if (!empty($log->changes))

                    <div class="p-4 space-y-3">

                        @foreach ($log->changes as $field => $values)

                            <div
                                class="rounded-lg bg-gray-50 dark:bg-white/5
                                       px-3 py-2">

                                <div
                                    class="text-xs font-semibold mb-2
                                           text-gray-700 dark:text-gray-200">

                                    {{ str($field)->headline() }}

                                </div>

                                <div class="space-y-1 text-xs">

                                    <div>

                                        <span class="text-gray-500">
                                            From:
                                        </span>

                                        <code
                                            class="font-mono text-red-600 dark:text-red-400 break-all">

                                            {{ $values['old'] ?? '—' }}

                                        </code>

                                    </div>

                                    <div>

                                        <span class="text-gray-500">
                                            To:
                                        </span>

                                        <code
                                            class="font-mono text-green-600 dark:text-green-400 break-all">

                                            {{ $values['new'] ?? '—' }}

                                        </code>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        @endforeach

    </div>

</div>