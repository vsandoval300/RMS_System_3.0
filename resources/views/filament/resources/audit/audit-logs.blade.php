<div class="audit-modal-content" style="max-height: 45vh !important;">

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
            style="margin-top: calc(0.25rem * calc(1 - 0));
                    margin-bottom: calc(0.25rem * 0);
                    border-radius: 0.375rem;
                    border-width: 1px;
                    border-color: rgb(229,231,235,0.8);
                    padding-left: 0.75rem;
                    padding-right: 0.75rem;
                    padding-top: 0.5rem;
                    padding-bottom: 0.5rem;
                    box-shadow: 0 0 #0000, 0 0 #0000, 0 1px 2px 0 rgb(0,0,0,0.05);"
        >

            <!-- Header -->
            <div class="min-w-0">

                <div
                    
                >
                    {{ $log->event }}
                </div>

                <div
                style="font-size: small; 
                    justify-content: space-between;
                    text-align: end;">
                

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
                            
                        >

                            <div
                                style="font-size: small;"
                            >
                                {{ $field }}
                            </div>

                            <div
                                style="font-size: small;"
                            >
                                <span class="font-small" style="font-size: small;">
                                    From:
                                </span>

                                {{ $values['old'] ?? '—' }}
                            </div>

                            <div
                                style="font-size: small;"
                            >
                                <span class="font-small" style="font-size: small;">
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