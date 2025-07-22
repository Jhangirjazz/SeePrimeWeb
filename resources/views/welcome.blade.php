@extends('layouts.master')

<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

@section('title','Home Page')

@section('content')

<!-- Filter Dropdown -->
<div class="px-4 pt-5 mt-5">
    <div class="dropdown">
        <button class="btn btn-danger dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Filter
        </button>
        <ul class="dropdown-menu" aria-labelledby="filterDropdown" id="filterMenu">
    <li>
        <a class="dropdown-item" href="#" onclick="handleFilterClick(event, 'category')">Categories</a>
    </li>
    
</ul>

        
    </div>
</div>

{{-- Category Filters --}}
<div id="filters-section" style="display: none;">
    <p class="text-danger fw-bold px-4 mt-4 mb-2" style="font-size: 18px;">Catergories</p>
    {{-- Category Filters --}}
    <div id="category-filters" class="d-flex flex-wrap gap-2 px-4 py-3" style="display: none;">
        @foreach ($categories as $cat)
            <button type="button"
                class="btn btn-outline-danger btn-sm filter-btn category-btn"
                data-id="{{ $cat['CATEGORY_ID'] }}">
                {{ $cat['NAME'] }}
            </button>
        @endforeach
    </div>
    <p class="text-danger fw-bold px-4 mt-4 mb-2" style="font-size: 18px;">Genres</p>
    {{-- Genre Filters --}}
    <div id="genre-filters" class="d-flex flex-wrap gap-2 px-4 py-3" style="display: none;">
        @foreach ($genres as $genre)
            <button type="button"
                class="btn btn-outline-light btn-sm filter-btn genre-btn"
                data-id="{{ $genre['GENRE_ID'] }}">
                {{ $genre['NAME'] }}
            </button>
        @endforeach
    </div>

    {{-- Search Button --}}
    <div class="px-4 pb-4">
        <button id="applyFilters" class="btn btn-danger">Search</button>
        <button id="selectAllFilters" class="btn btn-danger">Select All</button>
    </div>
    
</div>

{{-- Hero Banner (Recently Added) --}}
@if (!empty($latest))
<div class="scrollable-hero-banner">
    <div class="hero-banner-scroll-inner">
        @foreach (collect($latest)->take(6) as $item)
    @if (is_array($item) && isset($item['CONTENT_ID']) )
        @php
            $thumb = $item['BANNER_PATH'] ?? null;
            $title = $item['TITLE'] ?? 'Untitled';
            $shortDesc = $item['DESCRIPTION'] ?? 'No description available.';
            $genresText = $item['GENRES'] ?? '';
            $contentId = $item['CONTENT_ID'];
            $source = strtolower($item['SOURCE'] ?? $item['SOURCE_PATH'] ?? '');

            if ($thumb && preg_match('/\.(jpg|jpeg|png|webp)$/i', $thumb)) {
                $thumbUrl = seeprime_url("Content/Images/Banners/{$thumb}");
            } else {
                $thumbUrl = asset('images/default.jpg');
            }

            $detailApi = seeprime_url("apis/select.php") . "?select_id=content_detail&content_id={$contentId}&admin_portal=Y";
            $detailJson = @file_get_contents($detailApi);
            $detailArray = $detailJson ? json_decode($detailJson, true) : [];
            $detail = $detailArray[0] ?? [];

            $longDesc = $detail['DESCRIPTION'] ?? $shortDesc;
            $videoPath = $detail['SOURCE_PATH'] ?? null;
            $trailerUrl = $videoPath ? seeprime_url("Content/Videos/{$videoPath}") : '#';
        @endphp

        <div class="hero-banner-single" style="
            background: linear-gradient(to right, rgba(0,0,0,0.85) 30%, rgba(0,0,0,0.3) 100%),
            url('{{ $thumbUrl }}') no-repeat center center;
            background-size: cover;">
            <div class="banner-content d-flex justify-content-between align-items-center">
                <div class="left-banner-text">
                    <h1 class="featured-title">{{ strtoupper($title) }}</h1>
                    <p class="lead">{{ $longDesc }}</p>
                    @if (!empty($item['AGE_RATING']))
                        <p class="text-light mb-1"><strong>Age Rating:</strong> {{ $item['AGE_RATING'] }}</p>
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
                    @if ($genresText)
                        <p class="text-light"><strong>Genres:</strong> {{ $genresText }}</p>
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
        </div>
    @endif
@endforeach

        {{-- @foreach (collect($latest)->take(6) as $item)
        {{-- @if (is_array($item) && isset($item['CONTENT_ID']) )
            @php
                $thumb = $item['BANNER_PATH'] ?? null;
                $title = $item['TITLE'] ?? 'Untitled';
                $shortDesc = $item['DESCRIPTION'] ?? 'No description available.';
                $genresText = $item['GENRES'] ?? '';
                $contentId = $item['CONTENT_ID'];
                $source = strtolower($item['SOURCE'] ?? $item['SOURCE_PATH'] ?? '');              

                if ($thumb && preg_match('/\.(jpg|jpeg|png|webp)$/i', $thumb)) {
                $thumbUrl = seeprime_url("Content/Images/Banners/{$thumb}");
                }
                 else {
                    $thumbUrl = asset('images/default.jpg');
                    }


                $detailApi = seeprime_url("apis/select.php") . "?select_id=content_detail&content_id={$contentId}&admin_portal=Y";
                $detailJson = @file_get_contents($detailApi);
                $detailArray = $detailJson ? json_decode($detailJson, true) : [];
                $detail = $detailArray[0] ?? [];

                $longDesc = $detail['DESCRIPTION'] ?? $shortDesc;
                $videoPath = $detail['SOURCE_PATH'] ?? null;
                $trailerUrl = $videoPath ? seeprime_url("Content/Videos/{$videoPath}") : '#';
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
                            <p class="text-light mb-1"><strong>Age Rating:</strong> {{ $item['AGE_RATING'] }}</p>
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
                        @if ($genresText)
                            <p class="text-light"><strong>Genres:</strong> {{ $genresText }}</p>
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
            </div>
            @endif
        @endforeach --}} 
        @endif
    </div>
</div>
{{-- @endif --}}


{{-- Continue Watching --------------------------------------------------}}
@if ($continue->count())
<section class="mb-4 px-3">
    <h4 class="text-danger mb-3 fw-bold">Continue Watching</h4>
    <div class="scroll-container continue-watching-slider">
    @foreach ($continue as $c)
        <div class="me-3 continue-card">
                <a href="{{ route('play.video', ['id' => $c->video_id, 'resume' => $c->watched]) }}">
                <div class="thumbnail-wrapper position-relative rounded shadow-sm" style="overflow:hidden;">
                       <img src="{{ $c->thumb ?: asset('images/default.jpg') }}"
                         class="rounded w-100"
                         alt="{{ $c->title }}"
                         style="height: 150px; object-fit: cover;">

                    <div class="resume-badge position-absolute top-0 end-0 px-2 py-1 text-white small fw-bold">
                         <i class="fas fa-play"></i>
                    </div>

                    <div class="video-progress-bar">
                        <div class="video-progress-fill"
                             style="width: {{ $c->percent }}%"></div>
                    </div>
                </div>
            <p class="text-center small mt-1">
            <span class="text-white">{{ $c->title }}</span>
            <br>
            <span class="d-inline-block text-light mb-1" style="font-size: 13px;">Resume</span><br>
            <span class="badge bg-danger text-white px-2 py-1">
                
                <i class="fas fa-clock me-1"></i> {{ gmdate('i:s', $c->watched) }}
            </span>
        </p>
            </a>
        </div>
    @endforeach
</div>


</section>
@endif



{{-- Category Sliders --}}
<div class="container-fluid mt-0 px-0" style="background-color: #000;">
    @foreach ($grouped as $category => $items)
    @php
    $routeMap = [
        'movies' => 'movies',
        'shows' => 'shows',
        'documentaries' => 'documentaries',
        'webseries' => 'webseries',
];

$routeName = strtolower($category);
$routeUrl = isset($routeMap[$routeName]) ? url('/'. $routeMap[$routeName]): '#';


@endphp
    <div class="d-flex align-items-center mb-2 px-4">
    <h5 class="text-danger fw-bold m-0">{{ ucwords($category) }}</h5>
    @if($routeUrl !== '#')
        <a href="{{ $routeUrl }}" class="text-white small fw-bold text-decoration-none ms-3">
            See More <i class="fas fa-chevron-right ms-1"></i>
        </a>
    @endif
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
                        }
                    @endphp
            @if ($thumbUrl)
                <div class="content-card me-3">
    @php
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

    <a href="{{ $href }}" {!! $extra !!}>
        <div class="thumbnail-wrapper position-relative">
            <img src="{{ $thumbUrl }}" class="rounded w-100" alt="{{ $title }}">

            {{-- Prime Badge --}}
            @if (!empty($item['IS_PREMIUM']) && $item['IS_PREMIUM'] === 'Y')
                <span class="premium-badge position-absolute top-0 start-0">Prime</span>
            @endif

            {{-- State Badge (Coming Soon / Leaving Soon) --}}
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
    </div>
    @endforeach

    

    {{-- Latest Content Section --}}
@if (!empty($latest))
<div class="container px-3 pt-3">
    <h4 class="text-danger mb-3">Recently Added</h4>
    <div class="scroll-container">
        @foreach ($latest as $item)
            @if (is_array($item) && isset($item['CONTENT_ID']))
                @php
                    $thumb = $item['THUMBNAIL_PATH'] ?? null;
                    $source = strtolower($item['SOURCE'] ?? $item['SOURCE_PATH'] ?? '');
                    $thumbUrl = asset('images/default.jpg');

                    if ($thumb && preg_match('/\.(jpg|png)$/i', $thumb)) {
                        $thumbUrl = seeprime_url("Content/Images/Thumbnails/{$thumb}");
                    } elseif ($source === 'youtube' && $thumb) {
                        $thumbUrl = "https://img.youtube.com/vi/{$thumb}/maxresdefault.jpg";
                    }
                @endphp

                <div class="content-card">
                    <a href="{{ route('play.video', ['id' => $item['CONTENT_ID']]) }}">
                        <div class="thumbnail-wrapper">
                            <img src="{{ $thumbUrl }}" alt="{{ $item['TITLE'] ?? 'Untitled' }}" class="rounded w-100">
                        </div>
                        <p class="text-white text-center mt-2 small fw-bold">{{ $item['TITLE'] ?? 'Untitled' }}</p>
                    </a>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endif




{{--Top 10 Section--}}
@php
    $bannerImage = $top10[2]['thumbnail_url'] ?? asset('images/default.jpg');
@endphp
            <div class="trending-banner position-relative text-white py-5"
     style="background: linear-gradient(to right, rgba(0,0,0,0.85), rgba(0,0,0,0.4)),
            url('{{ $bannerImage }}') center center / cover no-repeat;">
    <div class="banner-content px-5 mb-4">
       <h1 class="featured-title display-5 fw-bold">Top 10 on SeePrime</h1>
       <p class="lead">Most watched this week by viewers like you.</p>
    </div>
    <div id="top10-scroll-inner" class="px-5 d-flex align-items-end overflow-auto pb-4">
       @foreach ($top10 as $item)
       <div class="top10-card">
            <span class="rank-number">{{ $item['rank'] }}</span>
            {{-- <a href="{{ route('play.video', ['id' => $item['content_id']]) }}"> --}}
                @if (!empty($item['content_id']))
    <a href="{{ route('play.video', ['id' => $item['content_id']]) }}">
        <img src="{{ $item['thumbnail_url'] }}" class="img-fluid rounded shadow" alt="{{ $item['title'] }}">
    </a>
@endif
       </div>
       @endforeach
    </div>
    <div class="scroll-button-bar position-absolute end-0" style="top: 50%; transform: translateY(-50%); margin-right: 20px;">
        <button class="scroll-btn btn btn-dark btn-sm me-1" onclick="scrollSlider1(this, -300)">
            <i class="fas fa-chevron-left text-danger"></i>
        </button>
        <button class="scroll-btn btn btn-dark btn-sm" onclick="scrollSlider1(this, 300)">
            <i class="fas fa-chevron-right text-danger"></i>
        </button>
</div>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
  <div id="alertToast" class="toast text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="alertToastMessage">
        <!-- Message goes here -->
      </div>
    </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

{{-- Genre Sliders --}}
    @if (!empty($groupedByGenre))
    <div class="container-fluid mt-0 px-0" style="background-color: #000;">
        @foreach ($groupedByGenre as $genre => $items)
        @if (!empty($genre))
        <div class="category-section mb-2 bg-black py-3 px-2 rounded">
            <div class="d-flex align-items-center mb-2 px-4">
                <h5 class="text-danger fw-bold m-0">{{ ucwords($genre) }}</h5>
            <a href="{{ url('/genre/' . urlencode($genre)) }}" class="text-white small fw-bold text-decoration-none ms-3">
            See More <i class="fas fa-chevron-right ms-1"></i>
    </a>
</div>

            <div class="slider-wrapper position-relative w-100">
                <div class="scroll-container">
                    @foreach ($items as $item )
                        <div class="content-card me-3">
    <a href="{{ route('play.video', ['id' => $item['CONTENT_ID']]) }}">
        <div class="thumbnail-wrapper">
            @php
                $thumb = $item['THUMBNAIL_PATH'] ?? null;
                $source = strtolower($item['SOURCE'] ?? $item ['SOURCE_PATH'] ?? '' );
                $thumbUrl = asset('images/default.jpg');

                if ($thumb && preg_match('/\.(jpg|png)$/i', $thumb)){
                    $thumbUrl = seeprime_url("Content/Images/Thumbnails/{$thumb}");                    
                }
                elseif ($source === 'youtube' && $thumb) {
                    if (str_contains($thumb,'youtube')) {
                        parse_str(parse_url($thumb,PHP_URL_QUERY),$ytparams);
                        $thumb = $ytparams['v'] ?? $thumb;
                    }
                    $thumbUrl = "https://img.youtube.com/vi/{$thumb}/maxresdefault.jpg";
                }
            @endphp

    <img src="{{ $thumbUrl }}"
     alt="{{ $item['TITLE'] }}"
     class="rounded w-100">
        </div>
        <p class="text-white text-center mt-2 small fw-bold">{{ $item['TITLE'] }}</p>
    </a>
</div>

                    @endforeach
                </div>
                <div class="scroll-controls">
                    <button class="scroll-btn scroll-left" onclick="scrollSlider(this,-300)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="scroll-btn scroll-right" onclick="scrollSlider(this,300)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>            
        @endif            
        @endforeach
    </div>       
    @endif

    

@endsection

@push('scripts')
<script>
let selectedCategories = [];
let selectedGenres = [];
let activeFilter = null;

function toggleFilters(type) {
    const filtersSection = document.getElementById('filters-section');
    const category = document.getElementById('category-filters');
    const genre = document.getElementById('genre-filters');

    if (filtersSection.style.display === 'none') {
        filtersSection.style.display = 'block';
    }

    if (type === 'category') {
        if (category.style.display === 'flex') {
            filtersSection.style.display = 'none';
            category.style.display = 'none';
            genre.style.display = 'none';
            activeFilter = null;
        } else {
            category.style.display = 'flex';
            genre.style.display = 'none';
            activeFilter = 'category';
        }
    } else if (type === 'genre') {
        if (genre.style.display === 'flex') {
            filtersSection.style.display = 'none';
            category.style.display = 'none';
            genre.style.display = 'none';
            activeFilter = null;
        } else {
            genre.style.display = 'flex';
            category.style.display = 'none';
            activeFilter = 'genre';
        }
    }
}

function setupFilterButtons(selector, stateArray) {
    document.querySelectorAll(selector).forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            btn.classList.toggle('active');
            if (btn.classList.contains('active')) {
                if (!stateArray.includes(id)) {
                    stateArray.push(id);
                }
            } else {
                const index = stateArray.indexOf(id);
                if (index !== -1) stateArray.splice(index, 1);
            }
        });
    });
}

function handleFilterClick(event, type) {
    event.preventDefault();
    event.stopPropagation();
    toggleFilters(type);

    const dropdown = document.getElementById('filterDropdown');
    const menu = document.getElementById('filterMenu');
    dropdown.classList.remove('show');
    menu.classList.remove('show');
}

document.addEventListener('DOMContentLoaded', () => {
    setupFilterButtons('.category-btn', selectedCategories);
    setupFilterButtons('.genre-btn', selectedGenres);

    let allSelected = false;

    document.getElementById('selectAllFilters')?.addEventListener('click', () => {
        allSelected = !allSelected;

        document.querySelectorAll('.category-btn').forEach(btn => {
            const id = btn.dataset.id;
            if (allSelected) {
                btn.classList.add('active');
                if (!selectedCategories.includes(id)) {
                    selectedCategories.push(id);
                }
            } else {
                btn.classList.remove('active');
                selectedCategories = [];
            }
        });

        document.querySelectorAll('.genre-btn').forEach(btn => {
            const id = btn.dataset.id;
            if (allSelected) {
                btn.classList.add('active');
                if (!selectedGenres.includes(id)) {
                    selectedGenres.push(id);
                }
            } else {
                btn.classList.remove('active');
                selectedGenres = [];
            }
        });

        const btn = document.getElementById('selectAllFilters');
        if (btn) {
            btn.innerText = allSelected ? 'Deselect All' : 'Select All';
        }
    });

    document.getElementById('applyFilters')?.addEventListener('click', () => {
        const params = new URLSearchParams();
        const selectedCatIds = Array.from(document.querySelectorAll('.category-btn.active')).map(btn => btn.dataset.id);
        const selectedGenreIds = Array.from(document.querySelectorAll('.genre-btn.active')).map(btn => btn.dataset.id);

        selectedCategories = selectedCatIds;
        selectedGenres = selectedGenreIds;

        if (selectedCatIds.length > 0) {
            params.append('categories', selectedCatIds.join(','));
        }
        if (selectedGenreIds.length > 0) {
            params.append('genres', selectedGenreIds.join(','));
        }

        const query = params.toString();
        if (query) {
            window.location.href = `/filter?${query}`;
        } else {
            alert("Please select at least one category or genre.");
        }
    });

    // ✅ Hero Banner Auto Scroll
    const bannerInner = document.querySelector('.hero-banner-scroll-inner');
    const slides = document.querySelectorAll('.hero-banner-single');
   if (!bannerInner || slides.length === 0 ) return ;

   const slideWidth = slides[0].offsetWidth;
   const totalSlides = slides.length;

   const firstClone = slides[0].cloneNode(true);
   bannerInner.appendChild(firstClone);

   let currentIndex = 0;
   let allowTransition = true;

   const moveToSlide = (index,animated = true) => {
    bannerInner.style.transition = animated ? 'transform 0.5s ease-in-out' : 'none';
    bannerInner.style.transform = `translateX(-${index * slideWidth}px)`;
   };

   const autoScroll = () => {
    if(!allowTransition) return

    currentIndex++;
    moveToSlide(currentIndex);
    if(currentIndex === totalSlides){
        allowTransition = false;
    }
   };

const resetToFirst  = () =>{
    bannerInner.style.transition = 'none';
    currentIndex = 0;
    moveToSlide(currentIndex,false);
    allowTransition = true;
};

bannerInner.addEventListener('transitionend', () =>{
    if(currentIndex === totalSlides){
        resetToFirst();
    }
});

   let interval = setInterval(autoScroll, 3000);

    // Pause on interaction
    bannerInner.addEventListener('mouseenter', () => clearInterval(interval));
    bannerInner.addEventListener('mouseleave', () => interval = setInterval(autoScroll, 4000));

    // Optional: touch handling for mobile
    let touchStartX = 0;
    bannerInner.addEventListener('touchstart', e => {
        clearInterval(interval);
        touchStartX = e.touches[0].clientX;
    });
    bannerInner.addEventListener('touchend', e => {
        const touchEndX = e.changedTouches[0].clientX;
        if (touchStartX - touchEndX > 50) {
            autoScroll();
        }
    interval = setInterval(autoScroll, 3000);
    });


});
   
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
