<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Truyện</th>
                <th>Loại</th>
                <th>Giá đã trả</th>
                <th>Thời gian đề cử</th>
                <th>Hết hạn</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $featured)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($featured->story->cover)
                                <img src="{{ asset('storage/' . $featured->story->cover) }}" 
                                     alt="{{ $featured->story->title }}" 
                                     class="rounded me-2" 
                                     style="width: 40px; height: 60px; object-fit: cover;">
                            @endif
                            <div>
                                <h6 class="mb-0">{{ $featured->story->title }}</h6>
                                <small class="text-muted">{{ $featured->story->slug }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-info">{{ ucfirst($featured->type) }}</span>
                    </td>
                    <td>
                        <span class="text-success fw-bold">{{ number_format($featured->price_paid) }} xu</span>
                    </td>
                    <td>
                        <small>{{ $featured->featured_at->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                        <small>{{ $featured->featured_until->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                        @if($featured->is_active && $featured->featured_until->isFuture())
                            <span class="badge bg-success">Đang hoạt động</span>
                        @elseif($featured->is_active && $featured->featured_until->isPast())
                            <span class="badge bg-warning">Hết hạn</span>
                        @else
                            <span class="badge bg-secondary">Đã hủy</span>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">{{ $featured->note ?? '-' }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-star fa-2x mb-2"></i>
                        <br>
                        Chưa có đề cử truyện nào
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
