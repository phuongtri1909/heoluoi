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
            <div class="col-12 col-md-6">
                @include('components.hot_stories')
                @include('components.currently_reading', ['currentlyReading' => $currentlyReading])
            </div>
        </div>

        @if ($completedStories->count() > 0)
            @include('components.list_story_full', ['completedStories' => $completedStories])
        @endif

    </section>
@endsection
