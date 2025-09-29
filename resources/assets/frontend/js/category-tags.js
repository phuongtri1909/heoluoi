class CategoryTags {
    constructor(options) {
        this.containerSelector = options.containerSelector;
        this.inputSelector = options.inputSelector;
        this.hiddenInputSelector = options.hiddenInputSelector;
        this.tagsSelector = options.tagsSelector;
        this.suggestionsSelector = options.suggestionsSelector;
        this.allCategories = options.allCategories || [];
        
        this.categories = [];
        this.currentSuggestionIndex = -1;
        
        this.init();
    }
    
    init() {
        this.container = document.querySelector(this.containerSelector);
        this.input = document.querySelector(this.inputSelector);
        this.hiddenInput = document.querySelector(this.hiddenInputSelector);
        this.tagsContainer = document.querySelector(this.tagsSelector);
        this.suggestionsContainer = document.querySelector(this.suggestionsSelector);
        
        if (!this.container || !this.input || !this.hiddenInput || !this.tagsContainer || !this.suggestionsContainer) {
            console.error('CategoryTags: Required elements not found');
            return;
        }
        
        this.bindEvents();
        this.loadInitialCategories();
        this.updatePlaceholder();
    }
    
    bindEvents() {
        // Input events
        this.input.addEventListener('input', (e) => this.handleInput(e));
        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));
        this.input.addEventListener('paste', (e) => this.handlePaste(e));
        this.input.addEventListener('blur', () => this.hideSuggestions());
        this.input.addEventListener('focus', () => this.updatePlaceholder());
        
        // Container click to focus input
        this.container.addEventListener('click', () => this.input.focus());
        
        // Prevent form submission on Enter in input
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    }
    
    loadInitialCategories() {
        const initialValue = this.hiddenInput.value;
        if (initialValue) {
            const initialCategories = this.parseCategories(initialValue);
            this.categories = [];
            initialCategories.forEach(category => {
                this.addCategory(category, false);
            });
        }
    }
    
    parseCategories(text) {
        return text.split(',')
                  .map(cat => cat.trim())
                  .filter(cat => cat.length > 0);
    }
    
    // Helper method to check if category exists (case insensitive)
    categoryExists(categoryName) {
        return this.categories.some(cat => cat.toLowerCase() === categoryName.toLowerCase());
    }
    
    // Helper method to find existing category with different case
    findExistingCategory(categoryName) {
        return this.categories.find(cat => cat.toLowerCase() === categoryName.toLowerCase());
    }
    
    addCategory(categoryName, updateHidden = true) {
        categoryName = categoryName.trim();
        
        // Check for empty categories
        if (!categoryName) {
            return false;
        }
        
        // Check for duplicate categories (case insensitive)
        if (this.categoryExists(categoryName)) {
            return false;
        }
        
        this.categories.push(categoryName);
        this.renderCategory(categoryName);
        
        if (updateHidden) {
            this.updateHiddenInput();
        }
        
        this.updatePlaceholder();
        return true;
    }
    
    removeCategory(categoryName) {
        const index = this.categories.indexOf(categoryName);
        if (index > -1) {
            this.categories.splice(index, 1);
            this.removeTagElement(categoryName);
            this.updateHiddenInput();
            this.updatePlaceholder();
        }
    }
    
    renderCategory(categoryName) {
        const tag = document.createElement('div');
        tag.className = 'category-tag';
        tag.setAttribute('data-category', categoryName);
        
        const textSpan = document.createElement('span');
        textSpan.className = 'category-tag-text';
        textSpan.textContent = categoryName;
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'category-tag-remove';
        removeBtn.innerHTML = '×';
        removeBtn.title = 'Xóa thể loại';
        
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeCategory(categoryName);
        });
        
        tag.appendChild(textSpan);
        tag.appendChild(removeBtn);
        
        this.tagsContainer.appendChild(tag);
    }
    
    removeTagElement(categoryName) {
        const tagElement = this.tagsContainer.querySelector(`[data-category="${categoryName}"]`);
        if (tagElement) {
            tagElement.remove();
        }
    }
    
    updateHiddenInput() {
        this.hiddenInput.value = this.categories.join(', ');
    }
    
    updatePlaceholder() {
        if (this.categories.length > 0) {
            this.input.placeholder = 'Thêm thể loại khác...';
        } else {
            
        }
    }
    
    handleInput(e) {
        const query = e.target.value.trim();
        
        // Check if user typed comma
        if (query.includes(',')) {
            this.processMultipleCategories(query);
            e.target.value = '';
            this.hideSuggestions();
            return;
        }
        
        if (query.length > 0) {
            this.showSuggestions(query);
        } else {
            this.hideSuggestions();
        }
    }
    
    handleKeydown(e) {
        const suggestions = this.suggestionsContainer.querySelectorAll('.category-suggestion');
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.currentSuggestionIndex = Math.min(this.currentSuggestionIndex + 1, suggestions.length - 1);
                this.updateSuggestionHighlight(suggestions);
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                this.currentSuggestionIndex = Math.max(this.currentSuggestionIndex - 1, -1);
                this.updateSuggestionHighlight(suggestions);
                break;
                
            case 'Enter':
                e.preventDefault();
                if (this.currentSuggestionIndex >= 0 && suggestions[this.currentSuggestionIndex]) {
                    const categoryName = suggestions[this.currentSuggestionIndex].textContent.trim();
                    this.selectSuggestion(categoryName);
                } else {
                    const value = this.input.value.trim();
                    if (value) {
                        if (value.includes(',')) {
                            this.processMultipleCategories(value);
                        } else {
                            // Check if category already exists before adding (case insensitive)
                            if (!this.categoryExists(value)) {
                                this.addCategory(value);
                            } else {
                                // Show brief feedback for duplicate with existing case
                                const existingCategory = this.findExistingCategory(value);
                                this.showDuplicateWarning(existingCategory);
                            }
                        }
                        this.input.value = '';
                    }
                }
                this.hideSuggestions();
                break;
                
            case 'Escape':
                this.hideSuggestions();
                this.currentSuggestionIndex = -1;
                break;
                
            case 'Backspace':
                if (this.input.value === '' && this.categories.length > 0) {
                    // Remove last category when backspace on empty input
                    const lastCategory = this.categories[this.categories.length - 1];
                    this.removeCategory(lastCategory);
                }
                break;
        }
    }
    
    handlePaste(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        
        if (pastedText.includes(',')) {
            this.processMultipleCategories(pastedText);
            this.input.value = '';
        } else {
            this.input.value = pastedText.trim();
            const query = pastedText.trim();
            if (query.length > 0) {
                this.showSuggestions(query);
            }
        }
    }
    
    processMultipleCategories(text) {
        const categories = this.parseCategories(text);
        let addedCount = 0;
        let duplicateCount = 0;
        let duplicateNames = [];
        
        categories.forEach(category => {
            if (this.categoryExists(category)) {
                duplicateCount++;
                const existingCategory = this.findExistingCategory(category);
                duplicateNames.push(existingCategory);
            } else if (this.addCategory(category)) {
                addedCount++;
            }
        });
        
        // Show feedback
        if (duplicateCount > 0) {
            if (duplicateCount === 1) {
                this.showDuplicateWarning(duplicateNames[0]);
            } else {
                this.showDuplicateWarning('', duplicateCount);
            }
        }
        
        if (addedCount > 0) {
            this.input.focus();
        }
    }
    
    showDuplicateWarning(categoryName = '', count = 1) {
        const warningText = count > 1 
            ? `${count} thể loại đã tồn tại` 
            : `Thể loại "${categoryName}" đã tồn tại`;
            showToast(warningText, 'warning');
    }
    
    showSuggestions(query) {
        const filtered = this.allCategories.filter(cat => 
            cat.toLowerCase().includes(query.toLowerCase()) && 
            !this.categoryExists(cat) // Use case insensitive check
        );
        
        this.suggestionsContainer.innerHTML = '';
        
        if (filtered.length === 0) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'category-empty-message';
            emptyDiv.textContent = 'Không tìm thấy thể loại phù hợp';
            this.suggestionsContainer.appendChild(emptyDiv);
        } else {
            filtered.forEach((category, index) => {
                const div = document.createElement('div');
                div.className = 'category-suggestion';
                
                const icon = document.createElement('i');
                icon.className = 'fas fa-tag category-suggestion-icon';
                
                const text = document.createElement('span');
                text.className = 'category-suggestion-text';
                text.textContent = category;
                
                div.appendChild(icon);
                div.appendChild(text);
                
                div.addEventListener('click', () => this.selectSuggestion(category));
                this.suggestionsContainer.appendChild(div);
            });
        }
        
        this.suggestionsContainer.classList.remove('d-none');
        this.currentSuggestionIndex = -1;
    }
    
    hideSuggestions() {
        setTimeout(() => {
            this.suggestionsContainer.classList.add('d-none');
        }, 200);
    }
    
    selectSuggestion(categoryName) {
        // Check if category already exists (case insensitive)
        if (!this.categoryExists(categoryName)) {
            this.addCategory(categoryName);
        } else {
            const existingCategory = this.findExistingCategory(categoryName);
            this.showDuplicateWarning(existingCategory);
        }
        this.input.value = '';
        this.hideSuggestions();
        this.input.focus();
    }
    
    updateSuggestionHighlight(suggestions) {
        suggestions.forEach((suggestion, index) => {
            suggestion.classList.toggle('active', index === this.currentSuggestionIndex);
        });
    }
    
    // Public methods
    getCategories() {
        return [...this.categories];
    }
    
    clearCategories() {
        this.categories = [];
        this.tagsContainer.innerHTML = '';
        this.updateHiddenInput();
        this.updatePlaceholder();
    }
    
    setCategories(categories) {
        this.clearCategories();
        categories.forEach(category => {
            this.addCategory(category, false);
        });
        this.updateHiddenInput();
        this.updatePlaceholder();
    }
}