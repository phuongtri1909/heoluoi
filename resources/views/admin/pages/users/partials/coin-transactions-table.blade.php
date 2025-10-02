@forelse($data as $transaction)
    <tr>
        <td>{{ $transaction->id }}</td>
        <td>
            @if($transaction->type === 'add')
                <span class="badge bg-success">Cộng cám</span>
            @else
                <span class="badge bg-danger">Trừ cám</span>
            @endif
        </td>
        <td>{{ number_format($transaction->amount) }}</td>
        <td>{{ $transaction->admin->name ?? 'N/A' }}</td>
        <td>{{ $transaction->note ?? 'Không có ghi chú' }}</td>
        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center">Chưa có giao dịch cám nào</td>
    </tr>
@endforelse 