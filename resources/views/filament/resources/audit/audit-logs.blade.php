<div
    class="
        max-h-[75vh]
        overflow-y-auto
        pr-2
        space-y-2
    "
>

    @forelse ($logs as $log)

        <div
            class="
                fi-section
                rounded-lg
                border
                border-gray-200
                px-3
                py-2

                dark:border-gray-700
            "
        >

            <!-- Header -->
            <div class="min-w-0">

                <div
                    class="
                        text-xs
                        font-semibold
                        leading-tight
                        break-words
                    "
                >
                    {{ $log->event }}
                </div>

                <div
                    class="
                        mt-1
                        text-[11px]
                        text-gray-500
                        dark:text-gray-400
                    "
                >

                    {{ $log->created_at->format('d/m/Y H:i') }}

                    @if ($log->user)
                        · {{ $log->user->name }}
                    @endif

                </div>

            </div>

            <!-- Changes -->
            @if (!empty($log->changes))

                <div class="mt-3 space-y-1.5">

                    @foreach ($log->changes as $field => $values)

                        <div
                            class="
                                rounded-md
                                border
                                border-gray-200
                                bg-gray-50
                                px-2
                                py-1.5

                                dark:border-gray-700
                                dark:bg-gray-800
                            "
                        >

                            <div
                                class="
                                    text-[11px]
                                    font-semibold
                                    break-words
                                    mb-1
                                "
                            >
                                {{ $field }}
                            </div>

                            <div
                                class="
                                    text-[11px]
                                    leading-tight
                                    text-gray-600
                                    dark:text-gray-300
                                    break-words
                                "
                            >
                                <span class="font-medium">
                                    From:
                                </span>

                                {{ $values['old'] ?? '—' }}
                            </div>

                            <div
                                class="
                                    text-[11px]
                                    leading-tight
                                    text-gray-600
                                    dark:text-gray-300
                                    break-words
                                "
                            >
                                <span class="font-medium">
                                    To:
                                </span>

                                {{ $values['new'] ?? '—' }}
                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>

    @empty

        <div class="text-xs text-gray-500 dark:text-gray-400">
            No audit logs found.
        </div>

    @endforelse

</div>