@forelse($data as $deposit)
    <tr>
        <td>{{ $deposit->id }}</td>
        <td>{{ $deposit->transaction_code }}</td>
        <td>{{ $deposit->usd_amount_formatted }}</td>
        <td>{{ $deposit->coins_formatted }}</td>
        <td>
            <span class="badge {{ $deposit->status_badge }}">{{ $deposit->status_text }}</span>
        </td>
        <td>{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
        <td>{{ $deposit->processed_at ? $deposit->processed_at->format('d/m/Y H:i') : 'N/A' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">Chưa có giao dịch nạp PayPal</td>
    </tr>
@endforelse
