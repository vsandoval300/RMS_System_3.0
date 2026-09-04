<div class="mt-4">
    <div class="relative mb-4">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300 dark:border-gray-700"></div>
        </div>

        <div class="relative flex justify-center text-sm">
            <span class="bg-white px-3 text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                OR
            </span>
        </div>
    </div>

    <a
        href="{{ route('auth.microsoft') }}"
        class="fi-btn fi-btn-size-md fi-btn-color-gray fi-w-full flex w-full items-center justify-center gap-2"
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
</div>