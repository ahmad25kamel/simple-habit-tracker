<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard - Manage Habits') }}
        </h2>
    </x-slot>

    <div class="py-1 sm:py-12" x-data="{ selectedUser: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- User Monitoring Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900">Monitor User Progress</h3>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select User to View</label>
                    <select x-model="selectedUser"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm md:w-1/3">
                        <option value="">-- Select a User --</option>
                        @foreach ($users as $user)
                            @if (!$user->is_admin)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div x-show="selectedUser" x-data="adminHabitTracker()"
                    x-effect="if(selectedUser) fetchHabits(null, selectedUser)">
                    <!-- Calendar View (Read Only) -->
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="flex justify-between items-center mb-4">
                            <button @click="changeWeek(-1)" class="p-1 rounded-full hover:bg-gray-200">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <h4 class="font-bold text-gray-700" x-text="weekRange"></h4>
                            <button @click="changeWeek(1)" class="p-1 rounded-full hover:bg-gray-200">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>

                        <div x-show="loading" class="text-center py-8">
                            <svg class="animate-spin h-6 w-6 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>

                        <div x-show="!loading" class="overflow-x-auto">
                            <table class="w-full min-w-[500px]">
                                <thead>
                                    <tr>
                                        <th class="text-left text-xs font-medium text-gray-500 uppercase w-1/4">Habit
                                        </th>
                                        <template x-for="date in weekDates" :key="date.date">
                                            <th class="text-center p-1">
                                                <div class="flex flex-col items-center">
                                                    <span class="text-[10px] text-gray-400 uppercase"
                                                        x-text="date.day"></span>
                                                    <span class="text-xs font-bold"
                                                        :class="date.is_today ? 'text-indigo-600' : 'text-gray-700'"
                                                        x-text="date.day_num"></span>
                                                </div>
                                            </th>
                                        </template>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-for="habit in habits" :key="habit.id">
                                        <tr>
                                            <td class="py-2 text-sm font-medium text-gray-900" x-text="habit.name"></td>
                                            <template x-for="log in habit.logs" :key="log.date">
                                                <td class="text-center py-2">
                                                    <div class="w-6 h-6 rounded-full mx-auto flex items-center justify-center"
                                                        :class="log.completed ? 'bg-green-500 text-white' : 'bg-gray-100'">
                                                        <svg x-show="log.completed" class="w-4 h-4" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                </td>
                                            </template>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <div x-show="habits.length === 0" class="text-center py-4 text-gray-500 text-sm">No habits
                                found for this user.</div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach ($users as $user)
                @if (!$user->is_admin)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-bold mb-4 flex items-center">
                                <span class="bg-gray-200 rounded-full p-2 mr-2">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                </span>
                                {{ $user->name }} ({{ $user->email }})
                            </h3>

                            <!-- Add Habit Form -->
                            <form action="{{ route('habits.store') }}" method="POST"
                                class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Habit Name</label>
                                        <input type="text" name="name" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Description</label>
                                        <input type="text" name="description"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Frequency</label>
                                        <select name="frequency"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                        </select>
                                    </div>
                                    <button type="submit"
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium">
                                        Assign Habit
                                    </button>
                                </div>
                            </form>

                            <!-- List Habits -->
                            @if ($user->habits->isEmpty())
                                <p class="text-gray-500 italic text-sm">No habits assigned.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Name</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Frequency</th>
                                                <th
                                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($user->habits as $habit)
                                                <tr>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $habit->name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ ucfirst($habit->frequency) }}</td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <form action="{{ route('habits.destroy', $habit) }}"
                                                            method="POST" class="inline-block"
                                                            onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="text-red-600 hover:text-red-900">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <script>
        function adminHabitTracker() {
            return {
                currentDate: new Date().toISOString().split('T')[0],
                weekDates: [],
                habits: [],
                weekStart: '',
                weekEnd: '',
                loading: false,
                currentUser: null,

                get weekRange() {
                    return `${this.weekStart} - ${this.weekEnd}`;
                },

                async fetchHabits(date = null, userId = null) {
                    if (!userId) return;

                    this.loading = true;
                    this.currentUser = userId;
                    let url = `/api/habits?user_id=${userId}`;
                    if (date) {
                        url += `&date=${date}`;
                    } else {
                        // Reset date if switching user without date
                        this.currentDate = new Date().toISOString().split('T')[0];
                    }

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
                    this.fetchHabits(date.toISOString().split('T')[0], this.currentUser);
                }
            }
        }
    </script>
</x-app-layout>
