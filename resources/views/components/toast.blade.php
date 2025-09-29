{{-- SweetAlert2 for session flash messages --}}

{{-- Make sure SweetAlert2 is loaded --}}
@push('scripts')
    @if(!isset($loadedSweetAlert))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endif
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Configure SweetAlert2 Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        
        // Show success message if present
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif
        
        // Show error message if present
        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif
        
        // Show warning message if present
        @if(session('warning'))
            Toast.fire({
                icon: 'warning',
                title: '{{ session('warning') }}'
            });
        @endif
        
        // Show info message if present
        @if(session('info'))
            Toast.fire({
                icon: 'info',
                title: '{{ session('info') }}'
            });
        @endif
    });
</script>