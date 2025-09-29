@forelse($data as $bookmark)
    <tr>
        <td>{{ $bookmark->id }}</td>
        <td>
            <a href="{{ route('stories.show', $bookmark->story_id) }}">
                {{ $bookmark->story->title ?? 'Không xác định' }}
            </a>
        </td>
        <td>
            @if($bookmark->lastChapter)
                Chương {{ $bookmark->lastChapter->number }}: {{ Str::limit($bookmark->lastChapter->title, 30) }}
            @else
                Chưa đọc
            @endif
        </td>
        <td>
            @if($bookmark->notification_enabled)
                <span class="badge bg-success">Bật</span>
            @else
                <span class="badge bg-secondary">Tắt</span>
            @endif
        </td>
        <td>{{ $bookmark->created_at->format('d/m/Y H:i') }}</td>
        <td>{{ $bookmark->last_read_at ? $bookmark->last_read_at->format('d/m/Y H:i') : 'Chưa đọc' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center">Chưa có truyện nào được theo dõi</td>
    </tr>
@endforelse 