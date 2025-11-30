<?php

namespace App\Livewire;

use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Livewire\Component;

class HabitTracker extends Component
{
    public $currentDate;
    public $weekDates = [];

    public function mount()
    {
        $this->currentDate = Carbon::today();
        $this->setWeekDates();
    }

    public function setWeekDates()
    {
        $startOfWeek = $this->currentDate->copy()->startOfWeek();
        $this->weekDates = [];
        for ($i = 0; $i < 7; $i++) {
            $this->weekDates[] = $startOfWeek->copy()->addDays($i);
        }
    }

    public function previousWeek()
    {
        $this->currentDate->subWeek();
        $this->setWeekDates();
    }

    public function nextWeek()
    {
        $this->currentDate->addWeek();
        $this->setWeekDates();
    }

    public function toggle($habitId, $dateStr)
    {
        $habit = Habit::find($habitId);

        // Security check
        if ($habit->user_id !== auth()->id()) {
            return;
        }

        $log = HabitLog::where('habit_id', $habitId)
            ->where('date', $dateStr)
            ->first();

        if ($log) {
            $log->delete();
        } else {
            HabitLog::create([
                'habit_id' => $habitId,
                'user_id' => auth()->id(),
                'date' => $dateStr,
                'status' => 'completed'
            ]);
        }
    }

    public function render()
    {
        $habits = auth()->user()->habits()->with(['logs' => function ($q) {
            $start = $this->weekDates[0]->format('Y-m-d');
            $end = $this->weekDates[6]->format('Y-m-d');
            $q->whereBetween('date', [$start, $end]);
        }])->get();

        return view('livewire.habit-tracker', [
            'habits' => $habits
        ]);
    }
}
