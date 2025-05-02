<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

@extends('layouts.master')

@section('title','Home Page')

@section('content')

@if ($featured)
<div class="hero-banner text-white" style="
    background: linear-gradient(to right, rgba(0,0,0,0.85) 30%, rgba(0,0,0,0.3) 100%), 
    url('http://3.109.176.31/SeePrime/Content/Images/{{ $featured['THUMBNAIL_PATH'] }}') no-repeat center center;
    background-size: cover;
    height: 75vh;
    display: flex;
    align-items: center;
">
    <div class="container position-relative">
        <div class="col-md-6">
            <h1 class="featured-title">{{ strtoupper($featured['TITLE']) }}</h1>
                    @if (!empty($featured['DESCRIPTION']))
                    <p class="lead mb-4">{{ $featured['DESCRIPTION'] }}</p>
                     @else
                    <p class="lead mb-4 text-danger">No description available.</p>
                    @endif
         
            
            <!-- Left red button -->
            <a href="{{ route('play.video', ['id' => $featured['CONTENT_ID']]) }}" class="btn btn-danger btn-sm px-4 py-2">
                <i class="fas fa-play me-2"></i>Watch Now
            </a>
        </div>

        
        <!-- Right transparent play button -->
            <a href="{{ route('play.video', ['id' => $featured['CONTENT_ID']]) }}" class="trailer-play-btn d-flex align-items-center">
                <span class="circle">
                    <i class="fas fa-play"></i>
                </span>
                <span class="text ms-2">WATCH TRAILER</span>
            </a>

    </div>
</div>
@endif




<!-- Content from API -->
<div class="container-fluid mt-0 px-0 " style="background-color: #000;">
    @foreach ($grouped as $category => $items)
    <div class="category-section mb-2 bg-black py-3 px-2 rounded">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="text-danger m-0">{{ $category ?? 'Untitled Category' }}</h5>
            
        </div>

        <div class="slider-wrapper position-relative w-100">

            <!-- Slider Scroll Area -->
            <div class="scroll-container">
                @foreach ($items as $item)
                @php
                $thumb = $item['THUMBNAIL_PATH'] ?? null;
                $title = $item['TITLE'] ?? 'Untitled';
                $source = strtolower($item['SOURCE'] ?? $item['SOURCE_PATH'] ?? '');
            
                // Final thumbnail URL
                $thumbUrl = '';
            
                if ($thumb && str_ends_with($thumb, '.jpg')) {
                    $thumbUrl = "http://3.109.176.31/SeePrime/Content/Images/{$thumb}";
                } elseif ($source === 'youtube' && $thumb) {
                    $thumbUrl = "https://img.youtube.com/vi/{$thumb}/maxresdefault.jpg";
                }
            @endphp
            
            @if ($thumbUrl)
                <div class="content-card me-3">
                    <a href="{{ route('play.video', ['id' => $item['CONTENT_ID']]) }}">
                        <img src="{{ $thumbUrl }}" class="rounded" alt="{{$title}}"

                             alt="{{ $title }}">
                        <p class="text-white small mt-1 text-center fw-bold">{{ $title }}</p>
                    </a>
                </div>
            @endif
            
                @endforeach
            </div>

            <!-- Arrows stacked vertically at right edge -->
            
            <!-- Arrows stacked vertically at right edge -->
                <div class="scroll-controls">
                    <button class="scroll-btn scroll-left" onclick="scrollSlider(this, -300)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="scroll-btn scroll-right" onclick="scrollSlider(this, 300)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
        </div>
    </div>
@endforeach

</div>

@endsection

@push('scripts')
<script>
    function scrollSlider(button, offset) {
        const wrapper = button.closest('.slider-wrapper');
        const container = wrapper.querySelector('.scroll-container');
        container.scrollBy({ left: offset, behavior: 'smooth' });
    }
</script>
@endpush

