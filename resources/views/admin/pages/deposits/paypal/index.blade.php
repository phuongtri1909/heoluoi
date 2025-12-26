@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-0 mx-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Quản lý nạp PayPal</h5>
                            <p class="text-sm mb-0">Quản lý các giao dịch nạp cám bằng PayPal</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.request-payment-paypal.index') }}" class="btn bg-gradient-warning btn-sm">
                                <i class="fas fa-clock me-2"></i>Yêu cầu thanh toán
                            </a>
                            <a href="{{ route('admin.card-deposits.index') }}" class="btn bg-gradient-info btn-sm">
                                <i class="fas fa-credit-card me-2"></i>Card Deposits
                            </a>
                            <a href="{{ route('admin.deposits.index') }}" class="btn bg-gradient-primary btn-sm">
                                <i class="fas fa-university me-2"></i>Bank Deposits
                            </a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <form method="GET" class="d-flex gap-2">
                            <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="">- Trạng thái -</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
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
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">ID</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">Người dùng</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Mã GD</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Email PayPal</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Số tiền</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Cám</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Trạng thái</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Thời gian</th>
                                    <th class="text-center text-uppercase  text-xxs font-weight-bolder ">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paypalDeposits as $deposit)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->id }}</p>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $deposit->user->id) }}" class="d-flex">
                                                <div>
                                                    <img src="{{ $deposit->user->avatar ? asset('storage/' . $deposit->user->avatar) : asset('/images/defaults/avatar_default.jpg') }}"
                                                         class="avatar avatar-sm me-2" alt="user image">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-xs">{{ $deposit->user->name }}</h6>
                                                    <p class="text-xs  mb-0">{{ $deposit->user->email }}</p>
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->transaction_code }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->paypal_email }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->usd_amount_formatted }}</p>
                                            <p class="text-xs  mb-0">{{ $deposit->vnd_amount_formatted }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->coins_formatted }} cám</p>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm {{ $deposit->status_badge }}">
                                                {{ $deposit->status_text }}
                                            </span>
                                            @if($deposit->note)
                                                <button type="button" class="btn btn-link text-danger text-xs p-0 ms-1"
                                                        data-bs-toggle="modal" data-bs-target="#noteModal{{ $deposit->id }}">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->created_at->format('d/m/Y H:i') }}</p>
                                            @if($deposit->processed_at)
                                                <p class="text-xs  mb-0">{{ $deposit->processed_at->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center gap-2">
                                                @if($deposit->image)
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#viewImageModal{{ $deposit->id }}"
                                                       class="btn btn-link text-info text-gradient px-3 mb-0">
                                                        <i class="far fa-eye me-2"></i>Xem ảnh
                                                    </a>
                                                @endif

                                                @if($deposit->status === 'processing')
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
                                        <td colspan="9" class="text-center py-4">Không có giao dịch PayPal nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 pt-4">
                        <x-pagination :paginator="$paypalDeposits" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @foreach($paypalDeposits as $deposit)
        <!-- Image Modal -->
        @if($deposit->image)
            <div class="modal fade" id="viewImageModal{{ $deposit->id }}" tabindex="-1" role="dialog" aria-labelledby="viewImageModalLabel{{ $deposit->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewImageModalLabel{{ $deposit->id }}">Ảnh chứng minh PayPal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ Storage::url($deposit->image) }}" class="img-fluid" alt="Chứng minh PayPal">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Approve Modal -->
        @if($deposit->status === 'processing')
            <div class="modal fade" id="approveModal{{ $deposit->id }}" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel{{ $deposit->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveModalLabel{{ $deposit->id }}">Xác nhận duyệt giao dịch PayPal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="text-sm">Mã giao dịch: <span class="text-dark">{{ $deposit->transaction_code }}</span></h6>
                                    <h6 class="text-sm">Người dùng: <span class="text-dark">{{ $deposit->user->name }}</span></h6>
                                    <h6 class="text-sm">Số tiền: <span class="text-dark">{{ $deposit->usd_amount_formatted }}</span></h6>
                                    <h6 class="text-sm">Số cám: <span class="text-dark">{{ $deposit->coins_formatted }} cám</span></h6>
                                    <h6 class="text-sm">Email PayPal: <span class="text-dark">{{ $deposit->paypal_email }}</span></h6>
                                </div>
                            </div>
                            <div class="alert alert-info text-white text-sm">
                                Bạn có chắc chắn muốn duyệt giao dịch này? Người dùng sẽ được cộng {{ $deposit->coins_formatted }} cám vào tài khoản.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Hủy</button>
                            <form action="{{ route('admin.paypal-deposits.approve', $deposit) }}" method="POST" class="m-0" id="approvePaypalForm{{ $deposit->id }}">
                                @csrf
                                <button type="submit" class="btn bg-gradient-success" id="approvePaypalBtn{{ $deposit->id }}">
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
        @if($deposit->status === 'processing')
            <div class="modal fade" id="rejectModal{{ $deposit->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $deposit->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel{{ $deposit->id }}">Từ chối giao dịch PayPal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('admin.paypal-deposits.reject', $deposit) }}" method="POST">
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
                                <button type="submit" class="btn bg-gradient-danger" id="rejectPaypalBtn{{ $deposit->id }}">
                                    <span class="btn-text">Từ chối giao dịch</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Note Modal -->
        @if($deposit->note)
            <div class="modal fade" id="noteModal{{ $deposit->id }}" tabindex="-1" role="dialog" aria-labelledby="noteModalLabel{{ $deposit->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="noteModalLabel{{ $deposit->id }}">Ghi chú giao dịch</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="text-sm">Mã giao dịch: <span class="text-dark">{{ $deposit->transaction_code }}</span></h6>
                                    <h6 class="text-sm">Người dùng: <span class="text-dark">{{ $deposit->user->name }}</span></h6>
                                    <h6 class="text-sm">Trạng thái: <span class="text-dark">{{ $deposit->status_text }}</span></h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-control-label mb-2">Ghi chú:</label>
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
            $('[id^="approvePaypalForm"]').on('submit', function(e) {
                const formId = $(this).attr('id');
                const depositId = formId.replace('approvePaypalForm', '');
                const button = $('#approvePaypalBtn' + depositId);
                const btnText = button.find('.btn-text');
                const spinner = button.find('.spinner-border');

                // Disable button and show spinner
                button.prop('disabled', true);
                btnText.text('Đang xử lý...');
                spinner.removeClass('d-none');
            });

            $('form[action*="paypal-deposits"][action*="reject"]').on('submit', function(e) {
                const form = $(this);
                const depositId = form.find('button[type="submit"]').attr('id').replace('rejectPaypalBtn', '');
                const button = $('#rejectPaypalBtn' + depositId);
                const btnText = button.find('.btn-text');
                const spinner = button.find('.spinner-border');

                button.prop('disabled', true);
                btnText.text('Đang xử lý...');
                spinner.removeClass('d-none');
            });
        });
    </script>
@endpush
