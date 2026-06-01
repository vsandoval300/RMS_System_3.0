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
                                
                            >
                                {{ $field }}
                            </div>

                            <div
                                
                            >
                                <span class="font-medium">
                                    From:
                                </span>

                                {{ $values['old'] ?? '—' }}
                            </div>

                            <div
                                
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