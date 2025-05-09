<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

@extends('layouts.master')

@section('title','Home Page')

@section('content')

@if ($grouped)
<div class="scrollable-hero-banner">
    <div class="hero-banner-scroll-inner">
        @foreach ($grouped->flatten(1) as $item)
            @php
                $thumb = $item['THUMBNAIL_PATH'] ?? null;
                $title = $item['TITLE'] ?? 'Untitled';
                $shortDesc = $item['DESCRIPTION'] ?? 'No description available.';
                $genres = $item['GENRES'] ?? '';
                $contentId = $item['CONTENT_ID'];
                $thumbUrl = $thumb ? "http://15.184.102.5/SeePrime/Content/Images/{$thumb}" : '';

                // Fetch extended content detail from API
                $detailApi = "http://15.184.102.5/SeePrime/APIS/SELECT.php?select_id=content_detail&content_id={$contentId}&admin_portal=Y";
                $detailJson = @file_get_contents($detailApi);
                $detailArray = $detailJson ? json_decode($detailJson, true) : [];
                $detail = $detailArray[0] ?? [];

                $longDesc = $detail['DESCRIPTION'] ?? $shortDesc;
                $videoPath = $detail['SOURCE_PATH'] ?? null;
                $trailerUrl = $videoPath ? "http://15.184.102.5/SeePrime/Content/Videos/{$videoPath}" : '#';
            @endphp

            @if ($thumbUrl)
            <div class="hero-banner-single" style="
                background: linear-gradient(to right, rgba(0,0,0,0.85) 30%, rgba(0,0,0,0.3) 100%),
                url('{{ $thumbUrl }}') no-repeat center center;
                background-size: cover;">
                <div class="banner-content d-flex justify-content-between align-items-center">
                    <div class="left-banner-text">
                        <h1 class="featured-title">{{ strtoupper($title) }}</h1>
                        <p class="lead">{{ $longDesc }}</p>
                    
                        @if (!empty($item['AGE_RATING']))
                            <p class="text-light mb-1"><strong>Rating:</strong> {{ $item['AGE_RATING'] }}</p>
                        @endif
                    
                        @if (!empty($item['REVIEW_STARS']))
                            <p class="text-light mb-2">
                                <strong>Review Stars:</strong>
                                @for ($i = 1; $i <= floor($item['REVIEW_STARS']); $i++)
                                    <i class="fas fa-star text-warning"></i>
                                @endfor
                                @if (fmod($item['REVIEW_STARS'], 1) >= 0.5)
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                @endif
                                <span class="ms-1">{{ $item['REVIEW_STARS'] }}/5</span>
                            </p>
                        @endif
                    
                        @if ($genres)
                        <p class="text-light"><strong>Genres:</strong> {{ $genres }}</p>
                    @endif
                    <a href="{{ route('play.video', ['id' => $contentId]) }}" class="btn btn-danger px-4 py-2 me-3 mt-2">
                        <i class="fas fa-play me-2"></i> Watch Now
                    </a>                    
                </div>
                <div class="right-banner-btn">
                    <a href="{{ $trailerUrl }}" target="_blank" class="btn btn-outline-light px-4 py-2">
                        <i class="fas fa-play-circle me-2"></i> Watch Trailer
                    </a>
                </div>
            </div>
        </div> {{-- <== this closes .hero-banner-single --}}
        @endif {{-- ✅ THIS closes @if ($thumbUrl) --}}
        @endforeach
    </div>
</div>
@endif


<!-- Category Sections -->
<div class="container-fluid mt-0 px-0" style="background-color: #000;">
    @foreach ($grouped as $category => $items)
    <div class="category-section mb-2 bg-black py-3 px-2 rounded">
        <div class="d-flex justify-content-between align-items-center mb-2 px-4">
            <h5 class="text-danger fw-bold m-0">{{ $category ?? 'Untitled Category' }}</h5>
        </div>

        <div class="slider-wrapper position-relative w-100">
            <div class="scroll-container">
                @foreach ($items as $item)
                    @php
                        $thumb = $item['THUMBNAIL_PATH'] ?? null;
                        $title = $item['TITLE'] ?? 'Untitled';
                        $source = strtolower($item['SOURCE'] ?? $item['SOURCE_PATH'] ?? '');
                        $thumbUrl = '';

                        if ($thumb && str_ends_with($thumb, '.jpg')) {
                            $thumbUrl = "http://15.184.102.5/SeePrime/Content/Images/{$thumb}";
                        } elseif ($source === 'youtube' && $thumb) {
                            $thumbUrl = "https://img.youtube.com/vi/{$thumb}/maxresdefault.jpg";
                        }
                    @endphp

                    @if ($thumbUrl)
                    <div class="content-card me-3">
                        <a href="{{ route('play.video', ['id' => $item['CONTENT_ID']]) }}">
                            <img src="{{ $thumbUrl }}" class="rounded" alt="{{ $title }}">
                            <p class="text-white small mt-1 text-center fw-bold">{{ $title }}</p>
                        </a>
                    </div>
                    @endif
                @endforeach
            </div>

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

  <!-- Top 10 Section -->
<div class="trending-banner position-relative text-white py-5"
style="background: linear-gradient(to right, rgba(0,0,0,0.85), rgba(0,0,0,0.4)),
       url('http://15.184.102.5/SeePrime/Content/Images/password.jpg') center center / cover no-repeat;">

<div class="banner-content px-5 mb-4">
   <h1 class="featured-title display-5 fw-bold">Top 10 on SeePrime</h1>
   <p class="lead">Most watched this week by viewers like you.</p>
</div>

<div id="top10-scroll-inner" class="px-5 d-flex align-items-end overflow-auto pb-4">
   @foreach ($top10 as $item)
   <div class="top10-card">
    <span class="rank-number">{{ $item['rank'] }}</span>
    <a href="{{ route('play.video', ['id' => $item['rank']]) }}"> {{-- Temporarily using rank as dummy ID --}}
           <img src="{{ $item['thumbnail_url'] }}" class="img-fluid rounded shadow" alt="{{ $item['title'] }}">
       </a>
   </div>
@endforeach

</div>

<div class="scroll-button-bar position-absolute end-0" style="top: 55%; transform: translateY(-50%); margin-right: 20px;">
    <button class="scroll-btn btn btn-dark btn-sm me-1" onclick="scrollSlider1(this, -300)">
        <i class="fas fa-chevron-left text-danger"></i>
    </button>
    <button class="scroll-btn btn btn-dark btn-sm" onclick="scrollSlider1(this, 300)">
        <i class="fas fa-chevron-right text-danger"></i>
    </button>
</div>

 
</div>

</div>

@endsection

@push('scripts')
<script>
    function scrollSlider(button, offset) {
        const wrapper = button.closest('.slider-wrapper');
        const container = wrapper.querySelector('.scroll-container');
        container.scrollBy({ left: offset, behavior: 'smooth' });
    }

    function scrollSlider1(button, offset) {
    const container = document.getElementById('top10-scroll-inner');
    if (container) {
        container.scrollBy({ left: offset, behavior: 'smooth' });
    }
}

</script>
@endpush
