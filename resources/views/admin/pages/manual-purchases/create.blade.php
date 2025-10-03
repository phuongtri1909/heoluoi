@extends('admin.layouts.app')

@section('content-auth')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Cộng/Trừ quyền truy cập cho người dùng</h6>
                        <a href="{{ route('admin.manual-purchases.index') }}" class="btn btn-secondary btn-sm">
                            Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.manual-purchases.store') }}" id="purchaseForm">
                        @csrf
                        
                                        <!-- Multi-User Selection -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Chọn người dùng</label>
                                    
                                    <!-- Selected Users Display -->
                                    <div id="selected_users" class="selected-items mb-2"></div>
                                    
                                    <!-- Search Input -->
                                    <input type="text" class="form-control" 
                                           id="user_search_input" placeholder="Gõ email hoặc tên để tìm..."
                                           autocomplete="off">
                                    
                                    <!-- Search Results -->
                                    <div id="user_search_results" class="search-results mt-2" style="display: none;"></div>
                                    
                                    <!-- Hidden inputs for selected users -->
                                    <div id="user_hidden_inputs"></div>
                                    
                                    <div class="form-text">Có thể chọn nhiều người dùng cùng lúc</div>
                                </div>
                            </div>
                        </div>

                        <!-- Purchase Type -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="type" class="form-control-label">Loại quyền truy cập</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="storyType" value="story" required checked>
                                        <label class="form-check-label" for="storyType">
                                            Mua truyện (toàn bộ truyện)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="chapterType" value="chapter">
                                        <label class="form-check-label" for="chapterType">
                                            Mua chương (từng chương riêng lẻ)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Multi-Story Selection -->
                        <div class="row" id="storySelection" style="display: none;">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Chọn truyện</label>
                                    
                                    <!-- Selected Stories Display -->
                                    <div id="selected_stories" class="selected-items mb-2"></div>
                                    
                                    <!-- Search Input -->
                                    <input type="text" class="form-control" 
                                           id="story_search_input" placeholder="Gõ tên truyện hoặc tác giả..."
                                           autocomplete="off">
                                    
                                    <!-- Search Results -->
                                    <div id="story_search_results" class="search-results mt-2" style="display: none;"></div>
                                    
                                    <!-- Hidden inputs for selected stories -->
                                    <div id="story_hidden_inputs"></div>
                                    
                                    <div class="form-text">Có thể chọn nhiều truyện cùng lúc</div>
                                </div>
                            </div>
                        </div>

                        <!-- Multi-Chapter Selection -->
                        <div class="row" id="chapterSelection" style="display: none;">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Chọn chương</label>
                                    
                                    <!-- Selected Chapters Display -->
                                    <div id="selected_chapters" class="selected-items mb-2"></div>
                                    
                                    <!-- Search Input -->
                                    <input type="text" class="form-control" 
                                           id="chapter_search_input" placeholder="Gõ tên chương, số chương hoặc tên truyện..."
                                           autocomplete="off">
                                    
                                    <!-- Search Results -->
                                    <div id="chapter_search_results" class="search-results mt-2" style="display: none;"></div>
                                    
                                    <!-- Hidden inputs for selected chapters -->
                                    <div id="chapter_hidden_inputs"></div>
                                    
                                    <div class="form-text">Có thể chọn nhiều chương cùng lúc</div>
                                </div>
                            </div>
                        </div>

                        <!-- Reference ID -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="reference_id" class="form-control-label">Reference ID (tùy chọn)</label>
                                    <input type="text" class="form-control @error('reference_id') is-invalid @enderror" 
                                           name="reference_id" id="reference_id" placeholder="ID từ web cũ hoặc ghi chú tham khảo">
                                    <div class="form-text">ID từ web cũ hoặc thông tin tham khảo khác</div>
                                    @error('reference_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes" class="form-control-label">Ghi chú (tùy chọn)</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              name="notes" id="notes" rows="3" placeholder="Lý do thêm quyền truy cập này..."></textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Lưu giao dịch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Selected items styling */
.selected-items {
    background: white;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    padding: 12px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 50px;
    align-items: center;
}

.selected-item {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px 12px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
    transition: all 0.2s ease;
    border: 2px solid transparent;
    white-space: nowrap;
    width: auto;
}

.selected-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
    border-color: rgba(255, 255, 255, 0.3);
}

.selected-item .remove-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    padding: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    margin-left: 5px;
}

.selected-item .remove-btn:hover {
    background: rgba(255, 255, 255, 0.4);
    transform: scale(1.1);
}

/* Empty state styling */
.selected-items:empty {
    background: #f8f9fa;
    border-color: #dee2e6;
}

.selected-items:empty::before {
    content: 'Chưa có lựa chọn nào...';
    color: #6c757d;
    font-style: italic;
    font-size: 14px;
}

/* Search results styling */
.search-results {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
}

.search-result-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item.selected {
    background-color: #e7f3ff;
    color: #0056b3;
}

.search-highlight {
    background-color: #fff3cd;
    color: #856404;
    padding: 1px 3px;
    border-radius: 2px;
}

.checkbox-indicator {
    width: 16px;
    height: 16px;
    border: 2px solid #ddd;
    border-radius: 3px;
    display: inline-block;
    text-align: center;
    line-height: 12px;
    font-size: 12px;
}

.search-result-item.selected .checkbox-indicator {
    background: #007bff;
    border-color: #007bff;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function waitForElements() {
        const requiredElements = [
            'storySelection', 'chapterSelection',
            'selected_users', 'selected_stories', 'selected_chapters',
            'user_search_results', 'story_search_results', 'chapter_search_results',
            'user_hidden_inputs', 'story_hidden_inputs', 'chapter_hidden_inputs'
        ];
        
        const missingElements = requiredElements.filter(id => !document.getElementById(id));
        
        if (missingElements.length > 0) {
            console.log('Missing elements:', missingElements);
            return false;
        }
        return true;
    }
    
    if (!waitForElements()) {
        setTimeout(() => {
            if (waitForElements()) {
                initializeAll();
            }
        }, 1000);
    } else {
        initializeAll();
    }
    
    function initializeAll(retryCount = 0) {
        const usrEl = document.getElementById('selected_users');
        const storyEl = document.getElementById('selected_stories');
        const chEl = document.getElementById('selected_chapters');
        
        if (!usrEl || !storyEl || !chEl) {
            if (retryCount >= 5) {
                return;
            }
            setTimeout(() => initializeAll(retryCount + 1), 500);
            return;
        }
        
        document.querySelectorAll('input[name="type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const storySelection = document.getElementById('storySelection');
            const chapterSelection = document.getElementById('chapterSelection');
            
            if (this.value === 'story') {
                storySelection.style.display = 'block';
                chapterSelection.style.display = 'none';
            selectedChapters = [];
            refreshDropdown('story');
            } else {
                chapterSelection.style.display = 'block';
                storySelection.style.display = 'none';
                selectedStories = [];
                refreshDropdown('chapter');
            }
        });
    });

    const users = {!! json_encode(\App\Models\User::whereIn('role', ['user'])->orderBy('email')->get()) !!};
    const stories = {!! json_encode(\App\Models\Story::where('combo_price', '>', 0)->orderBy('title')->get()) !!};
    const chapters = {!! json_encode(\App\Models\Chapter::with('story')->where('price', '>', 0)->orderBy('number')->get()) !!};

    // Global variables for selected items
    let selectedUsers = [];
    let selectedStories = [];
    let selectedChapters = [];

    // Initialize multi-select functionality
    initMultiSearch('user', users);
    initMultiSearch('story', stories);
    initMultiSearch('chapter', chapters);
    
    // Load initial data for all searches
    setTimeout(() => {
        if (document.getElementById('storySelection')) {
            document.getElementById('storySelection').style.display = 'block';
        }
        loadInitialData();
    }, 500);
    
    function loadInitialData() {
        const userResults = document.getElementById('user_search_results');
        const storyResults = document.getElementById('story_search_results');
        const chapterResults = document.getElementById('chapter_search_results');
        
        if (userResults) {
            displayMultiResults(userResults, users.slice(0, 10), 'user');
            userResults.style.display = 'block';
        }
        
        if (storyResults) {
            displayMultiResults(storyResults, stories.slice(0, 10), 'story');
            storyResults.style.display = 'block';
        }
        
        if (chapterResults) {
            displayMultiResults(chapterResults, chapters.slice(0, 10), 'chapter');
            chapterResults.style.display = 'block';
        }
    }

    function initMultiSearch(type, data) {
        const searchInput = document.getElementById(type + '_search_input');
        const results = document.getElementById(type + '_search_results');
        const selectedContainer = document.getElementById('selected_' + type + 's');
        const hiddenInputs = document.getElementById(type + '_hidden_inputs');
        
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                results.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                const filtered = data.filter(item => {
                    let display = '';
                    if (type === 'user') {
                        display = item.email + (item.name ? ' (' + item.name + ')' : '');
                    } else if (type === 'story') {
                        display = item.title + (item.author_name ? ' - ' + item.author_name : '');
                    } else if (type === 'chapter') {
                        display = 'Chương ' + item.number + ': ' + item.title + ' (' + item.story.title + ')';
                    }
                    return display.toLowerCase().includes(query.toLowerCase());
                });
                
                displayMultiResults(results, filtered, type);
            }, 300);
        });

        searchInput.addEventListener('blur', function() {
            setTimeout(() => {
                results.style.display = 'none';
            }, 200);
        });
    }

    function displayMultiResults(container, items, type) {
        if (!container) {
            console.error('Results container not found for type:', type);
            return;
        }
        
        container.innerHTML = '';
        
        if (items.length === 0) {
            container.innerHTML = '<div class="search-result-item">Không tìm thấy kết quả</div>';
        } else {
            items.forEach(function(item) {
                const isSelected = getSelectedItems(type).some(selected => selected.id === item.id);
                
                const div = document.createElement('div');
                div.className = 'search-result-item';
                if (isSelected) div.classList.add('selected');
                
                let display = '';
                if (type === 'user') {
                    display = item.email + (item.name ? ' (' + item.name + ')' : '');
                } else if (type === 'story') {
                    display = item.title + (item.author_name ? ' - ' + item.author_name : '');
                } else if (type === 'chapter') {
                    display = 'Chương ' + item.number + ': ' + item.title + ' (' + item.story.title + ')';
                }
                
                div.innerHTML = '<span class="checkbox-indicator">' + (isSelected ? '✓' : '') + '</span><span>' + display + '</span>';
                
                div.addEventListener('click', function() {
                    toggleSelection(type, item, display);
                });
                
                container.appendChild(div);
            });
        }
        
        container.style.display = 'block';
    }

    function toggleSelection(type, item, display) {
        const selected = getSelectedItems(type);
        const isSelected = selected.some(s => s.id === item.id);
        
        if (isSelected) {
            if (type === 'user' && selectedUsers.length > 0) {
                selectedUsers = selectedUsers.filter(s => s.id !== item.id);
            } else if (type === 'story' && selectedStories.length > 0) {
                selectedStories = selectedStories.filter(s => s.id !== item.id);
            } else if (type === 'chapter' && selectedChapters.length > 0) {
                selectedChapters = selectedChapters.filter(s => s.id !== item.id);
            }
        } else {
            const selectedItem = {id: item.id, display: display};
            if (type === 'user') selectedUsers.push(selectedItem);
            else if (type === 'story') selectedStories.push(selectedItem);
            else if (type === 'chapter') selectedChapters.push(selectedItem);
        }
        
        const correspondingContainer = document.getElementById('selected_' + type + 's');
        if (correspondingContainer && correspondingContainer.offsetParent !== null) {
            updateSelectedDisplay(type);
        }
        
        updateHiddenInputs(type);
        refreshDropdown(type);
    }

    function getSelectedItems(type) {
        if (type === 'user') return selectedUsers;
        else if (type === 'story') return selectedStories;
        else if (type === 'chapter') return selectedChapters;
        return [];
    }

    function updateSelectedDisplay(type) {
        const selected = getSelectedItems(type);
        const container = document.getElementById('selected_' + type + 's');
        
        if (!container || container.offsetParent === null) {
            return;
        }
        
        if (selected.length === 0) {
            container.style.display = 'none';
            return;
        }
        
        container.innerHTML = '';
        selected.forEach(function(item) {
            const tag = document.createElement('div');
            tag.className = 'selected-item';
            tag.innerHTML = '<span>' + item.display + '</span><button type="button" class="remove-btn" onclick="removeSelection(\'' + type + '\', ' + item.id + ')">×</button>';
            container.appendChild(tag);
        });
        
        container.style.display = 'flex';
    }

    function updateHiddenInputs(type) {
        const selected = getSelectedItems(type);
        const container = document.getElementById(type + '_hidden_inputs');
        
        if (!container) {
            return;
        }
        
        container.innerHTML = '';
        selected.forEach(function(item) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = type + '_ids[]' ;
            input.value = item.id;
            container.appendChild(input);
        });
    }

    function refreshDropdown(type) {
        const data = type === 'user' ? users : (type === 'story' ? stories : chapters);
        const results = document.getElementById(type + '_search_results');
        const input = document.getElementById(type + '_search_input');
        const query = input.value.trim();
        
        // Nếu có search query thì filter, không thì hiển thị 10 items đầu
        let filtered = [];
        if (query.length >= 2) {
            filtered = data.filter(item => {
                let display = '';
                if (type === 'user') {
                    display = item.email + (item.name ? ' (' + item.name + ')' : '');
                } else if (type === 'story') {
                    display = item.title + (item.author_name ? ' - ' + item.author_name : '');
                } else if (type === 'chapter') {
                    display = 'Chương ' + item.number + ': ' + item.title + ' (' + item.story.title + ')';
                }
                return display.toLowerCase().includes(query.toLowerCase());
            });
        } else {
            filtered = data.slice(0, 10);
        }
        
        displayMultiResults(results, filtered, type);
        results.style.display = 'block';
    }

        window.removeSelection = function(type, id) {
            if (type === 'user') selectedUsers = selectedUsers.filter(s => s.id !== id);
            else if (type === 'story') selectedStories = selectedStories.filter(s => s.id !== id);
            else if (type === 'chapter') selectedChapters = selectedChapters.filter(s => s.id !== id);
            
            updateSelectedDisplay(type);
            updateHiddenInputs(type);
            refreshDropdown(type);
        };
    }
});
</script>
@endsection
