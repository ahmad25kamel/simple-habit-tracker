<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        $users = User::with('habits')->get();
        return view('admin.dashboard', compact('users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly',
            'color' => 'nullable|string',
        ]);

        Habit::create($validated);

        return back()->with('success', 'Habit assigned successfully.');
    }

    public function destroy(Habit $habit)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        $habit->delete();
        return back()->with('success', 'Habit removed.');
    }
}
