{{-- Load SweetAlert2 library globally --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Global SweetAlert2 configurations
        window.showToast = function(message, type = 'success') {
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
            
            Toast.fire({
                icon: type,
                title: message
            });
        };
        
        window.showAlert = function(title, message, type = 'success') {
            return Swal.fire({
                icon: type,
                title: title,
                text: message,
                confirmButtonColor: 'var(--primary-color-3)'
            });
        };
        
        window.showConfirm = function(title, message, confirmCallback, cancelCallback = null, type = 'warning') {
            Swal.fire({
                icon: type,
                title: title,
                text: message,
                showCancelButton: true,
                confirmButtonColor: 'var(--primary-color-3)',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof confirmCallback === 'function') {
                        confirmCallback();
                    }
                } else if (cancelCallback && typeof cancelCallback === 'function') {
                    cancelCallback();
                }
            });
        };
    </script>
@endpush

{{-- Set a flag to indicate SweetAlert2 is loaded --}}
@php
    $loadedSweetAlert = true;
@endphp 