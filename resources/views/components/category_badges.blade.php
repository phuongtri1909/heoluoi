@php
    $mainCategories = $story->categories->where('is_main', true);
    $subCategories = $story->categories->where('is_main', false);
    $displayCategories = collect();

    // Ưu tiên lấy tối đa 3 category chính
    foreach ($mainCategories->take(3) as $category) {
        $displayCategories->push($category);
    }

    // Nếu chưa đủ 3 category, lấy thêm category phụ
    if ($displayCategories->count() < 3) {
        $remainingSlots = 3 - $displayCategories->count();
        foreach ($subCategories->take($remainingSlots) as $category) {
            $displayCategories->push($category);
        }
    }
@endphp

<div class="d-flex">
    @foreach ($displayCategories as $category)
        <span class="badge border border-1 border-color-3 color-3 rounded-pill d-flex align-items-center me-2">
            {{ $category->name }}
        </span>
    @endforeach
</div>
