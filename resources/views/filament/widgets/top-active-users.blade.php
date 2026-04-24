<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach ($this->getTopUsers() as $index => $user)
        @php
            $colors = [
                'bg-yellow-100 border-yellow-400', // 🥇
                'bg-green-100 border-green-400',     // 🥈
                'bg-orange-100 border-orange-400', // 🥉
            ];
        @endphp

        <div class="p-6 rounded-2xl border {{ $colors[$index] }} shadow-sm">
            <div class="flex items-center gap-4">

                {{-- Avatar --}}
                <img 
                    src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                    class="w-14 h-14 rounded-full"
                >

                <div>
                    <div class="text-lg font-semibold">
                        #{{ $index + 1 }} {{ $user->name }}
                    </div>

                    <div class="text-sm text-gray-500">
                        {{ $user->email }}
                    </div>
                </div>
            </div>

            <div class="mt-4 text-3xl font-bold">
                {{ $user->login_logs_count }}
            </div>

            <div class="text-sm text-gray-500">
                Logins
            </div>
        </div>
    @endforeach
</div>