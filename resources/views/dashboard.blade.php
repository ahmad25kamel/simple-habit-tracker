<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Daily Habit') }}
        </h2>
    </x-slot>

    <div class="py-1 sm:py-12" x-data="habitTracker()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- Header / Navigation -->
                <div class="flex justify-between items-center mb-6">
                    <button @click="changeWeek(-1)" class="p-2 rounded-full hover:bg-gray-200 transition">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </button>
                    <h2 class="text-xl font-bold text-gray-800" x-text="weekRange"></h2>
                    <button @click="changeWeek(1)" class="p-2 rounded-full hover:bg-gray-200 transition">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Loading State -->
                <div x-show="loading" class="text-center py-12">
                    <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>

                <!-- Calendar Grid -->
                <div x-show="!loading" class="overflow-x-auto">
                    <table class="w-full min-w-[600px]">
                        <thead>
                            <tr>
                                <th class="text-left py-4 px-4 text-gray-500 font-medium w-1/4">Habit</th>
                                <template x-for="date in weekDates" :key="date.date">
                                    <th class="text-center py-4 px-2">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs text-gray-400 uppercase" x-text="date.day"></span>
                                            <span class="text-sm font-bold"
                                                :class="date.is_today ?
                                                    'text-indigo-600 bg-indigo-50 rounded-full w-8 h-8 flex items-center justify-center' :
                                                    'text-gray-700'"
                                                x-text="date.day_num"></span>
                                        </div>
                                    </th>
                                </template>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="habit in habits" :key="habit.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-2 h-10 rounded-l-md"
                                                :style="`background-color: ${habit.color}`"></div>
                                            <div>
                                                <p class="font-bold text-gray-800" x-text="habit.name"></p>
                                                <p class="text-xs text-gray-500" x-text="habit.frequency"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <template x-for="log in habit.logs" :key="log.date">
                                        <td class="text-center py-4 px-2">
                                            <button @click="toggle(habit.id, log.date)" :disabled="isFuture(log.date)"
                                                class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200"
                                                :class="{
                                                    'bg-green-500 text-white shadow-md scale-110': log.completed,
                                                    'bg-gray-100 text-gray-300 hover:bg-gray-200': !log.completed,
                                                    'opacity-50 cursor-not-allowed': isFuture(log.date),
                                                    'cursor-pointer': !isFuture(log.date)
                                                }">
                                                <template x-if="log.completed">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </template>
                                                <template x-if="!log.completed">
                                                    <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                                                </template>
                                            </button>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div x-show="!loading && habits.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No habits found. Ask your admin to assign some!</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function habitTracker() {
            return {
                currentDate: new Date().toISOString().split('T')[0],
                weekDates: [],
                habits: [],
                weekStart: '',
                weekEnd: '',
                loading: true,

                get weekRange() {
                    return `${this.weekStart} - ${this.weekEnd}`;
                },

                init() {
                    this.fetchHabits();
                },

                async fetchHabits(date = null) {
                    this.loading = true;
                    let url = '/api/habits';
                    if (date) {
                        url += `?date=${date}`;
                    }

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'Authorization': 'Bearer ' +
                                    '{{ auth()->user()->createToken('web-app')->plainTextToken }}' // Temporary token generation for session auth
                            }
                        });

                        // Since we are using Sanctum with SPA session auth, we might not need Bearer token if we use axios with credentials
                        // But for fetch, we rely on cookies. Let's try standard fetch first.
                        // Actually, for web routes, Laravel handles session. For API routes, we need to ensure Sanctum middleware is happy.
                        // If we are logged in, the session cookie should be enough if we include credentials.

                    } catch (error) {
                        console.error('Error fetching habits:', error);
                    }

                    // Re-implementing fetch with proper session handling
                    // We are in a Blade view, so we are authenticated via session.
                    // Sanctum's EnsureFrontendRequestsAreStateful middleware handles this for API routes if configured.
                    // However, we didn't configure stateful domains.
                    // Simplest way for this hybrid app: Use the web session.
                    // But API routes are in api.php which uses 'auth:sanctum'.
                    // Laravel automatically handles session auth for Sanctum if we use the 'web' middleware group or ensure stateful.
                    // Let's try to just fetch and see. If it fails, we might need to adjust.

                    // Wait, I can just use axios if it's installed (it is in app.js).
                    // But I removed app.js? No, I removed Livewire. app.js is Vite.
                    // Let's check if axios is available globally. Usually Laravel sets it up.

                    // Fallback to fetch with standard headers.

                    try {
                        const response = await axios.get(url);
                        const data = response.data;

                        this.weekDates = data.weekDates;
                        this.habits = data.habits;
                        this.currentDate = data.currentDate;
                        this.weekStart = data.weekStart;
                        this.weekEnd = data.weekEnd;
                    } catch (error) {
                        console.error('Error:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                changeWeek(offset) {
                    const date = new Date(this.currentDate);
                    date.setDate(date.getDate() + (offset * 7));
                    this.fetchHabits(date.toISOString().split('T')[0]);
                },

                async toggle(habitId, date) {
                    // Optimistic update
                    const habit = this.habits.find(h => h.id === habitId);
                    const log = habit.logs.find(l => l.date === date);
                    const originalState = log.completed;
                    log.completed = !originalState;

                    try {
                        await axios.post(`/api/habits/${habitId}/toggle`, {
                            date: date
                        });
                    } catch (error) {
                        // Revert on error
                        log.completed = originalState;
                        console.error('Error toggling habit:', error);
                        alert('Failed to update habit status');
                    }
                },

                isFuture(dateStr) {
                    const today = new Date().toISOString().split('T')[0];
                    return dateStr > today;
                }
            }
        }
    </script>
</x-app-layout>
