{{-- No need for toast container when using SweetAlert2 --}}

@push('scripts')
    <script>
        function showToast(message, type = 'success') {
            // Use SweetAlert2 instead of Bootstrap toast
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
        }
    </script>
@endpush

@push('scripts-main')
    <script>
        function showToast(message, type = 'success') {
            // Use SweetAlert2 instead of Bootstrap toast
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
        }
    </script>
@endpush
