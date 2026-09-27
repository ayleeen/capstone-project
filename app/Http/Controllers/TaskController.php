<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $orderBy = $request->query('order_by', 'priority');
        $allowedColumns = ['priority', 'status', 'due_date'];
        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'priority';
        }

        $tasks = Task::where('user_id', auth()->id())
            ->orderBy($orderBy)
            ->get();

        $total = $tasks->count();
        $done  = $tasks->where('status', 'completed')->count();
        $task  = $total - $done;

        return view('todo', [
            'tasks'    => $tasks,
            'order_by' => $orderBy,
            'total'    => $total,
            'task'     => $task,
            'done'     => $done,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'required|date',
            'priority'    => 'required|in:low,medium,high',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'todo';

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task added!');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'required|date',
            'priority'    => 'required|in:low,medium,high',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted!');
    }

    public function complete(Task $task)
    {
        $task->update(['status' => 'completed']);
        return redirect()->route('tasks.index');
    }

    public function start(Task $task)
    {
        $task->update(['status' => 'in-progress']);
        return redirect()->route('tasks.index');
    }

    public function monthData(Request $request)
    {
        $year  = $request->query('year');
        $month = (int) $request->query('month') + 1;

        $tasks = Task::where('user_id', auth()->id())
            ->whereYear('due_date', $year)
            ->whereMonth('due_date', $month)
            ->get(['due_date', 'status']);

        $grouped = $tasks->groupBy(fn ($t) => $t->due_date->format('Y-m-d'))
            ->map(fn ($group) => $group->pluck('status')->unique()->values());

        return response()->json($grouped);
    }
}