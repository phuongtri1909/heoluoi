@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-0 mx-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Quản lý nạp xu</h5>
                            <p class="text-sm mb-0">
                                Quản lý các giao dịch nạp xu của người dùng
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.request.payments.index') }}" class="btn bg-gradient-primary btn-sm">
                                <i class="fas fa-clock me-2"></i> Quản lý yêu cầu thanh toán
                            </a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <form method="GET" class="d-flex gap-2">
                            <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="">- Trạng thái -</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                            </select>

                            <input type="date" name="date" class="form-control form-control-sm" style="width: auto;"
                                   value="{{ request('date') }}" onchange="this.form.submit()">

                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}" placeholder="Tìm kiếm...">
                                <button class="btn bg-gradient-primary btn-sm px-2 mb-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        ID
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">
                                        Người dùng
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Ngân hàng
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Mã giao dịch
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Số tiền
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Xu
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Trạng thái
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Người duyệt
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Ngày tạo
                                    </th>
                                    <th class="text-center text-uppercase  text-xxs font-weight-bolder ">
                                        Hành động
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->id }}</p>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $deposit->user->id) }}" class="d-flex">
                                                <div>
                                                    <img src="{{ $deposit->user->avatar ? asset('storage/' . $deposit->user->avatar) : asset('images/defaults/avatar_default.jpg') }}"
                                                         class="avatar avatar-sm me-2" alt="user image">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-xs">{{ $deposit->user->name }}</h6>
                                                    <p class="text-xs  mb-0">{{ $deposit->user->email }}</p>
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->bank->name }}</p>
                                            <p class="text-xs  mb-0">{{ $deposit->bank->account_number }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->transaction_code }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ number_format($deposit->amount, 0, ',', '.') }} VNĐ</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ number_format($deposit->coins, 0, ',', '.') }} xu</p>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger'
                                                ][$deposit->status] ?? 'secondary';

                                                $statusText = [
                                                    'pending' => 'Chờ duyệt',
                                                    'approved' => 'Đã duyệt',
                                                    'rejected' => 'Đã từ chối'
                                                ][$deposit->status] ?? 'Không xác định';
                                            @endphp

                                            <span class="badge badge-sm bg-gradient-{{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>

                                            @if($deposit->status === 'rejected' && $deposit->note)
                                                <button type="button" class="btn btn-link text-danger text-xs p-0 ms-1"
                                                        data-bs-toggle="modal" data-bs-target="#noteModal{{ $deposit->id }}">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @if($deposit->status !== 'pending' && $deposit->approver)
                                                <p class="text-xs font-weight-bold mb-0">{{ $deposit->approver->name }}</p>
                                                <p class="text-xs  mb-0">{{ $deposit->approved_at->format('d/m/Y H:i') }}</p>
                                            @else
                                                <p class="text-xs  mb-0">-</p>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $deposit->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#viewImageModal{{ $deposit->id }}"
                                                   class="btn btn-link text-info text-gradient px-3 mb-0">
                                                    <i class="far fa-eye me-2"></i>Xem ảnh
                                                </a>

                                                @if($deposit->status === 'pending')
                                                    <button type="button" class="btn btn-link text-success text-gradient px-3 mb-0"
                                                            data-bs-toggle="modal" data-bs-target="#approveModal{{ $deposit->id }}">
                                                        <i class="fas fa-check me-2"></i>Duyệt
                                                    </button>

                                                    <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0"
                                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $deposit->id }}">
                                                        <i class="fas fa-times me-2"></i>Từ chối
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">Không có giao dịch nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 pt-4">
                        <x-pagination :paginator="$deposits" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @foreach($deposits as $deposit)
        <!-- Image Modal -->
        <div class="modal fade" id="viewImageModal{{ $deposit->id }}" tabindex="-1" role="dialog" aria-labelledby="viewImageModalLabel{{ $deposit->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewImageModalLabel{{ $deposit->id }}">Ảnh chứng minh chuyển khoản</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $deposit->image) }}" class="img-fluid" alt="Chứng minh chuyển khoản">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        @if($deposit->status === 'pending')
            <div class="modal fade" id="approveModal{{ $deposit->id }}" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel{{ $deposit->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveModalLabel{{ $deposit->id }}">Xác nhận duyệt giao dịch</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="text-sm">Mã giao dịch: <span class="text-dark">{{ $deposit->transaction_code }}</span></h6>
                                    <h6 class="text-sm">Người dùng: <span class="text-dark">{{ $deposit->user->name }}</span></h6>
                                    <h6 class="text-sm">Số tiền: <span class="text-dark">{{ number_format($deposit->amount, 0, ',', '.') }} VNĐ</span></h6>
                                    <h6 class="text-sm">Số xu: <span class="text-dark">{{ number_format($deposit->coins, 0, ',', '.') }} xu</span></h6>
                                </div>
                            </div>
                            <div class="alert alert-info text-white text-sm">
                                Bạn có chắc chắn muốn duyệt giao dịch này? Người dùng sẽ được cộng {{ number_format($deposit->coins, 0, ',', '.') }} xu vào tài khoản.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Hủy</button>
                            <form action="{{ route('admin.deposits.approve', $deposit) }}" method="POST" class="m-0" id="approveForm{{ $deposit->id }}">
                                @csrf
                                <button type="submit" class="btn bg-gradient-success" id="approveBtn{{ $deposit->id }}">
                                    <span class="btn-text">Xác nhận duyệt</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reject Modal -->
        @if($deposit->status === 'pending')
            <div class="modal fade" id="rejectModal{{ $deposit->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $deposit->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel{{ $deposit->id }}">Từ chối giao dịch</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('admin.deposits.reject', $deposit) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="note{{ $deposit->id }}" class="form-control-label">Lý do từ chối</label>
                                    <textarea class="form-control" id="note{{ $deposit->id }}" name="note" rows="4" required></textarea>
                                    <small class="form-text text-muted">Thông tin này sẽ được gửi đến người dùng</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn bg-gradient-danger" id="rejectBtn{{ $deposit->id }}">
                                    <span class="btn-text">Từ chối giao dịch</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Note Modal (for viewing rejection reason) -->
        @if($deposit->status === 'rejected' && $deposit->note)
            <div class="modal fade" id="noteModal{{ $deposit->id }}" tabindex="-1" role="dialog" aria-labelledby="noteModalLabel{{ $deposit->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="noteModalLabel{{ $deposit->id }}">Lý do từ chối giao dịch</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="text-sm">Mã giao dịch: <span class="text-dark">{{ $deposit->transaction_code }}</span></h6>
                                    <h6 class="text-sm">Người dùng: <span class="text-dark">{{ $deposit->user->name }}</span></h6>
                                    <h6 class="text-sm">Số tiền: <span class="text-dark">{{ number_format($deposit->amount, 0, ',', '.') }} VNĐ</span></h6>
                                    <h6 class="text-sm">Từ chối bởi: <span class="text-dark">{{ $deposit->approver->name ?? 'Không xác định' }}</span></h6>
                                    <h6 class="text-sm">Thời gian: <span class="text-dark">{{ $deposit->approved_at->format('d/m/Y H:i') }}</span></h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-control-label mb-2">Lý do từ chối:</label>
                                    <p class="p-3 bg-light rounded">{{ $deposit->note }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@push('scripts-admin')
    <script>
        $(document).ready(function() {
            var modals = [].slice.call(document.querySelectorAll('.modal'))
            modals.map(function (modalEl) {
                return new bootstrap.Modal(modalEl)
            });

            $('[id^="approveForm"]').on('submit', function(e) {
                const formId = $(this).attr('id');
                const depositId = formId.replace('approveForm', '');
                const button = $('#approveBtn' + depositId);
                const btnText = button.find('.btn-text');
                const spinner = button.find('.spinner-border');

                button.prop('disabled', true);
                btnText.text('Đang xử lý...');
                spinner.removeClass('d-none');
            });

            $('form[action*="deposits"][action*="reject"]').on('submit', function(e) {
                const form = $(this);
                const depositId = form.find('button[type="submit"]').attr('id').replace('rejectBtn', '');
                const button = $('#rejectBtn' + depositId);
                const btnText = button.find('.btn-text');
                const spinner = button.find('.spinner-border');

                button.prop('disabled', true);
                btnText.text('Đang xử lý...');
                spinner.removeClass('d-none');
            });
        });
    </script>
@endpush
