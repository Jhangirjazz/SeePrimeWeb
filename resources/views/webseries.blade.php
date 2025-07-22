@extends('layouts.master')

@section('Title', 'Webseries' )


@section('content')

<div class="container-fluid px-0" style="background-color: #000; padding-top: 100px;">
    <div class="px-4 pb-3">
        <h2 class="text-white fw-bold display-5 mb-1">Web Series</h2>
        <p class="text-light mb-4">Browse our Web Series collection —<br> action, comedy, thrillers and more.
Dive into a world of captivating stories, blockbuster hits, and timeless classics from every genre.</p>
    </div>




<div class="container-fluid mt-0 px-0" style="background-color: #000;">
    @foreach ($sections as $genre => $items)
        <div class="d-flex align-items-center mb-2 px-4">
            <h5 class="text-danger fw-bold m-0">{{ ucwords($genre) }}</h5>
        </div>

        <div class="slider-wrapper position-relative w-100">
            <div class="scroll-container">
                @foreach ($items as $item)
                    @php
                        $thumb = $item['THUMBNAIL_PATH'] ?? null;
                        $title = $item['TITLE'] ?? 'Untitled';
                        $source = strtolower($item['SOURCE'] ?? $item['SOURCE_PATH'] ?? '');
                        $thumbUrl = '';
                        if ($thumb && preg_match('/\.(jpg|jpeg|png|webp)$/i', $thumb)) {
                            $thumbUrl = seeprime_url("Content/Images/Thumbnails/{$thumb}");
                        }else {
                                $thumbUrl = asset('images/default.jpg');
                        }

                        $isComingSoon = !empty($item['STATE']) && $item['STATE'] === 'COMING SOON';
                        $href = $isComingSoon
                            ? "javascript:void(0);"
                            : route('play.video', ['id' => $item['CONTENT_ID']]);
                        $extra = $isComingSoon
                            ? 'onclick="alert(\'This content is not yet available.\')" style="cursor: not-allowed;"'
                            : '';

                        $videoId = (string) $item['CONTENT_ID'];
                        $watched = $watchHistory[$videoId] ?? 0;
                        $duration = $watchDurations[$videoId] ?? 1;
                        $progress = round(min(100, ($watched / max($duration, 1)) * 100), 1);
                    @endphp

                    @if ($thumbUrl)
                    <div class="content-card me-3">
                        <a href="{{ $href }}" {!! $extra !!}>
                            <div class="thumbnail-wrapper position-relative">
                                <img src="{{ $thumbUrl }}" class="rounded w-100" alt="{{ $title }}">

                                {{-- Prime Badge --}}
                                @if (!empty($item['IS_PREMIUM']) && $item['IS_PREMIUM'] === 'Y')
                                    <span class="premium-badge position-absolute top-0 start-0">Prime</span>
                                @endif

                                {{-- State Badge --}}
                                @if (!empty($item['STATE']))
                                    <span class="premium-badge position-absolute top-0 start-0" style="top: 30px;">
                                        {{ strtoupper($item['STATE']) }}
                                    </span>
                                @endif

                                {{-- Progress Bar --}}
                                <div class="video-progress-bar">
                                    <div class="video-progress-fill" style="width: {{ $progress }}%;"></div>
                                </div>
                            </div>
                            <p class="text-white small mt-2 text-center fw-bold">{{ $title }}</p>
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
