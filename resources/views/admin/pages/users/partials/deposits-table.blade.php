@forelse($data as $deposit)
    <tr>
        <td>{{ $deposit->id }}</td>
        <td>{{ $deposit->bank->name ?? 'N/A' }}</td>
        <td>{{ $deposit->transaction_code }}</td>
        <td>{{ number_format($deposit->amount) }}đ</td>
        <td>{{ number_format($deposit->coins) }}</td>
        <td>
            @if($deposit->status === 'approved')
                <span class="badge bg-success">Đã duyệt</span>
            @elseif($deposit->status === 'rejected')
                <span class="badge bg-danger">Từ chối</span>
            @else
                <span class="badge bg-warning">Chờ duyệt</span>
            @endif
        </td>
        <td>{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
        <td>{{ $deposit->approved_at ? $deposit->approved_at->format('d/m/Y H:i') : 'N/A' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center">Chưa có giao dịch nạp xu</td>
    </tr>
@endforelse 