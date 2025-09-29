@forelse($data as $request)
    <tr>
        <td>{{ $request->id }}</td>
        <td>{{ number_format($request->coins) }} xu</td>
        <td>{{ number_format($request->fee) }} xu</td>
        <td>{{ number_format($request->net_amount) }} xu</td>
        <td>
            @if($request->status === 'pending')
                <span class="badge bg-warning">Chờ duyệt</span>
            @elseif($request->status === 'approved')
                <span class="badge bg-success">Đã duyệt</span>
            @elseif($request->status === 'rejected')
                <span class="badge bg-danger">Từ chối</span>
            @endif
        </td>
        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
        <td>{{ $request->processed_at ? $request->processed_at->format('d/m/Y H:i') : 'N/A' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">Chưa có yêu cầu rút tiền</td>
    </tr>
@endforelse
