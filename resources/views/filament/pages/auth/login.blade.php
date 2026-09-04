<x-filament-panels::page.simple>

    <div class="mt-10 w-full border-t border-gray-200 pt-6 text-center dark:border-gray-700">

        {{-- Error message --}}
        @if (session('error'))
            <div class="mb-6 w-full rounded-lg bg-danger-50 p-4 text-sm text-danger-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Microsoft Login --}}
        <a
            href="{{ route('auth.microsoft') }}"
            class="fi-btn fi-btn-size-md fi-btn-color-primary mx-auto flex w-fit items-center justify-center gap-2"
        >
            <svg
                width="20"
                height="20"
                viewBox="0 0 23 23"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <path fill="#f35325" d="M0 0h11v11H0z"/>
                <path fill="#81bc06" d="M12 0h11v11H12z"/>
                <path fill="#05a6f0" d="M0 12h11v11H0z"/>
                <path fill="#ffba08" d="M12 12h11v11H12z"/>
            </svg>

            <span>
                Sign in with Microsoft
            </span>
        </a>

        {{-- Version --}}
        <!-- <div class="mt-10 w-full border-t border-gray-200 pt-6 text-center dark:border-gray-700">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Version 4.0.0
            </span>
        </div> -->

    </div>

</x-filament-panels::page.simple>