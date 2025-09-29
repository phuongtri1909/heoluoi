@forelse($data as $purchase)
    <tr>
        <td>{{ $purchase->id }}</td>
        <td>
            <a href="{{ route('stories.show', $purchase->story_id) }}">
                {{ $purchase->story->title ?? 'Không xác định' }}
            </a>
        </td>
        <td>{{ number_format($purchase->amount_paid) }}</td>
        <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center">Chưa có giao dịch mua truyện</td>
    </tr>
@endforelse 