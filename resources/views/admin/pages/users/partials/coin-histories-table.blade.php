@forelse($data as $history)
    <tr>
        <td>
            <div class="d-flex flex-column">
                <span>{{ $history->created_at->format('d/m/Y') }}</span>
                <small class="text-muted">{{ $history->created_at->format('H:i:s') }}</small>
            </div>
        </td>
        <td>
            <span class="badge bg-{{ $history->type == 'add' ? 'success' : 'danger' }}">
                {{ $history->transaction_type_label }}
            </span>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span>{{ $history->description }}</span>
                @if($history->reference)
                    <small class="text-muted">
                        Tham chiếu: {{ class_basename($history->reference_type) }} #{{ $history->reference_id }}
                    </small>
                @endif
            </div>
        </td>
        <td>
            <span class="fw-bold text-{{ $history->type == 'add' ? 'success' : 'danger' }}">
                {{ $history->formatted_amount }} xu
            </span>
        </td>
        <td>
            <span class="fw-bold">{{ number_format($history->balance_before) }} xu</span>
        </td>
        <td>
            <span class="fw-bold">{{ number_format($history->balance_after) }} xu</span>
        </td>
        <td>
            <small class="text-muted">{{ $history->ip_address }}</small>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-4">
            <div class="text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>Chưa có lịch sử xu nào</p>
            </div>
        </td>
    </tr>
@endforelse
