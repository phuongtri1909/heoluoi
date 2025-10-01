@if($relatedStories && $relatedStories->count() > 0)
<div class="related-stories-section">
    <h3 class="related-stories-title fs-3">TRUYỆN CÙNG THỂ LOẠI</h3>
    <div class="related-stories-list">
        @foreach($relatedStories as $relatedStory)
        <div class="related-story-item">
            <div class="story-cover">
                <img src="{{ Storage::url($relatedStory->cover) }}" alt="{{ $relatedStory->title }}" class="cover-image">
            </div>
            <div class="story-info">
                <h6 class="story-title">{{ $relatedStory->title }}</h6>
                <p class="story-chapters">{{ $relatedStory->chapters_count }} chương</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

@once
@push('styles')
<style>
.related-stories-section {
    margin-top: 20px;
}

.related-stories-title {
    text-align: center;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
    font-size: 16px;
}

.related-stories-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.related-story-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.related-story-item:hover {
    transform: translateX(5px);
}

.story-cover {
    flex-shrink: 0;
    width: 60px;
    height: 90px;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.cover-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.story-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    min-height: 70px;
}

.story-title {
    font-weight: bold;
    color: #333;
    font-size: 14px;
    line-height: 1.3;
    margin: 0 0 5px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.story-chapters {
    color: #666;
    font-size: 12px;
    margin: 0;
    font-weight: normal;
}

/* Dark mode support */
.dark-mode .related-stories-title {
    color: #e0e0e0;
}

.dark-mode .story-title {
    color: #e0e0e0;
}

.dark-mode .story-chapters {
    color: #b0b0b0;
}

/* Responsive */
@media (max-width: 768px) {
    .related-stories-title {
        font-size: 14px;
        margin-bottom: 15px;
    }
    
    .related-stories-list {
        gap: 12px;
    }
    
    .related-story-item {
        gap: 10px;
    }
    
    .story-title {
        font-size: 13px;
    }
    
    .story-chapters {
        font-size: 11px;
    }
}
</style>
@endpush
@endonce
@endif
