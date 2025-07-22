@extends('layouts.master')
<style>
    .nav-tabs {
        background-color: #000000; /* Pure black background */
    }
    .nav-tabs .nav-item .nav-link {
        color: #ffffff;
        background-color: transparent;
        border: none;
        margin: 0 10px;
        font-weight: 500;
        border-bottom: none; /* Remove underline from inactive tabs */
        transition: all 0.3s ease; /* Smooth transition for hover/active effects */
    }
    .nav-tabs .nav-item .nav-link.active {
        border-bottom: 3px solid white !important; /* Thicker underline for active tab */
        color: #ffffff !important;
        background-color: #222222 !important; /* Subtle dark gray background for active tab */
        border-radius: 0;
    }
    .nav-tabs .nav-item .nav-link:hover {
        color: #cccccc; /* Lighter white on hover */
        background-color: #1a1a1a; /* Darker gray on hover */
        border-bottom: none; /* No underline on hover */
    }
    .genre-icon, .category-icon {
        margin-right: 5px; /* Space between icon and text */
    }

    #backButton {
    z-index: 99999 !important;
    pointer-events: auto !important;
    position: absolute;
    top: 90px;
    left: 30px;
    font-size: 4rem;
    padding: 10px 16px;
    border-radius: 50%;
    color: white;
}
.plyr__video-wrapper {
    position: relative !important;
    z-index: 1 !important;
}

.video-player-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9; /* Or use 4 / 3 or dynamic via JS if needed */
    background-color: black;
    overflow: visible;
}

.video-player-wrapper video {
    width: 100%;
    height: 100%;
    object-fit: cover; /* You can change to 'contain' if needed */
}

</style>

@section('title', $video['TITLE'] ?? 'Play Video')

@section('content')
{{-- <div class="container-fluid px-3 px-md-5 pt-4"> --}}
<div class="container-fluid px-3 px-md-15 pt-0">

    <!-- === VIDEO PLAYER === -->
    @php
        $sourceUrl =  seeprime_url("Content/Videos/{$video['SOURCE']}");
        // $thumbUrl = seeprime_url("Content/Images/Thumbnails/" . ($video['THUMBNAIL_PATH'] ?? 'default.jpg'));
        $bannerPath = $video['BANNER_PATH'] ?? null;
        $thumbUrl = $bannerPath ? seeprime_url("Content/Images/Banners/{$bannerPath}") : asset('images/default.jpg'); 
        $ext = strtolower(pathinfo($video['SOURCE'], PATHINFO_EXTENSION));
        $mimeType = match($ext) {
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'mkv' => 'video/x-matroska',
            default => 'video/mp4'
        };
    @endphp

    {{-- <div class="video-player mb-4"> --}}
        <div id="videoWrapper" class="video-player mb-4 position-relative" style="position: relative;">
        @if (!empty($video['SOURCE_TYPE']) && $video['SOURCE_TYPE'] === 'video')

     <!-- ✅ Back Arrow -->
<a id="backButton" href="javascript:history.back()">
    <i class="fa-solid fa-arrow-left-long"></i>
</a>
        
            <video 
                id="player" 
                playsinline 
                controls 
                poster= "{{$thumbUrl}}"
                class="w-100" 
                style="max-height: auto; object-fit: contain; background-color: black"
                >
                <source 
                    src="{{ $sourceUrl }}" 
                    type="{{ $mimeType }}" 
                />
                Your browser does not support this video format.
            </video>
            


            @if (($video['EXT'] ?? null) === 'mkv')
            <div class="alert alert-warning mt-3">
                <strong>Note:</strong> This video is in `.mkv` format. Some browsers may not support it.
            </div>
            @endif

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
    {{-- Add/Remove My List --}}
        @if (session()->has('user_id'))
            @if ($inMyList)
                <form method="POST" action="{{ route('mylist.remove') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="content_id" value="{{ $video['CONTENT_ID'] }}">
                    <button class="btn btn-danger btn-sm">
                        <i class="fas fa-minus-circle me-2"></i> Remove from My List
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('mylist.add') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="content_id" value="{{ $video['CONTENT_ID'] }}">
                    <button class="btn btn-outline-light btn-sm">
                        <i class="fas fa-plus-circle me-2"></i> Add to My List
                    </button>
                </form>
            @endif
        @endif

    <!-- === CENTERED TABS HEADER === -->
    <ul class="nav nav-tabs justify-content-center border-0 mb-4" id="videoTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link text-white fw-semibold border-0 active" id="episodes-tab" data-bs-toggle="tab" data-bs-target="#episodes" type="button" role="tab" aria-controls="episodes" aria-selected="true">Episodes</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-white fw-semibold border-0" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="false">Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-white fw-semibold border-0" id="related-tab" data-bs-toggle="tab" data-bs-target="#related" type="button" role="tab" aria-controls="related" aria-selected="false">Related</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-white fw-semibold border-0" id="trailer-tab" data-bs-toggle="tab" data-bs-target="#trailer" type="button" role="tab" aria-controls="trailer" aria-selected="false">Trailer & Teaser</button>
        </li>
    </ul>

    <div class="tab-content p-3 text-white rounded-bottom" id="videoTabContent">
        
        <!-- Episodes Tab FIRST (Active) -->
        <div class="tab-pane fade show active" id="episodes" role="tabpanel">
            @livewire('season-selector', [
                'seasons' => array_keys($episodesBySeason->toArray()),
                'selectedSeason' => $selectedSeason,
                'contentId' => $video['CONTENT_ID'],
                'episodesBySeason' => $episodesBySeason
            ])
        </div>

        <!-- Details Tab -->
        <div class="tab-pane fade" id="details" role="tabpanel">
            <h5 class="fw-bold mb-3">More Info</h5>

            @php
                // Define category icons (extendable in controller if needed)
                $categoryIcons = [
                    'Uncategorized' => 'fa-folder',
                    'Movies' => 'fa-film',
                    'default' => 'fa-folder', // Fallback icon
                ];
                $categoryIcon = $categoryIcons[$video['CATEGORY_NAME'] ?? 'Uncategorized'] ?? $categoryIcons['default'];
                
                // Split genre names and map to icons from $genreIcons array
                $genreNames = explode(', ', trim($video['GENRE_NAME'] ?? 'Unknown'));
            @endphp

            <p><strong>Title</strong><br>{{ $video['TITLE'] ?? 'Untitled' }}</p>
            <p><strong>Description</strong><br>{{ $video['DESCRIPTION'] ?? 'No description available' }}</p>
            <p><strong>Genre</strong><br>
    @foreach ($genreNames as $index => $genre)
        @php
            $normalizedGenre = ucwords(strtolower(trim($genre)));
            $genreIcon = $genreIcons[$normalizedGenre] ?? 'fa-question';
        @endphp
        <i class="fas {{ $genreIcon }} genre-icon"></i>
        {{ $normalizedGenre }}
        @if ($index < count($genreNames) - 1), @endif
    @endforeach
</p>

            <p><strong>Category</strong><br>
                <i class="fas {{ $categoryIcon }} category-icon"></i>
                {{ $video['CATEGORY_NAME'] ?? 'Uncategorized' }}
            </p>
            <p><strong>Rating</strong><br>
                @if ($video['REVIEW_STARS'])
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $video['REVIEW_STARS'] ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    ({{ $video['REVIEW_STARS'] }} / 5)
                @else
                    Not rated
                @endif
            </p>
            <p><strong>Age Rating</strong><br>{{ $video['AGE_RATING'] ?? 'N/A' }}</p>
            <p><strong>Source</strong><br>{{ $video['SOURCE'] ? ($video['SOURCE_TYPE'] === 'youtube' ? 'YouTube' : 'Local Video') : 'N/A' }}</p>
            <p><strong>Audio Languages</strong><br>{{ $video['AUDIO_LANGUAGES'] ?? 'N/A' }}</p>
            <p><strong>Subtitles</strong><br>{{ $video['SUBTITLES'] ?? 'N/A' }}</p>
            <p><strong>Directors</strong><br>{!! $video['DIRECTORS'] ?? 'N/A' !!}</p>
            <p><strong>Producers</strong><br>{!! $video['PRODUCERS'] ?? 'N/A' !!}</p>
            <p><strong>Starring</strong><br>
    @if (!empty($casts) && is_array($casts))
        @foreach ($casts as $index => $cast)
            @php
                $name = $cast['CAST_NAME'] ?? 'Unknown';
                $role = $cast['CAST_ROLE'] ?? $cast['ROLE'] ?? null;
            @endphp
            <strong>{{ $name }}</strong>@if ($role) ({{ ucfirst(strtolower($role)) }})@endif
            @if ($index < count($casts) - 1), @endif
        @endforeach
    @else
        N/A
    @endif
</p>

            <p><strong>Studio</strong><br>{{ $video['STUDIO'] ?? 'N/A' }}</p>
        </div>

        <!-- Related Tab -->
<div class="tab-pane fade" id="related" role="tabpanel">
    <div class="row">
        @forelse ($related as $item)
            @php
                $thumb = trim($item['THUMBNAIL_PATH'] ?? '');
                $isComingSoon = ($item['STATE'] ?? '') === 'COMING SOON';

                // Build thumbnail URL
                if (!empty($thumb) && preg_match('/\.(jpg|jpeg|png|webp)$/i', $thumb)) {
                    $thumbUrl = seeprime_url("Content/Images/Thumbnails/{$thumb}");
                } else {
                    $thumbUrl = asset('images/default.jpg');
                }
            @endphp

            <div class="col-md-3 mb-4">
                <a href="{{ $isComingSoon ? 'javascript:void(0);' : route('play.video', ['id' => $item['CONTENT_ID']]) }}"
                   @if($isComingSoon) onclick="alert('This content is not yet available.')" style="pointer-events: none;" @endif>
                    <div class="thumbnail-wrapper position-relative">
                        <img src="{{ $thumbUrl }}" class="rounded w-100" alt="{{ $item['TITLE'] }}">
                        @if (($item['IS_PREMIUM'] ?? '') === 'Y')
                            <span class="premium-badge position-absolute top-0 start-0">Prime</span>
                        @endif
                        @if (!empty($item['STATE']))
                            <span class="premium-badge position-absolute top-0 start-0" style="top: 30px;">
                                {{ strtoupper($item['STATE']) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-white small mt-2 text-center fw-bold">{{ $item['TITLE'] }}</p>
                </a>
            </div>
        @empty
            <p class="text-light">No related content found.</p>
        @endforelse
    </div>
</div>


<!-- Trailer & Teaser Tab -->
<div class="tab-pane fade" id="trailer" role="tabpanel">
    <div class="row">
        @foreach (['TRAILER' => 'Trailer', 'TEASER' => 'Teaser'] as $key => $label)
    @php
        $file = $video[$key] ?? null;
        $typeKey = $key . '_TYPE';
        $ytKey = $key . '_YOUTUBE_ID';

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $type = match($ext) {
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'mkv' => 'video/x-matroska',
            default => 'video/mp4'
        };

        // Correct folder names
        $folder = $key === 'TRAILER' ? 'Trailers' : 'Teasers';
    @endphp

    @if ($file)
        <div class="col-md-6 mb-4">
            <h5 class="fw-bold">{{ $label }}</h5>
               @if ($video[$typeKey] === 'youtube' && !empty($video[$ytKey])) 
                <div class="plyr__video-embed trailer-teaser-player ratio ratio-16x9" id="yt_{{ strtolower($key) }}">
        <iframe
            src="https://www.youtube.com/embed/{{ $video[$ytKey] }}?origin={{ request()->getHost() }}&iv_load_policy=3&modestbranding=1&rel=0"
            allowfullscreen
            allow="autoplay"
            allowtransparency
        ></iframe>
    </div>
@else
            <video 
            id="player_{{ strtolower($key) }}"
            playsinline
            controls
            poster="{{$thumbUrl ?? asset('images/default.jpg')}}"
            class="w-100 rounded shadow"
            style="max-height: 300px; object-fit: contain; background-color: black">
            <source src="{{seeprime_url("Content/Videos/{$folder}/{$file}")}}" type="{{$type}}">
                Your Browser does not support this video format
            </video>
            @endif
        </div>
    @endif
@endforeach
{{-- <pre style="color:white">
{{ var_export([$video['TRAILER_TYPE'] ?? '', $video['TRAILER_YOUTUBE_ID'] ?? '', $video['TEASER_TYPE'] ?? '', $video['TEASER_YOUTUBE_ID'] ?? ''], true) }}
</pre> --}}

    </div>
</div>



    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('#videoTab .nav-link');
    tabs.forEach(tab => tab.addEventListener('click', function () {
        tabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
    }));

    const apiUrl  = '/api/save-progress';
    const videoId = @json($video['CONTENT_ID']);
    const csrfToken = '{{ csrf_token() }}'; // ✅ Generate token

    async function beacon(payload) {
        try {
            await fetch('/save-progress', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify(payload)
});
        } catch (err) {
            console.error("Failed to save progress:", err);
        }
    }

    /* === LOCAL VIDEO === */
    const tag = document.getElementById('player');
    if (tag && tag.nodeName.toLowerCase() === 'video') {
        const plyr     = new Plyr(tag);
        const resumeAt = {{ intval($watched ?? 0) }};

        plyr.once('canplay', () => {
            if (resumeAt > 5 && resumeAt < plyr.duration - 5) {
                plyr.currentTime = resumeAt;
            }
        });

        let lastSent = 0;
        function maybeSend() {
            const now = Math.floor(plyr.currentTime);
            if (now - lastSent < 5) return;
            lastSent = now;

            beacon({
                video_id : videoId,
                watched  : now,
                duration : Math.floor(plyr.duration || 0)
            });
        }

        plyr.on('timeupdate', maybeSend);
        plyr.on('pause',      maybeSend);
        window.addEventListener('beforeunload', maybeSend);
    }

    /* === YOUTUBE IFRAME === */
    if (tag && tag.classList.contains('plyr__video-embed')) {
        const iframe   = tag.querySelector('iframe');
        const resumeAt = {{ intval($watched ?? 0) }};

        let lastSentYT = 0;
        function sendYT(player) {
            const cur = Math.floor(player.getCurrentTime());
            if (cur - lastSentYT < 5) return;
            lastSentYT = cur;

            beacon({
                video_id : videoId,
                watched  : cur,
                duration : Math.floor(player.getDuration() || 0)
            });
        }

        const yt = new YT.Player(iframe, {
            events: {
                onReady: e => { if (resumeAt > 5) e.target.seekTo(resumeAt); },
                onStateChange: e => {
                    if (e.data === YT.PlayerState.PAUSED) sendYT(e.target);
                }
            }
        });

        setInterval(() => sendYT(yt), 5000);
        window.addEventListener('beforeunload', () => sendYT(yt));
    }
});
document.addEventListener("DOMContentLoaded",function(){
    const video = document.querySelector('video');
    const header = document.getElementById('mainHeader');

    if (video && header){
        video.addEventListener('play',()=>{
            header.style.display = 'none';
        });

        video.addEventListener('pause',()=>{
            header.style.display = 'block';
        });

        video.addEventListener('ended',()=>{
            header.style.display = 'block';
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const tag = document.getElementById('player');
    const backButton = document.getElementById('backButton');

    if (tag && tag.nodeName.toLowerCase() === 'video') {
        const plyr = new Plyr(tag, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'settings', 'fullscreen',],
        });

        // Force show back button when Plyr is ready
        backButton.style.display = 'block';

        plyr.on('play', () => {
            backButton.style.display = 'none';
        });

        plyr.on('pause', () => {
            backButton.style.display = 'block';
        });

        // Also show back button after player is fully ready
        plyr.once('canplay', () => {
            backButton.style.display = 'block';
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    ['trailer', 'teaser'].forEach(id => {
        const tag = document.getElementById(`player_${id}`);
        if (tag && tag.nodeName.toLowerCase() === 'video') {
            new Plyr(tag, {
                controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'settings', 'fullscreen',]
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', ()=> {
    const ytPlayers = document.querySelectorAll('.trailer-teaser-player iframe');
    ytPlayers.forEach((iframe,index) =>{
        new YT.Player(iframe,{
            events:{
                onReady: (e) =>{

                }
            }
        });
    });
});

</script>
@endpush


