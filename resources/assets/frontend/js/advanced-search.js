// Advanced Search Component JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Toggle search content
    const searchHeader = document.querySelector('.advanced-search-container .search-header');
    const searchContent = document.querySelector('.advanced-search-container .search-content');
    const searchToggle = document.querySelector('.advanced-search-container .search-toggle i');
    
    if (searchHeader && searchContent) {
        searchHeader.addEventListener('click', function() {
            const isHidden = searchContent.style.display === 'none';
            searchContent.style.display = isHidden ? 'block' : 'none';
            searchToggle.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
        });
    }
    
    // Add ripple effect to buttons
    const searchBtns = document.querySelectorAll('.advanced-search-container .search-btn');
    searchBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const ripple = this.querySelector('.btn-ripple');
            if (ripple) {
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.animation = 'none';
                ripple.offsetHeight; // Trigger reflow
                ripple.style.animation = 'advancedSearchRipple 0.6s linear';
            }
        });
    });
    
    // Add focus effects to selects
    const selectWrappers = document.querySelectorAll('.advanced-search-container .select-wrapper');
    selectWrappers.forEach(wrapper => {
        const select = wrapper.querySelector('.filter-select');
        const arrow = wrapper.querySelector('.select-arrow');
        
        if (select && arrow) {
            select.addEventListener('focus', function() {
                wrapper.style.transform = 'translateY(-2px)';
                arrow.style.color = 'var(--primary-color-3)';
            });
            
            select.addEventListener('blur', function() {
                wrapper.style.transform = 'translateY(0)';
                arrow.style.color = 'var(--primary-color-5)';
            });
        }
    });
    
    // Auto-submit form when filters change (optional)
    const filterSelects = document.querySelectorAll('.advanced-search-container .filter-select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Uncomment the line below if you want auto-submit on filter change
            // this.form.submit();
        });
    });
    
    // Add loading state to form submission
    const searchForm = document.querySelector('.advanced-search-container #advanced-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('.search-btn.primary');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Đang tìm...</span>';
                submitBtn.disabled = true;
            }
        });
    }
    
    // Add keyboard navigation support
    const filterGroups = document.querySelectorAll('.advanced-search-container .filter-group');
    filterGroups.forEach((group, index) => {
        const select = group.querySelector('.filter-select');
        if (select) {
            select.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.form.submit();
                } else if (e.key === 'ArrowDown' && index < filterGroups.length - 1) {
                    e.preventDefault();
                    const nextSelect = filterGroups[index + 1].querySelector('.filter-select');
                    if (nextSelect) nextSelect.focus();
                } else if (e.key === 'ArrowUp' && index > 0) {
                    e.preventDefault();
                    const prevSelect = filterGroups[index - 1].querySelector('.filter-select');
                    if (prevSelect) prevSelect.focus();
                }
            });
        }
    });
    
    // Add smooth scroll to results when form is submitted
    const form = document.querySelector('.advanced-search-container #advanced-search-form');
    if (form) {
        form.addEventListener('submit', function() {
            setTimeout(() => {
                const resultsSection = document.querySelector('.col-12.col-md-7');
                if (resultsSection) {
                    resultsSection.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }, 100);
        });
    }
});