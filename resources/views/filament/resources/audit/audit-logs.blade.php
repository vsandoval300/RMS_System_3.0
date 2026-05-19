<div style="max-height: 70vh; overflow-y: auto; padding-right: 12px;">

    @forelse ($logs as $log)

        <div
            style="
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 16px;
                background: #ffffff;
            "
        >

            <div style="font-weight: 600; margin-bottom: 8px;">

                {{ $log->event }}

            </div>

            <div style="font-size: 12px; color: #6b7280; margin-bottom: 12px;">

                {{ $log->created_at->format('d/m/Y H:i') }}

                @if ($log->user)
                    · {{ $log->user->name }}
                @endif

            </div>

            @if (!empty($log->changes))

                @foreach ($log->changes as $field => $values)

                    <div
                        style="
                            background: #f9fafb;
                            border-radius: 8px;
                            padding: 8px;
                            margin-top: 8px;
                            font-size: 12px;
                        "
                    >

                        <strong>{{ $field }}</strong>

                        <div>

                            From:
                            {{ $values['old'] ?? '—' }}

                        </div>

                        <div>

                            To:
                            {{ $values['new'] ?? '—' }}

                        </div>

                    </div>

                @endforeach

            @endif

        </div>

    @empty

        <div style="color: #6b7280;">

            No audit logs found.

        </div>

    @endforelse

</div>