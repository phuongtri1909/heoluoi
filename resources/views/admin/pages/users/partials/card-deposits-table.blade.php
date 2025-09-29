@forelse($data as $deposit)
    <tr>
        <td>{{ $deposit->id }}</td>
        <td>{{ $deposit->card_type_name }}</td>
        <td>{{ $deposit->serial }}</td>
        <td>{{ $deposit->amount_formatted }}</td>
        <td>{{ $deposit->coins_formatted }}</td>
        <td>
            <span class="badge {{ $deposit->status_badge }}">{{ $deposit->status_text }}</span>
        </td>
        <td>{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">Chưa có giao dịch nạp thẻ</td>
    </tr>
@endforelse
