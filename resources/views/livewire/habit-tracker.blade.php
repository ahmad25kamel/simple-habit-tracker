<div class="p-6">
    <!-- Header / Navigation -->
    <div class="flex justify-between items-center mb-6">
        <button wire:click="previousWeek" class="p-2 rounded-full hover:bg-gray-200 transition">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <h2 class="text-xl font-bold text-gray-800">
            {{ $weekDates[0]->format('M d') }} - {{ $weekDates[6]->format('M d, Y') }}
        </h2>
        <button wire:click="nextWeek" class="p-2 rounded-full hover:bg-gray-200 transition">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>

    <!-- Calendar Grid -->
    <div class="overflow-x-auto">
        <table class="w-full min-w-[600px]">
            <thead>
                <tr>
                    <th class="text-left py-4 px-4 text-gray-500 font-medium w-1/4">Habit</th>
                    @foreach ($weekDates as $date)
                        <th class="text-center py-4 px-2">
                            <div class="flex flex-col items-center">
                                <span class="text-xs text-gray-400 uppercase">{{ $date->format('D') }}</span>
                                <span
                                    class="text-sm font-bold {{ $date->isToday() ? 'text-indigo-600 bg-indigo-50 rounded-full w-8 h-8 flex items-center justify-center' : 'text-gray-700' }}">
                                    {{ $date->format('d') }}
                                </span>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($habits as $habit)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-10 rounded-l-md" style="background-color: {{ $habit->color }}"></div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $habit->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $habit->frequency }}</p>
                                </div>
                            </div>
                        </td>
                        @foreach ($weekDates as $date)
                            @php
                                $dateStr = $date->format('Y-m-d');
                                $isCompleted = $habit->logs->where('date', $date)->isNotEmpty();
                                $isFuture = $date->isFuture();
                            @endphp
                            <td class="text-center py-4 px-2">
                                <button wire:click="toggle({{ $habit->id }}, '{{ $dateStr }}')"
                                    @if ($isFuture) disabled @endif
                                    class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200 
                                    {{ $isCompleted ? 'bg-green-500 text-white shadow-md scale-110' : 'bg-gray-100 text-gray-300 hover:bg-gray-200' }}
                                    {{ $isFuture ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                                    @if ($isCompleted)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                                    @endif
                                </button>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($habits->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500">No habits found. Ask your admin to assign some!</p>
        </div>
    @endif
</div>
