<div style="max-height: 70vh; overflow-y: auto; padding-right: 12px;">

    @forelse ($logs as $log)

        <div
            style="
                border-radius: 12px;
                margin-bottom: 16px;
            "

            class="
                border border-gray-200
                bg-white
                dark:border-gray-700
                dark:bg-gray-900
            "

            class="
                dark:!bg-gray-900
                dark:!border-gray-700
            "
        >

            <div
                style="font-weight: 600; margin-bottom: 10px;"
                class="text-gray-900 dark:text-white"
            >

                {{ $log->event }}

            </div>

            <div
                style="font-size: 12px; margin-bottom: 12px; text-align: end;"
                class="text-gray-500 dark:text-gray-400"
            >

                {{ $log->created_at->format('d/m/Y H:i') }}

                @if ($log->user)
                    · {{ $log->user->name }}
                @endif

            </div>

            @if (!empty($log->changes))

                @foreach ($log->changes as $field => $values)

                    <div
                        style="
                            border-radius: 8px;
                            
                            margin-top: 8px;
                            font-size: 12px;
                            background: #f9fafb;
                        "

                        class="
                            dark:!bg-gray-800
                        "
                    >

                        <strong class="text-gray-900 dark:text-white">

                            {{ $field }}

                        </strong>

                        <div class="text-gray-700 dark:text-gray-300">

                            From:
                            {{ $values['old'] ?? '—' }}

                        </div>

                        <div class="text-gray-700 dark:text-gray-300">

                            To:
                            {{ $values['new'] ?? '—' }}

                        </div>

                    </div>

                @endforeach

            @endif

        </div>

    @empty

        <div class="text-gray-500 dark:text-gray-400">

            No audit logs found.

        </div>

    @endforelse

</div>