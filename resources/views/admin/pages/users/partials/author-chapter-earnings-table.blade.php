@forelse($data as $earning)
    <tr>
        <td>{{ $earning->id }}</td>
        <td>{{ $earning->user->name }}</td>
        <td>
            <a href="{{ route('stories.show', $earning->chapter->story_id) }}">
                {{ $earning->chapter->story->title ?? 'Không xác định' }}
            </a>
        </td>
        <td>Chương {{ $earning->chapter->number }}: {{ Str::limit($earning->chapter->title, 30) }}</td>
        <td class="text-success fw-bold">+{{ number_format($earning->amount_received) }} xu</td>
        <td>{{ $earning->created_at->format('d/m/Y H:i') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center">Chưa có thu nhập từ chương</td>
    </tr>
@endforelse
