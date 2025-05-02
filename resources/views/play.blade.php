@extends('layouts.master')

@section('title', $video['TITLE'] ?? 'Play Video')

@section('content')
<div class="container mt-5 text-center">
    <h2 class="mb-4 text-white">{{ $video['TITLE'] ?? 'Untitled' }}</h2>

    @if (!empty($video['SOURCE']) && strtolower($video['SOURCE']) !== 'youtube')
        <video width="80%" height="500" controls autoplay>
            <source src="http://3.109.176.31/SeePrime/Content/Videos/{{ $video['SOURCE'] }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @elseif (!empty($video['SOURCE']) && strtolower($video['SOURCE']) === 'youtube')
        <div class="ratio ratio-16x9">
            <iframe src="https://www.youtube.com/embed/{{ $video['THUMBNAIL_PATH'] }}" frameborder="0" allowfullscreen></iframe>
        </div>
    @else
        <p class="text-danger">Video not available</p>
    @endif
</div>
@endsection
