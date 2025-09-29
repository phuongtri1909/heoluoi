<!-- Reading Settings Floating Button -->
<div class="reading-settings-container position-fixed bottom-0 start-0 mx-2 mb-2 mx-md-4">
    <div class="reading-settings-menu">
        @if(request()->routeIs('chapter'))
            <!-- Full settings for chapter page -->
            <button class="reading-setting-btn fullscreen-btn" title="Toàn màn hình">
                <i class="fas fa-expand"></i>
            </button>
            <button class="reading-setting-btn bookmark-btn" title="Đánh dấu trang">
                <i class="fas fa-bookmark"></i>
            </button>
            <button class="reading-setting-btn theme-btn" title="Chế độ tối/sáng">
                <i class="fas fa-moon"></i>
            </button>
            <button class="reading-setting-btn book-mode-btn" title="Chế độ sách">
                <i class="fas fa-book-open"></i>
            </button>
            <button class="reading-setting-btn font-increase-btn" title="Tăng cỡ chữ">
                <i class="fas fa-plus"></i>
            </button>
            <button class="reading-setting-btn font-decrease-btn" title="Giảm cỡ chữ">
                <i class="fas fa-minus"></i>
            </button>
            <button class="reading-setting-btn font-family-btn" title="Đổi font chữ">
                <i class="fas fa-font"></i>
            </button>
        @else
            <!-- Only theme button for other pages -->
            <button class="reading-setting-btn theme-btn" title="Chế độ tối/sáng">
                <i class="fas fa-moon"></i>
            </button>
        @endif
    </div>
    <button class="reading-settings-toggle">
        <i class="fas fa-cog"></i>
    </button>
</div>

@push('styles')
<style>
/* Reading Settings Styles */
.reading-settings-container {
    z-index: 1000;
    display: block !important;
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.reading-settings-toggle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background-color: var(--primary-color-3);
    color: white;
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.reading-settings-toggle:hover {
    transform: scale(1.1);
}

.reading-settings-toggle i {
    font-size: 20px;
}

.reading-settings-menu {
    position: absolute;
    bottom: 60px;
    left: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    transform: translateY(10px);
    z-index: 1001;
}

.reading-settings-menu.active {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
    display: flex !important;
}

.reading-setting-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background-color: white;
    color: var(--primary-color-3);
    border: 2px solid var(--primary-color-3);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.reading-setting-btn:hover {
    transform: scale(1.1);
    background-color: var(--primary-color-3);
    color: white;
}

.reading-setting-btn.active {
    background-color: var(--primary-color-3);
    color: white;
}

/* Font family dropdown */
.font-family-dropdown {
    position: absolute;
    left: 60px;
    bottom: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    padding: 10px;
    display: none;
    border: 1px solid #ddd;
}

.font-family-dropdown.active {
    display: block;
}

.font-family-dropdown button {
    display: block;
    width: 100%;
    text-align: left;
    padding: 8px 12px;
    border: none;
    background: none;
    cursor: pointer;
    border-radius: 4px;
    color: #333;
    transition: background-color 0.2s ease;
}

.font-family-dropdown button:hover {
    background-color: #f0f0f0;
}

/* Font families */
body.font-segoe {
    font-family: 'Segoe UI', 'Segoe UI Variable', -apple-system, BlinkMacSystemFont, system-ui, sans-serif !important;
}

body.font-roboto {
    font-family: 'Roboto', sans-serif !important;
}

body.font-open-sans {
    font-family: 'Open Sans', sans-serif !important;
}

body.font-lora {
    font-family: 'Lora', serif !important;
}

body.font-merriweather {
    font-family: 'Merriweather', serif !important;
}

/* Book mode styles */
body.book-mode #chapter-content {
    background-color: #f8f5e8;
    padding: 30px;
    color: #333;
    border: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Dark mode book mode */
body.dark-mode.book-mode #chapter-content {
    background-color: #2d2d2d;
    color: #e0e0e0;
    border-color: #404040;
}

/* Dark mode for reading settings */
body.dark-mode .reading-settings-toggle {
    background-color: var(--primary-color-3) !important;
    color: white !important;
}

body.dark-mode .reading-setting-btn {
    background-color: #2d2d2d !important;
    color: var(--primary-color-3) !important;
    border-color: var(--primary-color-3) !important;
}

body.dark-mode .reading-setting-btn:hover {
    background-color: var(--primary-color-3) !important;
    color: white !important;
}

body.dark-mode .reading-setting-btn.active {
    background-color: var(--primary-color-3) !important;
    color: white !important;
}

body.dark-mode .font-family-dropdown {
    background-color: #2d2d2d !important;
    border-color: #404040 !important;
}

body.dark-mode .font-family-dropdown button {
    color: #e0e0e0 !important;
}

body.dark-mode .font-family-dropdown button:hover {
    background-color: #404040 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const readingSettingsToggle = document.querySelector('.reading-settings-toggle');
    const readingSettingsMenu = document.querySelector('.reading-settings-menu');
    const themeBtn = document.querySelector('.theme-btn');
    
    // Toggle menu functionality
    if (readingSettingsToggle && readingSettingsMenu) {
        readingSettingsToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Reading settings toggle clicked');
            
            readingSettingsMenu.classList.toggle('active');
            console.log('Menu active:', readingSettingsMenu.classList.contains('active'));
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.reading-settings-container')) {
                readingSettingsMenu.classList.remove('active');
            }
        });
    }

    // Theme toggle functionality
    if (themeBtn) {
        themeBtn.addEventListener('click', function() {
            console.log('Theme button clicked');
            document.body.classList.toggle('dark-mode');
            themeBtn.classList.toggle('active');

            if (document.body.classList.contains('dark-mode')) {
                themeBtn.innerHTML = '<i class="fas fa-sun"></i>';
                localStorage.setItem('dark-mode', 'true');
            } else {
                themeBtn.innerHTML = '<i class="fas fa-moon"></i>';
                localStorage.setItem('dark-mode', 'false');
            }
        });
    }

    // Load saved theme preference
    function loadSavedTheme() {
        const savedTheme = localStorage.getItem('dark-mode');
        if (savedTheme === 'true') {
            document.body.classList.add('dark-mode');
            if (themeBtn) {
                themeBtn.innerHTML = '<i class="fas fa-sun"></i>';
                themeBtn.classList.add('active');
            }
        }
    }

    loadSavedTheme();

    let lastScrollTop = 0;
    const readingSettingsContainer = document.querySelector('.reading-settings-container');
    let isScrolling = false;

    function handleScroll() {
        if (isScrolling) return;
        
        isScrolling = true;
        requestAnimationFrame(function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                readingSettingsContainer.style.opacity = '0';
                readingSettingsContainer.style.transform = 'translateY(20px)';
                readingSettingsMenu.classList.remove('active');
            }
    
            else if (scrollTop < lastScrollTop) {
                readingSettingsContainer.style.opacity = '1';
                readingSettingsContainer.style.transform = 'translateY(0)';
            }
            
            lastScrollTop = scrollTop;
            isScrolling = false;
        });
    }

    function showContainerOnLoad() {
        readingSettingsContainer.style.opacity = '1';
        readingSettingsContainer.style.transform = 'translateY(0)';
    }

    window.addEventListener('scroll', handleScroll);
    
    showContainerOnLoad();
    

    // Chapter-specific functionality (only on chapter pages)
    @if(request()->routeIs('chapter'))
        
        const fullscreenBtn = document.querySelector('.fullscreen-btn');
        const bookmarkBtn = document.querySelector('.bookmark-btn');
        const bookModeBtn = document.querySelector('.book-mode-btn');
        const fontIncreaseBtn = document.querySelector('.font-increase-btn');
        const fontDecreaseBtn = document.querySelector('.font-decrease-btn');
        const fontFamilyBtn = document.querySelector('.font-family-btn');
        const chapterContent = document.getElementById('chapter-content');
        
        // Fullscreen functionality
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.log(`Error attempting to enable fullscreen: ${err.message}`);
                    });
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
                    fullscreenBtn.classList.add('active');
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                        fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
                        fullscreenBtn.classList.remove('active');
                    }
                }
            });
        }

        // Book mode toggle
        if (bookModeBtn) {
            bookModeBtn.addEventListener('click', function() {
                document.body.classList.toggle('book-mode');
                bookModeBtn.classList.toggle('active');
                localStorage.setItem('book-mode', document.body.classList.contains('book-mode'));
            });
        }

        // Font size adjustment
        if (fontIncreaseBtn && fontDecreaseBtn && chapterContent) {
            let currentFontSize = parseInt(window.getComputedStyle(chapterContent).fontSize);

            fontIncreaseBtn.addEventListener('click', function() {
                if (currentFontSize < 24) {
                    currentFontSize += 1;
                    chapterContent.style.fontSize = currentFontSize + 'px';
                    localStorage.setItem('chapter-font-size', currentFontSize);
                }
            });

            fontDecreaseBtn.addEventListener('click', function() {
                if (currentFontSize > 12) {
                    currentFontSize -= 1;
                    chapterContent.style.fontSize = currentFontSize + 'px';
                    localStorage.setItem('chapter-font-size', currentFontSize);
                }
            });
        }

        // Font family functionality
        if (fontFamilyBtn) {
            // Create font family dropdown
            const fontFamilyDropdown = document.createElement('div');
            fontFamilyDropdown.className = 'font-family-dropdown';
            fontFamilyDropdown.innerHTML = `
                <button data-font="font-segoe">Segoe UI (Mặc định)</button>
                <button data-font="font-roboto">Roboto</button>
                <button data-font="font-open-sans">Open Sans</button>
                <button data-font="font-lora">Lora</button>
                <button data-font="font-merriweather">Merriweather</button>
            `;
            document.querySelector('.reading-settings-container').appendChild(fontFamilyDropdown);

            fontFamilyBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fontFamilyDropdown.classList.toggle('active');
            });

            // Font family selection
            fontFamilyDropdown.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function() {
                    const fontClass = this.getAttribute('data-font');

                    // Remove all font classes from body
                    document.body.classList.remove('font-segoe', 'font-roboto', 'font-open-sans',
                        'font-lora', 'font-merriweather');

                    // Add selected font class to body
                    document.body.classList.add(fontClass);

                    // Save preference
                    localStorage.setItem('chapter-font-family', fontClass);

                    // Close dropdown
                    fontFamilyDropdown.classList.remove('active');
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.reading-settings-container')) {
                    fontFamilyDropdown.classList.remove('active');
                }
            });
        }

        // Load saved chapter preferences
        function loadSavedChapterPreferences() {
            // Load font size
            const savedFontSize = localStorage.getItem('chapter-font-size');
            if (savedFontSize && chapterContent) {
                chapterContent.style.fontSize = savedFontSize + 'px';
            }

            // Load font family
            const savedFontFamily = localStorage.getItem('chapter-font-family');
            if (savedFontFamily) {
                document.body.classList.add(savedFontFamily);
            }

            // Load book mode
            if (localStorage.getItem('book-mode') === 'true') {
                document.body.classList.add('book-mode');
                if (bookModeBtn) {
                    bookModeBtn.classList.add('active');
                }
            }
        }

        // Load chapter preferences on page load
        loadSavedChapterPreferences();
    @endif
});
</script>
@endpush

