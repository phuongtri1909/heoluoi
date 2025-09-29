@forelse($data as $earning)
    <tr>
        <td>{{ $earning->id }}</td>
        <td>{{ $earning->user->name }}</td>
        <td>
            <a href="{{ route('stories.show', $earning->story_id) }}">
                {{ $earning->story->title ?? 'Không xác định' }}
            </a>
        </td>
        <td class="text-success fw-bold">+{{ number_format($earning->amount_received) }} xu</td>
        <td>{{ $earning->created_at->format('d/m/Y H:i') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center">Chưa có thu nhập từ truyện</td>
    </tr>
@endforelse
