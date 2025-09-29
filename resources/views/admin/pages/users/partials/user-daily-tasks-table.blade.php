@forelse($data as $task)
    <tr>
        <td>{{ $task->id }}</td>
        <td>{{ $task->dailyTask->name ?? 'N/A' }}</td>
        <td>{{ $task->task_date->format('d/m/Y') }}</td>
        <td>{{ $task->completed_count }}</td>
        <td>{{ $task->last_completed_at ? $task->last_completed_at->format('d/m/Y H:i') : 'N/A' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center">Chưa có nhiệm vụ nào</td>
    </tr>
@endforelse
