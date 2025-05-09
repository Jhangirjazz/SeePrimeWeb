@extends('layouts.master')

@section('title', 'Search Results')

@section('content')
<div class="container pt-5 mt-5">
    <h4 class="text-white">Search results for: <em>{{ $query }}</em></h4>

    @if($results->count())
    <div class="row mt-4">
        @foreach ($results as $video)
    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
        <div class="card bg-dark text-white border-0 shadow-sm h-100">
            @php
                $thumbUrl = '';

                if (!empty($video['SOURCE']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $video['SOURCE'])) {
                    // YouTube video
                    $thumbUrl = "https://img.youtube.com/vi/{$video['SOURCE']}/maxresdefault.jpg";
                } elseif (!empty($video['THUMBNAIL_PATH'])) {
                    // Local video
                    $thumbUrl = "http://15.184.102.5/SeePrime/Content/Images/{$video['THUMBNAIL_PATH']}";
                } else {
                    $thumbUrl = asset('images/default.jpg');
                }
            @endphp


            <img 
                src="{{ $thumbUrl }}" 
                alt="{{ $video['TITLE'] ?? 'Thumbnail' }}" 
                class="card-img-top" 
                style="object-fit: cover; height: 280px;"
            >

            <div class="card-body d-flex flex-column justify-content-between">
                <h5 class="card-title mb-2">{{ $video['TITLE'] ?? 'Untitled' }}</h5>
                <p class="card-text small text-muted mb-2">{{ \Illuminate\Support\Str::limit($video['DESCRIPTION'] ?? 'No description', 80) }}</p>
                <a href="{{ route('play.video', ['id' => $video['CONTENT_ID']]) }}" class="btn btn-sm btn-danger mt-auto">
                    <i class="bi bi-play-circle"></i> Watch
                </a>
            </div>
        </div>
    </div>
@endforeach

    </div>
    
    @else
        <p class="text-white mt-4">No results found for your search.</p>
    @endif
</div>
@endsection
