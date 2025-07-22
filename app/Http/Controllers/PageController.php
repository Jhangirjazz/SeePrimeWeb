<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\MyList;
use App\Models\WatchHistory;

class PageController extends Controller
{

 public function welcome()
{
    $categories = seeprime_api(['select_id'=>'select_categories','admin_portal'=>'N']);
    $genres     = seeprime_api(['select_id'=>'select_genres',     'admin_portal'=>'N']);
    $latest     = seeprime_api(['select_id'=>'select_latest_content','admin_portal'=>'N']);

    $data = seeprime_api([
        'select_id'    => 'select_content',
        'admin_portal' => config('app.debug') ? 'Y' : 'N',
    ]);

    if (!is_array($latest)) {
    $latest = [];
}

$latest = array_filter($latest, function ($item) {
    return is_array($item) && !empty($item['CONTENT_ID']);
});


    /* featured / grouped / top-10 ------------------------------------------------ */
    // $featured = collect($data)->firstWhere('CONTENT_ID', (string)$data[0]['CONTENT_ID'] ?? null);

    $featuredContentId = isset($data[0]['CONTENT_ID']) ? (string)$data[0]['CONTENT_ID'] : null;
    $featured = $featuredContentId ? collect($data)->firstWhere('CONTENT_ID', $featuredContentId) : null;

    $featured = is_array($featured) ? $featured :[];

    $featured['REVIEW_STARS'] = $featured['REVIEW_STARS'] ?? null;
    $featured['AGE_RATING']   = $featured['AGE_RATING']   ?? null;
    $featured['DESCRIPTION']  = $featured['DESCRIPTION']  ?? 'No description available.';

    $grouped = collect($data)->groupBy(fn($i) => strtolower(trim($i['CONTENT_TYPE'] ?? 'unknown')));

    
    $customGenreMap = [
        16 => 'Romance',
        24 => 'Animated' 
    ];

    $groupedByGenres = [];

foreach ($data as $item) {
    $genreIds = explode(',', $item['GENRE_IDS'] ?? '');

    foreach ($genreIds as $id) {
        $id = (int) trim($id);
        if (isset($customGenreMap[$id])) {
            $genreName = $customGenreMap[$id];
            $groupedByGenres[$genreName][] = $item;
        }
    }
}
  

    $top10 = collect($data)->sortByDesc(fn($i)=> (int)($i['VIEWS'] ?? 0))->take(10)->values()->map(function($item,$idx){
       $thumb = $item['THUMBNAIL_PATH'] ?? null;
       $source = strtolower($item['SOURCE'] ?? $item ['SOURCE_PATH'] ?? '' );
       $thumbUrl = asset('images/default.jpg');

        if($thumb && preg_match('/\.(jpg|png)$/i', $thumb)){
            $thumbUrl = seeprime_url("Content/Images/Thumbnails/{$thumb}");
        }
        elseif ($source === 'youtube' && $thumb){
                $thumbUrl = "https://img.youtube.com/vi/{$thumb}/maxresdefault.jpg";
        }

        return[

            'rank' => $idx + 1,
            'title' => $item['TITLE'] ?? 'Untitled',
            'thumbnail_url' => $thumbUrl,
            'views' => !empty($item['VIEWS']) ? $item['VIEWS'] . ' VIEWS' : '',
            'content_id' => $item['CONTENT_ID'] ?? null, 
        ];

    })->toArray();
    $userId = session('user_id');

    $watchHistory   = WatchHistory::where('user_id',$userId)
                      ->pluck('watched','video_id')
                      ->mapWithKeys(fn($v,$k)=>[(string)$k=>$v]);

    $watchDurations = WatchHistory::where('user_id',$userId)
                      ->pluck('duration','video_id')
                      ->mapWithKeys(fn($v,$k)=>[(string)$k=>$v]);

    /* -------------------------------------------------------------
       3) Continue-Watching list
    ------------------------------------------------------------- */
    $contentLookup = collect($data)->keyBy('CONTENT_ID');

    $continue = WatchHistory::where('user_id',$userId)
        ->whereColumn('watched','<',DB::raw('duration * 0.95'))   // <95 % finished
        ->orderByDesc('updated_at')
        ->take(20)
        ->get()
        ->map(function ($row) use ($contentLookup) {

            $meta = $contentLookup[(string)$row->video_id] ?? null;
            if (!$meta) return null;          // skip unknown IDs

            $thumb   = $meta['THUMBNAIL_PATH'] ?? $mets['IMAGE_PATH'] ?? null ;
            if($thumb && preg_match('/\.(jpg|png)$/i', $thumb)){
                $thumUrl = seeprime_url("Content/Images/Thumbnails/{$thumb}");
            }
            $source  = strtolower($meta['SOURCE'] ?? $meta['SOURCE_PATH'] ?? '');
            $thumbUrl = asset('images/default.jpg');

            if ($thumb && preg_match('/\.(jpg|png)$/i',$thumb)) {
                $thumbUrl = seeprime_url("Content/Images/Thumbnails/{$thumb}");
            } elseif ($source === 'youtube' && $thumb) {
                $thumbUrl = "https://img.youtube.com/vi/{$thumb}/maxresdefault.jpg";
            }

            $row->title   = $meta['TITLE'] ?? 'Untitled';
            $row->thumb   = $thumbUrl;
            $row->percent = $row->duration
                          ? round(min(100,$row->watched/max($row->duration,1)*100),1)
                          : 0;

            return $row;
        })
        ->filter()
        ->values();
    return view('welcome',[
        'featured'        => $featured,
        'grouped'         => $grouped,
        'top10'           => $top10,
        'categories'      => $categories,
        'genres'          => $genres,
        'latest'          => $latest,
        'watchHistory'    => $watchHistory,
        'watchDurations'  => $watchDurations,
        'continue'        => $continue,
        'groupedByGenre'  => $groupedByGenres
    ]);
}


    public function shows()
{
    // 1️⃣ Fetch content and genres using the global API helper
    $data   = collect(seeprime_api(['select_id' => 'select_content', 'admin_portal' => 'Y']));
    $genres = collect(seeprime_api(['select_id' => 'select_genres', 'admin_portal' => 'N']));

    // 2️⃣ Filter only "Shows"
    $shows = $data->filter(function ($item) {
        return is_array($item) && ($item['CONTENT_TYPE'] ?? null) === 'Shows';
    });

    // 3️⃣ Group Shows by genre names
    $sections = [];
    foreach ($shows as $item) {
        $genreIds = explode(',', $item['GENRE_IDS'] ?? '');
        $genreNames = collect($genreIds)
            ->map(fn($id) => trim($id))
            ->map(fn($id) => $genres->firstWhere('GENRE_ID', $id)['NAME'] ?? 'Other')
            ->unique();

        foreach ($genreNames as $genreName) {
            $sections[$genreName][] = $item;
        }
    }

    return view('shows', ['sections' => $sections]);
}

   public function movies()
{
    // 1️⃣ Fetch content and genres using the helper
    $data   = collect(seeprime_api(['select_id' => 'select_content', 'admin_portal' => 'Y']));
    $genres = collect(seeprime_api(['select_id' => 'select_genres', 'admin_portal' => 'N']));

    // 2️⃣ Filter only Movies
    $movies = $data->filter(function ($item) {
        return is_array($item) && ($item['CONTENT_TYPE'] === 'Movies');
    });

    // 3️⃣ Group movies by genre names
    $sections = [];
    foreach ($movies as $item) {
        $genreIds = explode(',', $item['GENRE_IDS'] ?? '');
        $genreNames = collect($genreIds)
            ->map(fn($id) => trim($id))
            ->map(fn($id) => $genres->firstWhere('GENRE_ID', $id)['NAME'] ?? 'Other')
            ->unique();

        foreach ($genreNames as $genreName) {
            $sections[$genreName][] = $item;
        }
    }

    return view('movies', ['sections' => $sections]);
}


 public function documentaries()
{
    // 1️⃣ Fetch content and genres using the helper
    $data   = collect(seeprime_api(['select_id' => 'select_content', 'admin_portal' => 'Y']));
    $genres = collect(seeprime_api(['select_id' => 'select_genres', 'admin_portal' => 'N']));

    // 2️⃣ Filter only Documentaries
    $documentaries = $data->filter(function ($item) {
        return is_array($item) && ($item['CONTENT_TYPE'] === 'Documentaries');
    });

    // 3️⃣ Group Documentaries by genre names
    $sections = [];
    foreach ($documentaries as $item) {
        $genreIds = explode(',', $item['GENRE_IDS'] ?? '');
        $genreNames = collect($genreIds)
            ->map(fn($id) => trim($id))
            ->map(fn($id) => $genres->firstWhere('GENRE_ID', $id)['NAME'] ?? 'Other')
            ->unique();

        foreach ($genreNames as $genreName) {
            $sections[$genreName][] = $item;
        }
    }

    return view('documentaries', ['sections' => $sections]);
}

        public function webseries()
        {
            $data = collect(seeprime_api(['select_id'=> 'select_content', 'admin_portal' => 'N']));
            $genres = collect(seeprime_api(['select_id'=> 'select_genres', 'admin_portal' => 'Y']));

            $webSeries = $data->filter(function($item){
                return trim(strtolower($item['CONTENT_TYPE'] ?? '')) === 'web series';
            });

            $sections = [];
            foreach($webSeries as $item){
                $genreIds = explode(',', $item['GENRE_IDS'] ?? '');
                $genreNames = collect($genreIds)
                ->map(fn($id)=> trim($id))
                ->map(fn($id)=> $genres->firstWhere('GENRE_ID',$id)['NAME'] ?? 'other')
                ->unique();

                foreach ($genreNames as $genreName){
                    $sections[$genreName][] = $item;
                }
            }


            return view('webseries',['sections' => $sections]);
        }

    public function new()
    {
        return view('new');
    }

    public function mylist()
{
    $userId = session('user_id');

    // Step 1: Fetch all content_ids for this user from SQLite
    $list = MyList::where('user_id', $userId)->pluck('content_id');

    if ($list->isEmpty()) {
        return view('mylist', ['content' => []]);
    }

    // Step 2: Use the global API helper to fetch content
    $allContent = collect(seeprime_api(['select_id' => 'select_content', 'admin_portal' => 'Y']));

    // Step 3: Filter the content from API based on IDs in list
    $filtered = $allContent->whereIn('CONTENT_ID', $list);

    return view('mylist', ['content' => $filtered]);
}

    
    public function playVideo($id, $partId = null)
{
    $data = seeprime_api([
        'select_id' => 'content_detail',
        'content_id' => $id,
        'admin_portal' => 'Y',
    ]);

    if (!session()->has('user_id')) {
        return redirect('/welcome')->with('toast', 'Please register yourself to access content.');
    }

    if (!empty($data)) {
        $allContent = collect(seeprime_api([
            'select_id' => 'select_content',
            'admin_portal' => 'Y',
        ]));

        $metaContent = $allContent->firstWhere('CONTENT_ID', (string)$id);
        $categoryName = 'Uncategorized';
        $genreName = 'Unknown';

        // Fetch categories and genres
        $allCategories = seeprime_api([
            'select_id' => 'select_categories',
            'admin_portal' => 'N',
        ]);
        $allGenres = seeprime_api([
            'select_id' => 'select_genres',
            'admin_portal' => 'N',
        ]);

        // Category and genre name resolution
        $categoryId = $metaContent['CATEGORY_ID'] ?? null;
        $genreIds = explode(',', $metaContent['GENRE_IDS'] ?? '');
        $matchedCategory = collect($allCategories)->firstWhere('CATEGORY_ID', (string) $categoryId);
        $categoryName = $matchedCategory['NAME'] ?? 'Uncategorized';

        $genreNames = collect($genreIds)
            ->map('trim')
            ->map(fn($id) => collect($allGenres)->firstWhere('GENRE_ID', $id)['NAME'] ?? null)
            ->filter()
            ->unique()
            ->toArray();
        $genreName = implode(', ', $genreNames);

        $related = [];

if (!empty($genreNames)) {
    // You can use the first genre or loop through multiple
    
if (!empty($genreIds)) {
    $genreIdCsv = implode(',', $genreIds);

    $apiRelated = seeprime_api([
        'select_id' => 'select_content',
        'genre_id' => $genreIdCsv,
        'admin_portal' => 'N'
    ]);

    $related = is_array($apiRelated)
        ? collect($apiRelated)
            ->where('CONTENT_ID', '!=', (string)$id)
            ->unique('CONTENT_ID') // optional: avoid duplicates
            ->take(12)
            ->values()
        : collect();
}


}
        if (
            isset($metaContent['IS_PREMIUM']) &&
            $metaContent['IS_PREMIUM'] === 'Y' &&
            session('subscription_state') !== 'PRIME MEMBER'
        ) {
            return redirect('/welcome')->with('toast', 'This video is for Prime Members only.');
        }

        $isMultiPart = is_array($data) && count($data) > 1 && isset($data[0]['CONTENT_DETAIL_ID']);
        $episodes = $isMultiPart ? $data : [];
        $video = [];

        if ($isMultiPart) {
            $meta = $data[0];
            $selected = $partId
                ? collect($data)->firstWhere('CONTENT_DETAIL_ID', $partId)
                : $meta;

            $selectedSeason = $partId
                ? (string)($selected['SEASON'] ?? '1')
                : (string)request()->get('season', '1');

            $video = array_merge($meta, $selected);
            $video['CATEGORY_ID'] = $metaContent['CATEGORY_ID'] ?? null;
            $video['GENRE_IDS'] = $metaContent['GENRE_IDS'] ?? '';
            $video['CATEGORY_NAME'] = $categoryName;
            $video['GENRE_NAME'] = $genreName;
            $video['REVIEW_STARS'] = $video['REVIEW_STARS'] ?? $metaContent['REVIEW_STARS'] ?? null;
            $video['AGE_RATING'] = $video['AGE_RATING'] ?? $metaContent['AGE_RATING'] ?? null;
            $video['TITLE'] = $video['TITLE'] ?? $metaContent['TITLE'] ?? 'Untitled';
            $video['DESCRIPTION'] = $video['DESCRIPTION'] ?? $metaContent['DESCRIPTION'] ?? 'No description available';
            if(!empty($selected['SOURCE_PATH']) &&
                !str_contains($selected['SOURCE_PATH'], 'teaser') &&
                ! str_contains($selected['SOURCE_PATH'], 'trailer') &&
                !in_array($selected['SOURCE_PATH'], [$selected['TRAILER'], $selected['TEASER']])
            ){
                $video['SOURCE'] = $selected['SOURCE_PATH'];
            }else{
                $video['SOURCE'] = '';
            }
            $video['IMAGE_PATH'] = $selected['IMAGE_PATH'] ?? $meta['IMAGE_PATH'] ?? '';
            $video['BANNER_PATH'] = $metaContent['BANNER_PATH'] ?? null;
            // Handle Trailer and Teaser for multipart
            $video['TRAILER'] = $selected['TRAILER'] ?? $meta['TRAILER'] ?? null;
            $video['TEASER']  = $selected['TEASER'] ?? $meta['TEASER'] ?? null;

            $video['TRAILER_EXT'] = strtolower(pathinfo($video['TRAILER'], PATHINFO_EXTENSION));
            $video['TEASER_EXT']  = strtolower(pathinfo($video['TEASER'], PATHINFO_EXTENSION));


            $episodesBySeason = collect($episodes)
                ->groupBy('SEASON')
                ->mapWithKeys(fn($items, $key) => [(string)$key => $items]);
        } else {
            $raw = is_array($data) && isset($data[0]) ? $data[0] : $data;
            $video = is_array($raw) ? $raw : [];
            $video['CATEGORY_ID'] = $metaContent['CATEGORY_ID'] ?? null;
            $video['GENRE_IDS'] = $metaContent['GENRE_IDS'] ?? '';
            $video['CATEGORY_NAME'] = $categoryName;
            $video['GENRE_NAME'] = $genreName;
            $video['REVIEW_STARS'] = $video['REVIEW_STARS'] ?? $metaContent['REVIEW_STARS'] ?? null;
            $video['AGE_RATING'] = $video['AGE_RATING'] ?? $metaContent['AGE_RATING'] ?? null;
            $video['TITLE'] = $video['TITLE'] ?? $metaContent['TITLE'] ?? 'Untitled';
            $video['DESCRIPTION'] = $video['DESCRIPTION'] ?? $metaContent['DESCRIPTION'] ?? 'No description available';
            $video['SOURCE'] = $video['SOURCE_PATH'] ?? $video['SOURCE'] ?? '';
            $video['TRAILER'] = $video['TRAILER'] ?? $raw['TRAILER'] ?? '';
            $video['TEASER'] = $video['TEASER'] ?? $raw['TEASER'] ?? '';

            $video['TRAILER_EXT'] = strtolower(pathinfo($video['TRAILER'], PATHINFO_EXTENSION));
            $video['TEASER_EXT']  = strtolower(pathinfo($video['TEASER'], PATHINFO_EXTENSION)); 
            $video['EXT'] = strtolower(pathinfo($video['SOURCE'], PATHINFO_EXTENSION));
            $video['EXT'] = strtolower(pathinfo($video['SOURCE'], PATHINFO_EXTENSION));
            $video['MIME_TYPE'] = match($video['EXT']) {
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'ogg' => 'video/ogg',
                'mkv' => 'video/x-matroska',
                default => 'video/mp4',
            };


            $video['IMAGE_PATH'] = $video['IMAGE_PATH'] ?? '';
            $video['BANNER_PATH'] = $metaContent['BANNER_PATH'] ?? null;
            $episodesBySeason = collect();
            $selectedSeason = 1;
        }

        if (!empty($video['SOURCE']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $video['SOURCE'])) {
            $video['SOURCE_TYPE'] = 'youtube';
            $video['YOUTUBE_ID'] = $video['SOURCE'];
        } else {
            $video['SOURCE_TYPE'] = 'video';
        }


        if(!empty($video['TRAILER']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $video['TRAILER'])) {
            $video['TRAILER_TYPE'] = 'youtube';
            $video['TRAILER_YOUTUBE_ID'] = $video['TRAILER']; 
        }   
        elseif(preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/))([\w\-]{11})/',$video['TRAILER'],$matches)){
            $video['TRAILER_TYPE'] = 'youtube';
            $video['TRAILER_YOUTUBE_ID'] = $matches[1];
        }
        else{
                $video['TRAILER_TYPE'] ='video';
        }

    if (!empty($video['TEASER']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $video['TEASER'])) {
        $video['TEASER_TYPE'] = 'youtube';
        $video['TEASER_YOUTUBE_ID'] = $video['TEASER'];
    } elseif (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/))([\w\-]{11})/', $video['TEASER'], $matches)) {
        $video['TEASER_TYPE'] = 'youtube';
        $video['TEASER_YOUTUBE_ID'] = $matches[1];
    } else {
        $video['TEASER_TYPE'] = 'video';
}      

        // Cast API (non-standard endpoint, not SELECT.php)
        $castUrl = seeprime_url("apis/select.php")."?select_id=select_content_casts&content_id={$id}&admin_portal=N";
        $castResponse = Http::get($castUrl);
        $casts = $castResponse->successful() ? $castResponse->json() : [];

        $genreIcons = [
            'Action'    => 'fa-bolt',
            'Mystery'   => 'fa-mask',
            'Family'    => 'fa-people-roof',
            'Comedy'    => 'fa-laugh',
            'Drama'     => 'fa-theater-masks',
            'Horror'    => 'fa-ghost',
            'Adventure' => 'fa-hat-wizard',
            'Romance'   => 'fa-heart',
            'Fantasy'   => 'fa-dragon',
            'Sci-Fi'    => 'fa-robot',
            'Music'     => 'fa-music',
            'Thriller'  => 'fa-skull'
        ];

        $inMyList = false;
        if (session()->has('user_id') && isset($video['CONTENT_ID'])) {
    $inMyList = MyList::where('user_id', session('user_id'))
                      ->where('content_id', $video['CONTENT_ID'])
                      ->exists();
}
        try {
        $video['CONTENT_ID'] = $metaContent['CONTENT_ID'] ?? (string)$id;
         $watch = WatchHistory::where('user_id', session('user_id'))
    ->where('content_id', $video['CONTENT_ID'])
    ->first();
    $watched = $watch->watched ?? 0;
    $watched = request()->get('resume', $watched); // ✅ Override if ?resume= passed
    $duration = $watch->duration ?? 1;
    $progressPercent = round(min(100, ($watched / max($duration, 1)) * 100), 1);
}

catch (\Throwable $e){

    Log::error('Watchhistory error in playvideo()',[
        'user_id' => session('user_id'),
        'content_id' => $video['CONTENT_ID'] ?? null,
        'exception' => $e->getMessage()
    ]);
    $watched = 0;
    $progressPercent = 0;
        // return redirect('/welcome')->with('toast','video progress could not be loaded');
}

        return view('play', [
            'video' => $video,
            'episodes' => $episodes,
            'episodesBySeason' => $episodesBySeason,
            'selectedSeason' => $selectedSeason,
            'casts' => $casts,
            'genres' => $allGenres,
            'genreIcons' => $genreIcons,
            'seasons' => array_keys($episodesBySeason->toArray()),
            'inMyList' => $inMyList,
            'progressPercent' => $progressPercent,
            'watched' => $watched,
            'related' => $related

        ]);

       
    }

    return abort(500, 'Failed to load video details');
}


 public function search(Request $request)
{
    $query = trim($request->input('query'));

    // ✅ Use the global API helper
    $data = collect(seeprime_api([
        'select_id' => 'select_content',
        'admin_portal' => 'Y'
    ]));

    // 🔍 Filter by title or description
    $results = $data->filter(function ($item) use ($query) {
        return str_contains(strtolower($item['TITLE'] ?? ''), strtolower($query)) ||
               str_contains(strtolower($item['DESCRIPTION'] ?? ''), strtolower($query)) ||
               str_contains(strtolower($item['CONTENT_TYPE'] ?? ''), strtolower($query)) ||
               str_contains(strtolower($item['GENERE_DESCRIPTIONS']?? ''), strtolower($query))||
               str_contains(strtolower($item['CAST_DESCRIPTIONS']?? '') , strtolower($query));
    });

    if($results->isEmpty()){
        $results = $data->shuffle()->take(12);
    }

    return view('search-results', [
        'results' => $results,
        'query'   => $query,
    ]);
}


public function filterByCategory($id)
{
    $data = seeprime_api([
        'select_id' => 'select_content',
        'category_id' => $id,
        'admin_portal' => 'Y'
    ]);

    // ✅ Make sure $data is a list, not an error message
    if (!isset($data[0]['CONTENT_ID'])) {
        $data = []; // force it to be an empty list
    }

    return view('filtered', [
        'content' => $data,
        'filterType' => 'Category'
    ]);
}

public function filterByGenre($id)
{
    $data = seeprime_api([
        'select_id' => 'select_content',
        'genre_id' => $id,
        'admin_portal' => 'Y'
    ]);

    if (!isset($data[0]['CONTENT_ID'])) {
        $data = [];
    }

    return view('filtered', [
        'content' => $data,
        'filterType' => 'Genre'
    ]);
}


public function showProfiles()
{
    if (session('subscription_state') !== 'PRIME MEMBER') {
        return redirect('/welcome')->with('toast', 'Only Prime Members can access profiles.');
    }

    // Fake profiles for now — later connect to DB or API
    $profiles = [
        ['name' => 'DH', 'image' => 'zombiewar.jpg'],
        ['name' => 'Alan', 'image' => 'default2.png'],
        ['name' => 'Sam', 'image' => 'default3.png'],
        ['name' => 'John', 'image' => 'default4.png'],
        ['name' => 'Adult', 'image' => 'default5.png'],
    ];

    return view('profiles', ['profiles' => $profiles]);
}

public function multiFilter(Request $request)
{
    $categoryIds = explode(',', $request->input('categories', ''));
    $genreIds = explode(',', $request->input('genres', ''));

    // ✅ Use global helper to fetch all content
    $data = collect(seeprime_api([
        'select_id' => 'select_content',
        'admin_portal' => 'Y'
    ]));

    // ✅ Filter content based on category and genre
    $filtered = $data->filter(function ($item) use ($categoryIds, $genreIds) {
        $matchCategory = empty($categoryIds[0]) || in_array($item['CATEGORY_ID'], $categoryIds);

        $itemGenres = explode(',', $item['GENRE_IDS'] ?? '');
        $matchGenre = empty($genreIds[0]) || count(array_intersect($itemGenres, $genreIds)) > 0;

        return $matchCategory && $matchGenre;
    });

    return view('filtered', [
        'content' => $filtered,
        'filterType' => 'Multi-Filter',
    ]);
}


}