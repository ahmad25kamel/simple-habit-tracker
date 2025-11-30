<div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 block sm:hidden z-50 pb-safe">
    <div class="flex justify-around items-center h-16">
        <!-- Dashboard / Home -->
        <a href="{{ route('dashboard') }}"
            class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            <span class="text-xs font-medium">Home</span>
        </a>

        @if (auth()->user()->is_admin)
            <!-- Users (Admin Only) -->
            <a href="{{ route('users.index') }}"
                class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('users.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <span class="text-xs font-medium">Users</span>
            </a>
        @endif

        <!-- Profile -->
        <a href="{{ route('profile.edit') }}"
            class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('profile.edit') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-xs font-medium">Profile</span>
        </a>

        <!-- Logout (Optional, maybe in Profile?) -->
        <form method="POST" action="{{ route('logout') }}" class="w-full h-full">
            @csrf
            <button type="submit"
                class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-red-600">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                <span class="text-xs font-medium">Logout</span>
            </button>
        </form>
    </div>
</div>
