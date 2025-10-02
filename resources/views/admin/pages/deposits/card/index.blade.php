@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-0 mx-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Quản lý nạp thẻ cào</h5>
                            <p class="text-sm mb-0">Quản lý các giao dịch nạp cám bằng thẻ cào</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.paypal-deposits.index') }}" class="btn bg-gradient-info btn-sm">
                                <i class="fab fa-paypal me-2"></i>PayPal Deposits
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
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Thành công</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                            </select>

                            <select name="type" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="">- Loại thẻ -</option>
                                @foreach(\App\Models\CardDeposit::CARD_TYPES as $key => $value)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
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
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Loại thẻ</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Serial</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Mệnh giá</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Cám nhận</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Trạng thái</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cardDeposits as $deposit)
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
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->card_type_name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->serial }}</p>
                                            @if($deposit->request_id)
                                                <p class="text-xs  mb-0">ID: {{ $deposit->request_id }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->amount_formatted }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->coins_formatted }}</p>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm {{ $deposit->status_badge }}">
                                                {{ $deposit->status_text }}
                                            </span>
                                            @if($deposit->note)
                                                <p class="text-xs  mb-0 mt-1">{{ Str::limit($deposit->note, 30) }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $deposit->created_at->format('d/m/Y H:i') }}</p>
                                            @if($deposit->processed_at)
                                                <p class="text-xs  mb-0">Xử lý: {{ $deposit->processed_at->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">Không có giao dịch nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 pt-4">
                        <x-pagination :paginator="$cardDeposits" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
