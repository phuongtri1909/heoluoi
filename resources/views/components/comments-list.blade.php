{{-- Show pinned comments first --}}
@if(isset($pinnedComments) && $pinnedComments->count() > 0)
    <div class="pinned-comments mb-4">
        <div class="pinned-header mb-3">
            <h6 class="pinned-title">
                <i class="fas fa-thumbtack text-warning me-2"></i> Bình luận đã ghim
            </h6>
        </div>
        @foreach($pinnedComments as $comment)
            @include('components.comments-item', ['comment' => $comment])
        @endforeach
    </div>
    
    @if(isset($regularComments) && $regularComments->count() > 0)
        <div class="regular-comments-header mb-3">
            <h6 class="text-muted">Bình luận khác</h6>
            <div class="header-line"></div>
        </div>
    @endif
@endif

{{-- Show regular comments --}}
@if(isset($regularComments))
    <div class="regular-comments-container">
        @foreach($regularComments as $comment)
            @include('components.comments-item', ['comment' => $comment])
        @endforeach

        @if($regularComments->count() == 0 && (!isset($pinnedComments) || $pinnedComments->count() == 0))
            <div class="text-center py-4 text-muted empty-comments animate__animated animate__fadeIn">
                <i class="far fa-comment-dots fa-3x mb-3"></i>
                <p>Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
            </div>
        @endif
    </div>
@endif

@once
    @push('styles')
    <style>
        .pinned-comments {
            position: relative;
        }
        
        .pinned-header {
            position: relative;
        }
        
        .pinned-title {
            font-weight: 600;
            color: #555;
            display: inline-block;
            background: #fffbea;
            padding: 5px 15px;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(255, 193, 7, 0.2);
        }
        
        .regular-comments-header {
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .regular-comments-header h6 {
            font-weight: 600;
            margin-bottom: 0;
            white-space: nowrap;
        }
        
        .header-line {
            flex-grow: 1;
            height: 1px;
            background: #e0e0e0;
        }
        
        .empty-comments {
            color: #888;
            padding: 30px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.05);
        }
        
        .empty-comments i {
            opacity: 0.7;
        }
        
        @media (max-width: 768px) {
            .pinned-title {
                font-size: 14px;
                padding: 3px 10px;
            }
            
            .empty-comments {
                padding: 20px;
            }
            
            .empty-comments i {
                font-size: 2em;
            }
        }
    </style>
    @endpush
@endonce