@extends('layouts.minimal')

@section('title', 'Chuyển hướng')

@section('content')
<div class="redirect-container text-center" style="padding: 50px 20px;">
    <a id="redirect-link" href="{{ $affiliateLink }}" target="_blank" class="d-none"></a>
</div>
@endsection

@push('scripts')
<script>
    window.onload = function() {
        var link = document.getElementById('redirect-link');
        var event = new MouseEvent('click', { bubbles: true, cancelable: true, view: window });
        var opened = link.dispatchEvent(event);
        window.history.back();
    };
</script>
@endpush
