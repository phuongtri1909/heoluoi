@forelse($data as $purchase)
    <tr>
        <td>{{ $purchase->id }}</td>
        <td>
            <a href="{{ route('stories.show', $purchase->chapter->story_id) }}">
                {{ $purchase->chapter->story->title ?? 'Không xác định' }}
            </a>
        </td>
        <td>Chương {{ $purchase->chapter->number }}: {{ Str::limit($purchase->chapter->title, 30) }}</td>
        <td>{{ number_format($purchase->amount_paid) }}</td>
        <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center">Chưa có giao dịch mua chương</td>
    </tr>
@endforelse 