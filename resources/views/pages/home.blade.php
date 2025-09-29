@extends('layouts.app')
@section('content')
    @include('components.banner_home')
    <section class="container-xl">
        @include('components.list_story_home', ['list_story' => $hotStories])

        @if ($newStories->count() > 0)
            @include('components.list_story_new_slide', ['newStories' => $newStories])
        @endif

        @include('components.list_story_new_chapter', [
            'latestUpdatedStories' => $latestUpdatedStories,
        ])

        <div class="row mt-4">
            <div class="col-12 col-md-6">
                @include('components.list_story_view_rating_fl', [
                    'topViewedStories' => $topViewedStories,
                    'ratingStories' => $ratingStories,
                    'topFollowedStories' => $topFollowedStories,
                ])

            </div>
            <div class="col-12 col-md-6">
                @include('components.hot_stories')
            </div>
        </div>

        @if ($completedStories->count() > 0)
            @include('components.list_story_full', ['completedStories' => $completedStories])
        @endif

        @include('components.show_categories')

    </section>
@endsection
