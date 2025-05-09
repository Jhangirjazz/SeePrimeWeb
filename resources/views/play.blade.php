@extends('layouts.master')

<style>
    .episode-scroll::-webkit-scrollbar {
        height: 8px;
    }

    .episode-scroll::-webkit-scrollbar-thumb {
        background-color: #444;
        border-radius: 4px;
    }

    .episode-card {
        scroll-snap-align: start;
        flex-shrink: 0;
    }

    .nav-tabs .nav-link.active {
        background-color: transparent !important;
        border: none;
        border-bottom: 2px solid #e50914;
        color: #fff !important;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #bbb;
    }

    .nav-tabs .nav-link:hover {
        color: #fff;
    }
</style>

@section('title', $video['TITLE'] ?? 'Play Video')

@section('content')
<div class="container-fluid px-3 px-md-5 pt-4">

    <!-- === VIDEO PLAYER === -->
    <div class="video-player mb-5">
        @if (!empty($video['SOURCE_TYPE']) && $video['SOURCE_TYPE'] === 'video')
    <video 
        id="player" 
        playsinline 
        controls 
        poster="http://15.184.102.5/SeePrime/Content/Images/{{ $video['THUMBNAIL_PATH'] }}" 
        class="w-100" 
        style="max-height: 100%; object-fit: contain; background-color: black"
    >
        <source src="http://15.184.102.5/SeePrime/Content/Videos/{{ $video['SOURCE'] }}" type="video/mp4" />
    </video>
@elseif (!empty($video['SOURCE_TYPE']) && $video['SOURCE_TYPE'] === 'youtube')
    <div class="plyr__video-embed" id="player">
        <iframe
            src="https://www.youtube.com/embed/{{ $video['YOUTUBE_ID'] ?? $video['SOURCE'] }}?origin={{ request()->getHost() }}&iv_load_policy=3&modestbranding=1&rel=0"
            allowfullscreen
            allowtransparency
            allow="autoplay"
        ></iframe>
    </div>
@else
    <p class="text-danger">Video not available</p>
@endif



    </div>
    <pre class="text-white bg-dark p-2">
        Details : {{ $video['SOURCE'] ?? 'N/A' }}
        </pre>

        <!-- === TAB NAVIGATION === -->
    <div class="d-flex justify-content-center">
        <ul class="nav nav-tabs border-0 mb-4" id="videoTabs" role="tablist">
            @if (!empty($episodes) && count($episodes) > 0)
            {{-- @if (!empty($episodes)) --}}
            {{-- @if (!empty($episodes) && count($episodes) > 0 && ($video['PARTWISE'] ?? '') === 'Y') --}}
                <li class="nav-item">
                    <a class="nav-link active text-white" id="episodes-tab" data-bs-toggle="tab" href="#episodes" role="tab">Episodes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" id="related-tab" data-bs-toggle="tab" href="#related" role="tab">Related</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" id="details-tab" data-bs-toggle="tab" href="#details" role="tab">Details</a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link active text-white" id="related-tab" data-bs-toggle="tab" href="#related" role="tab">Related</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" id="details-tab" data-bs-toggle="tab" href="#details" role="tab">Details</a>
                </li>
            @endif
        </ul>
    </div>

    <!-- === TAB CONTENT === -->
    <div class="tab-content text-white">
        @if (!empty($episodes) && count($episodes) > 0)

        <!-- Episodes Tab -->
        <div class="tab-pane fade show active" id="episodes" role="tabpanel">
            <div class="d-flex flex-column gap-3 py-3">
                {{-- @foreach ($episodes as $ep) --}}
                
            </div>
        </div>
        @endif

        <!-- Related Tab -->
        {{-- <div class="tab-pane fade {{ empty($episodes) ? 'show active' : '' }}" id="related" role="tabpanel"> --}}
            <div class="tab-pane fade {{ (empty($episodes) || count($episodes) === 0) ? 'show active' : '' }}" id="related" role="tabpanel">
            <p>Coming soon: Related content...</p>
        </div>

        {{-- <!-- Details Tab -->
        <div class="tab-pane fade" id="details" role="tabpanel">
            @if (!empty($video))
                <p><strong>Title:</strong> {{ $video['TITLE'] ?? 'N/A' }}</p>
                <p><strong>Rating:</strong> {{ $video['AGE_RATING'] ?? 'N/A' }}</p>
                <p><strong>Review Stars:</strong> {{ $video['REVIEW_STARS'] ?? 'N/A' }}</p>
                <p><strong>Description:</strong> {{ $video['DESCRIPTION'] ?? 'No description available.' }}</p>
            @else
                <p class="text-warning">Video details not available.</p>
            @endif
        </div> --}}


        <!-- Details Tab -->
<div class="tab-pane fade" id="details" role="tabpanel">
    @if (!empty($video))
        <p><strong>Title:</strong> {{ $video['TITLE'] ?? 'Untitled' }}</p>
        <p><strong>Rating:</strong> {{ $video['AGE_RATING'] ?? 'Not Rated' }}</p>

        <p><strong>Review Stars:</strong>
            @if (!empty($video['REVIEW_STARS']))
                @for ($i = 1; $i <= floor($video['REVIEW_STARS']); $i++)
                    <i class="fas fa-star text-warning"></i>
                @endfor
                @if (fmod($video['REVIEW_STARS'], 1) >= 0.5)
                    <i class="fas fa-star-half-alt text-warning"></i>
                @endif
                <span>{{ $video['REVIEW_STARS'] }}/5</span>
            @else
                Not Rated
            @endif
        </p>

        <p><strong>Description:</strong> {{ $video['DESCRIPTION'] ?? 'No description available.' }}</p>
    @else
        <p class="text-warning">Video details not available.</p>
    @endif
        </div>

    </div>
</div>
        
        @livewire('season-selector', [
            'seasons' => array_keys($episodesBySeason->toArray()),
            'selectedSeason' => $selectedSeason,
            'contentId' => $video['CONTENT_ID'],
            'episodesBySeason' => $episodesBySeason
        ])
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const player = new Plyr('#player');
    });
</script>
@endpush
