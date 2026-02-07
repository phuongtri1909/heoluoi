@extends('layouts.app')
@section('content')
    @include('components.toast')
    @include('components.banner_home')
    <section class="container-xl">
        @include('components.list_story_home', ['hotStories' => $hotStories])

        <div class="row mt-5">
            <div class="col-12 col-md-6">

                @include('components.list_story_new_chapter', [
                    'latestUpdatedStories' => $latestUpdatedStories,
                ])

            </div>
            <div class="col-12 col-md-6" style="border-left: 2px solid var(--primary-color-10);">
                @include('components.hot_stories')
            </div>
        </div>

        @if (isset($tagSections) && $tagSections->isNotEmpty())
            @include('components.list_story_by_tag', ['tagSections' => $tagSections])
        @endif

        @if (isset($categories) && $categories->count() > 0)
            @include('components.list_categories', ['categories' => $categories])
        @endif

        
    </section>
@endsection
