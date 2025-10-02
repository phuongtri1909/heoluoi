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
            <button class="reading-setting-btn font-increase-btn" title="Tăng cỡ chữ">
                <i class="fas fa-plus"></i>
            </button>
            <button class="reading-setting-btn font-decrease-btn" title="Giảm cỡ chữ">
                <i class="fas fa-minus"></i>
            </button>
            <button class="reading-setting-btn font-family-btn" title="Đổi font chữ">
                <i class="fas fa-font"></i>
            </button>
            <button class="reading-setting-btn background-color-btn" title="Đổi màu nền">
                <i class="fas fa-palette"></i>
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
            
            readingSettingsMenu.classList.toggle('active');
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

    const readingSettingsContainer = document.querySelector('.reading-settings-container');

    function showContainerOnLoad() {
        readingSettingsContainer.style.opacity = '1';
        readingSettingsContainer.style.transform = 'translateY(0)';
    }

    showContainerOnLoad();
    
    @if(request()->routeIs('chapter'))
        
        const fullscreenBtn = document.querySelector('.fullscreen-btn');
        const bookmarkBtn = document.querySelector('.bookmark-btn');
        const fontIncreaseBtn = document.querySelector('.font-increase-btn');
        const fontDecreaseBtn = document.querySelector('.font-decrease-btn');
        const fontFamilyBtn = document.querySelector('.font-family-btn');
        const backgroundColorBtn = document.querySelector('.background-color-btn');
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
                <button data-font="font-noto-sans">Noto Sans</button>
                <button data-font="font-open-sans">Open Sans</button>
                <button data-font="font-lora">Lora</button>
                <button data-font="font-merriweather">Merriweather</button>
            `;
            document.querySelector('.reading-settings-container').appendChild(fontFamilyDropdown);

            fontFamilyBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fontFamilyDropdown.classList.toggle('active');
                // Close background color dropdown if open
                const backgroundColorDropdown = document.querySelector('.background-color-dropdown');
                if (backgroundColorDropdown) {
                    backgroundColorDropdown.classList.remove('active');
                }
            });

            // Font family selection
            fontFamilyDropdown.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function() {
                    const fontClass = this.getAttribute('data-font');

                    // Remove all font classes from body
                    document.body.classList.remove('font-segoe', 'font-roboto', 'font-open-sans', 'font-noto-sans',
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

        // Background color functionality
        if (backgroundColorBtn) {
            // Create background color dropdown
            const backgroundColorDropdown = document.createElement('div');
            backgroundColorDropdown.className = 'background-color-dropdown';
            backgroundColorDropdown.innerHTML = `
                <button data-bg="bg-white">
                    <div class="color-preview" style="background-color: #fff; border: 2px solid #e5e7eb;"></div>
                    <span>Trắng</span>
                </button>
                <button data-bg="bg-sepia">
                    <div class="color-preview" style="background-color: #f5e4c1;"></div>
                    <span>Sepia</span>
                </button>
                <button data-bg="bg-warm">
                    <div class="color-preview" style="background-color: #ffedd5;"></div>
                    <span>Bé Ấm</span>
                </button>
                <button data-bg="bg-cream">
                    <div class="color-preview" style="background-color: #faf4d4;"></div>
                    <span>Xám Xanh</span>
                </button>
                <button data-bg="bg-mint">
                    <div class="color-preview" style="background-color: #ecfef6;"></div>
                    <span>Bạc Hà</span>
                </button>
                <button data-bg="bg-gray-dark">
                    <div class="color-preview" style="background-color: #374151;"></div>
                    <span>Xám</span>
                </button>
                <button data-bg="bg-black">
                    <div class="color-preview" style="background-color: #000;"></div>
                    <span>Đen</span>
                </button>
                <button data-bg="bg-navy">
                    <div class="color-preview" style="background-color: #001f3f;"></div>
                    <span>Xám Đậm</span>
                </button>
                <button data-bg="bg-forest">
                    <div class="color-preview" style="background-color: #052e16;"></div>
                    <span>Rừng</span>
                </button>
                <button data-bg="bg-midnight">
                    <div class="color-preview" style="background-color: #0d1224;"></div>
                    <span>Nửa Đêm</span>
                </button>
            `;
            document.querySelector('.reading-settings-container').appendChild(backgroundColorDropdown);

            backgroundColorBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                backgroundColorDropdown.classList.toggle('active');
                // Close font family dropdown if open
                const fontFamilyDropdown = document.querySelector('.font-family-dropdown');
                if (fontFamilyDropdown) {
                    fontFamilyDropdown.classList.remove('active');
                }
            });

            // Background color selection
            backgroundColorDropdown.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function() {
                    const bgClass = this.getAttribute('data-bg');

                    // Remove all background classes from chapter-content
                    chapterContent.classList.remove('bg-white', 'bg-sepia', 'bg-warm', 'bg-cream', 'bg-mint',
                        'bg-gray-dark', 'bg-black', 'bg-navy', 'bg-forest', 'bg-midnight');

                    // Add selected background class to chapter-content
                    chapterContent.classList.add(bgClass);

                    // Save preference
                    localStorage.setItem('chapter-background-color', bgClass);

                    // Close dropdown
                    backgroundColorDropdown.classList.remove('active');
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.reading-settings-container')) {
                    backgroundColorDropdown.classList.remove('active');
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

            // Load background color
            const savedBackgroundColor = localStorage.getItem('chapter-background-color');
            if (savedBackgroundColor && chapterContent) {
                chapterContent.classList.add(savedBackgroundColor);
            }
        }

        // Load chapter preferences on page load
        loadSavedChapterPreferences();
    @endif
});
</script>
@endpush

