<button id="topButton" class="btn bg-3 rounded-circle position-fixed bottom-0 end-0 mx-2 mb-2 mx-md-4" 
        style="display: none; z-index: 1000;">
    <i class="fas fa-arrow-up text-white"></i>
</button>

<style>
    #topButton {
        width: 45px;
        height: 45px;
        transition: all 0.3s ease;
    }
    
    #topButton:hover {
        opacity: 1;
        transform: translateY(-5px);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const topButton = document.getElementById('topButton');
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 300) {
                topButton.style.display = 'block';
            } else {
                topButton.style.display = 'none';
            }
        });
        
        topButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>