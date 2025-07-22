@extends('layouts.master')

@section('title', 'Documentaries')

@section('content')
<div class="container-fluid px-0" style="background-color: #000; padding-top: 100px;">
    <div class="px-4 pb-3">
        <h2 class="text-white fw-bold display-5 mb-1">Documentaries</h2>
        <p class="text-light mb-4">Explore our selection of documentaries — <br>real stories, deeper truths.
From nature and history to social issues and science, uncover thought-provoking films that inform and inspire.</p>
    </div>

    @foreach ($sections as $genre => $items)
    <div class="category-section mb-2 bg-black py-3 px-2 rounded">
        <div class="d-flex justify-content-between align-items-center mb-2 px-4">
            <h5 class="text-danger fw-bold m-0">{{ $genre !== 'Other' ? $genre : 'Uncategorized' }}</h5>
        </div>
        <div class="slider-wrapper position-relative w-100">
            <div class="scroll-container">
                @foreach ($items as $item)
                    @php
                        $thumb = $item['THUMBNAIL_PATH'] ?? null;
                        $title = $item['TITLE'] ?? 'Untitled';
                        $source = strtolower($item['SOURCE'] ?? $item['SOURCE_PATH'] ?? '');
                        $thumbUrl = asset('images/default.jpg');

                        if ($thumb && preg_match('/\.(jpg|jpeg|png|webp)$/i', $thumb)) {
                            $thumbUrl = seeprime_url("Content/Images/Thumbnails/{$thumb}");
                        } elseif ($source === 'youtube' && $thumb) {
                            $thumbUrl = "https://img.youtube.com/vi/{$thumb}/maxresdefault.jpg";
                        }
                    @endphp

                    <div class="content-card me-3">
                        <a href="{{ route('play.video', ['id' => $item['CONTENT_ID']]) }}">
                            <div class="thumbnail-wrapper position-relative">
                                <img src="{{ $thumbUrl }}" class="rounded w-100" alt="{{ $title }}">
                                @if (!empty($item['IS_PREMIUM']) && $item['IS_PREMIUM'] === 'Y')
                                    <span class="premium-badge position-absolute top-0 start-0">Prime</span>
                                @endif
                            </div>
                            <p class="text-white small mt-2 text-center fw-bold">{{ $title }}</p>
                        </a>
                    </div>
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
</div>
@endsection
<script>
    function scrollSlider(button,amount){
        const scrollContainer = button.closest('.slider-wrapper').querySelector('.scroll-container');
        scrollContainer.scrollBy({
            left : amount,
            behavior : 'smooth'
        });
    }
</script>