<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HabitApiController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        $weekDates = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $startOfWeek->copy()->addDays($i);
            $weekDates[] = [
                'date' => $d->format('Y-m-d'),
                'day' => $d->format('D'),
                'day_num' => $d->format('d'),
                'is_today' => $d->isToday(),
                'is_future' => $d->isFuture(),
            ];
        }

        $userId = $request->input('user_id');
        $user = auth()->user();

        if ($userId && $user->is_admin) {
            $targetUser = \App\Models\User::findOrFail($userId);
        } else {
            $targetUser = $user;
        }

        $habits = $targetUser->habits()->with(['logs' => function ($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->endOfDay()->format('Y-m-d H:i:s')]);
        }])->get();

        // Transform habits to include completion status for each date
        $transformedHabits = $habits->map(function ($habit) use ($weekDates) {
            $logs = $habit->logs->keyBy(function ($log) {
                return $log->date->format('Y-m-d');
            });

            return [
                'id' => $habit->id,
                'name' => $habit->name,
                'frequency' => $habit->frequency,
                'color' => $habit->color,
                'logs' => collect($weekDates)->map(function ($date) use ($logs) {
                    return [
                        'date' => $date['date'],
                        'completed' => $logs->has($date['date']),
                        'is_future' => $date['is_future'],
                    ];
                })
            ];
        });

        return response()->json([
            'weekDates' => $weekDates,
            'habits' => $transformedHabits,
            'currentDate' => $date->format('Y-m-d'),
            'weekStart' => $startOfWeek->format('M d'),
            'weekEnd' => $endOfWeek->format('M d, Y'),
        ]);
    }

    public function toggle(Habit $habit, Request $request)
    {
        if ($habit->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $dateStr = $request->input('date');

        $log = HabitLog::where('habit_id', $habit->id)
            ->whereDate('date', $dateStr)
            ->first();

        if ($log) {
            $log->delete();
            $status = 'deleted';
        } else {
            HabitLog::create([
                'habit_id' => $habit->id,
                'user_id' => auth()->id(),
                'date' => $dateStr,
                'status' => 'completed'
            ]);
            $status = 'created';
        }

        return response()->json(['status' => $status]);
    }
}
