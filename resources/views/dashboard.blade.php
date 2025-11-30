<x-app-layout>
    <div class="min-h-screen bg-gray-50 pb-24" x-data="habitTracker()">
        <!-- Header -->
        <div class="bg-white shadow-sm sticky top-0 z-10">
            <div class="max-w-md mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-900">My Habits</h1>
                <div class="flex items-center space-x-2 bg-gray-100 rounded-full p-1">
                    <button @click="changeWeek(-1)"
                        class="p-1.5 rounded-full hover:bg-white hover:shadow-sm transition text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </button>
                    <span class="text-xs font-semibold text-gray-600 px-2" x-text="weekRange"></span>
                    <button @click="changeWeek(1)"
                        class="p-1.5 rounded-full hover:bg-white hover:shadow-sm transition text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Date Scroller -->
            <div class="max-w-md mx-auto pb-2">
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="date in weekDates" :key="date.date">
                        <div class="flex flex-col items-center p-1">
                            <span class="text-[10px] font-medium text-gray-400 uppercase mb-1" x-text="date.day"></span>
                            <div class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold transition-all duration-300"
                                :class="date.is_today ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-700 bg-transparent'">
                                <span x-text="date.day_num"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Habits List -->
        <div class="max-w-md mx-auto py-2 space-y-2">
            <!-- Loading State -->
            <div x-show="loading" class="flex justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>

            <template x-for="habit in habits" :key="habit.id">
                <div class="bg-white shadow-sm border-b border-gray-100 overflow-hidden">
                    <div class="px-4 pt-4 pb-2 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-1.5 h-6 rounded-full" :style="`background-color: ${habit.color}`"></div>
                            <div>
                                <h3 class="font-bold text-gray-900 leading-tight" x-text="habit.name"></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Progress Dots -->
                    <div class="grid grid-cols-7 gap-1 bg-white p-1">
                        <template x-for="log in habit.logs" :key="log.date">
                            <div class="flex items-center justify-center aspect-square">
                                <button @click="toggle(habit.id, log.date)" :disabled="isFuture(log.date)"
                                    class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200"
                                    :class="{
                                        'bg-green-500 text-white shadow-md scale-110': log.completed,
                                        'bg-gray-100 text-gray-300 hover:bg-gray-200': !log.completed && !isFuture(log
                                            .date),
                                        'opacity-50 cursor-not-allowed': isFuture(log.date)
                                    }">
                                    <svg x-show="log.completed" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <div x-show="!log.completed" class="w-2 h-2 rounded-full bg-gray-300"></div>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div x-show="!loading && habits.length === 0" class="text-center py-12">
                <div class="bg-indigo-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No habits yet</h3>
                <p class="text-gray-500 text-sm mt-1">Ask your admin to assign some habits to get started!</p>
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
                    // Shorten the range display
                    return `${this.weekStart.split(' ')[0]} ${this.weekStart.split(' ')[1]} - ${this.weekEnd.split(' ')[0]} ${this.weekEnd.split(' ')[1]}`;
                },

                init() {
                    this.fetchHabits();
                },

                formatDate(dateStr) {
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('en-US', {
                        weekday: 'short',
                        day: 'numeric'
                    });
                },

                async fetchHabits(date = null) {
                    this.loading = true;
                    let url = '/api/habits';
                    if (date) {
                        url += `?date=${date}`;
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
