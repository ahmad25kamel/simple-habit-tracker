<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;

class HabitLogController extends Controller
{
    public function toggle(Habit $habit)
    {
        if ($habit->user_id !== auth()->id()) {
            abort(403);
        }

        $today = now()->format('Y-m-d');
        $log = HabitLog::where('habit_id', $habit->id)
            ->where('date', $today)
            ->first();

        if ($log) {
            $log->delete();
        } else {
            HabitLog::create([
                'habit_id' => $habit->id,
                'user_id' => auth()->id(),
                'date' => $today,
                'status' => 'completed'
            ]);
        }

        return back();
    }
}
