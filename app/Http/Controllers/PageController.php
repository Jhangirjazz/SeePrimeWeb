<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class PageController extends Controller
{

    public function welcome()
{
    $apiUrl = "http://15.184.102.5/SeePrime/APIS/SELECT.php?select_id=select_content&admin_portal=Y";
    $response = Http::get($apiUrl);

    if ($response->successful()) {
        $data = $response->json();

        $featured = collect($data)->firstWhere('CONTENT_ID', (string) $data[0]['CONTENT_ID'] ?? null);
        $featured['REVIEW_STARS'] = $featured['REVIEW_STARS'] ?? null;
        $featured['AGE_RATING'] = $featured['AGE_RATING'] ?? null;
        $featured['DESCRIPTION'] = $featured['DESCRIPTION'] ?? 'No description available.';

        $grouped = collect($data)->groupBy('CONTENT_TYPE');

        $top10 = collect($data)
    ->take(10)
    ->map(function ($item, $index) {
        $thumb = $item['THUMBNAIL_PATH'] ?? null;
        $source = strtolower($item['SOURCE'] ?? $item['SOURCE_PATH'] ?? '');

        if ($thumb && str_ends_with($thumb, '.jpg')) {
            $thumbUrl = "http://15.184.102.5/SeePrime/Content/Images/{$thumb}";
        } elseif ($source === 'youtube' && $thumb) {
            $thumbUrl = "https://img.youtube.com/vi/{$thumb}/maxresdefault.jpg";
        } else {
            $thumbUrl = asset('images/default.jpg');
        }

        return [
            'rank' => $index + 1,
            'title' => $item['TITLE'] ?? 'Untitled',
            'thumbnail_url' => $thumbUrl,
            'views' => !empty($item['VIEWS']) ? $item['VIEWS'] . ' VIEWS' : '',
            'content_id' => $item['CONTENT_ID'] ?? null,   // ✅ Add this
        ];
    })->toArray();
        
    }

    return view('welcome', [
        'featured' => $featured,
        'grouped' => $grouped,
        'top10' => $top10
    ]);
    
      
}

    
    public function shows()
    {
        return view('shows');
    }

    public function movies()
    {
        return view('movies');
    }

    public function webseries()
    {
        return view('webseries');
    }

    public function new()
    {
        return view('new');
    }

    public function mylist()
    {
        return view('mylist');
    }

    public function login()
    {
        return view('login');
    }
    
    public function handleLogin(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    $username = $request->username;
    $password = $request->password;

    $apiUrl = "http://15.184.102.5/SeePrime/APIS/SELECT.php?select_id=select_users_by_name_and_pass&admin_portal=N&username={$username}&password={$password}";

    $response = Http::get($apiUrl);

    if ($response->successful()) {
        $data = $response->json();

        //  Check API state
        if (isset($data['state']) && $data['state'] === 'Success') {
            
            session([
                'user_id' => $data['user_id'],
                'user_name' => $data['user_name'],
                'subscription_state' => $data['subscription_state']
            ]);

            return redirect('/welcome');
        } else {
            return back()->with('error', $data['message'] ?? 'Invalid Username or Password');
        }
    } else {
        return back()->with('error' , 'Unable to connect to server.');
    }

   

    }

    public function register()
    {

        return view('register');
    }

    public function logout()
    {
    
        session()->flush();
        
        return redirect('/');
    }

    

    public function playVideo($id, $partId = null)
    {
        $apiUrl = "http://15.184.102.5/SeePrime/APIS/SELECT.php?select_id=content_detail&content_id={$id}&admin_portal=Y";
        $response = Http::get($apiUrl);
    
        if (!session()->has('user_id')) {
            return redirect('/welcome')->with('toast', 'Please register yourself to access content.');
        }
    
        if ($response->successful()) {
            $data = $response->json();

                        // Fetch top-level content info from select_content API
            $metaApiUrl = "http://15.184.102.5/SeePrime/APIS/SELECT.php?select_id=select_content&admin_portal=Y";
            $metaResponse = Http::get($metaApiUrl);

            $metaContent = [];
            if ($metaResponse->successful()) {
                $allContent = $metaResponse->json();
                $metaContent = collect($allContent)->firstWhere('CONTENT_ID', (string)$id);
}

    
            // ✅ Define multi-part only if array has more than 1 item with CONTENT_DETAIL_ID
            $isMultiPart = is_array($data) && count($data) > 1 && isset($data[0]['CONTENT_DETAIL_ID']);
            $episodes = $isMultiPart ? $data : [];
             // or SEASON based on your data key
            $video = [];

            
            if ($isMultiPart) {
                $meta = $data[0];
                $selected = $partId
                ? collect($data)->firstWhere('CONTENT_DETAIL_ID', $partId)
                : $meta;

                $selectedSeason = $partId 
                ? (string) ($selected['SEASON'] ?? '1')
                : (string) request()->get('season', '1');


                // ✅ Merge selected episode and fallback data into $video
                $video = array_merge($meta, $selected);

                // ✅ Fallback from top-level content meta
                $video['REVIEW_STARS'] = $video['REVIEW_STARS'] ?? $metaContent['REVIEW_STARS'] ?? null;
                $video['AGE_RATING']   = $video['AGE_RATING'] ?? $metaContent['AGE_RATING'] ?? null;
                $video['TITLE']        = $video['TITLE'] ?? $metaContent['TITLE'] ?? 'Untitled';
                $video['DESCRIPTION']  = $video['DESCRIPTION'] ?? $metaContent['DESCRIPTION'] ?? 'No description available';


                    // $video = array_merge($meta, $selected);
                    // $video['TITLE']        = $video['TITLE'] ?? $meta['TITLE'] ?? 'Untitled';
                    // $video['DESCRIPTION']  = $video['DESCRIPTION'] ?? $meta['DESCRIPTION'] ?? 'No description available';
                    // $video['REVIEW_STARS'] = $video['REVIEW_STARS'] ?? $meta['REVIEW_STARS'] ?? null;
                    // $video['AGE_RATING']   = $video['AGE_RATING'] ?? $meta['AGE_RATING'] ?? null;

                    

                $video['SOURCE'] = $selected['SOURCE_PATH'] ?? $selected['SOURCE'] ?? $meta['SOURCE_PATH'] ?? $meta['SOURCE'] ?? '';
                $video['THUMBNAIL_PATH'] = $selected['THUMBNAIL_PATH'] ?? $meta['THUMBNAIL_PATH'] ?? '';
            
                // ✅ Group episodes by string-based season key
                $episodes = $data;
                $episodesBySeason = collect($episodes)
                    ->groupBy('SEASON')
                    ->mapWithKeys(function ($items, $key) {
                        return [(string) $key => $items];
                    });
            }
            
            else {
                // Handle single video that may still be wrapped in an array
                $raw = is_array($data) && isset($data[0]) ? $data[0] : $data;
            
                $video = is_array($raw) ? $raw : [];
                
                $video['REVIEW_STARS'] = $video['REVIEW_STARS'] ?? $metaContent['REVIEW_STARS'] ?? null;
                $video['AGE_RATING']   = $video['AGE_RATING'] ?? $metaContent['AGE_RATING'] ?? null;
                $video['TITLE']        = $video['TITLE'] ?? $metaContent['TITLE'] ?? 'Untitled';
                $video['DESCRIPTION']  = $video['DESCRIPTION'] ?? $metaContent['DESCRIPTION'] ?? 'No description available';
                $video['SOURCE'] = $video['SOURCE_PATH'] ?? $video['SOURCE'] ?? '';
                $video['THUMBNAIL_PATH'] = $video['THUMBNAIL_PATH'] ?? '';

                $episodes = [];
                $episodesBySeason = collect();
                $selectedSeason = 1;
            }
            
        
    
            // ✅ Detect YouTube
            if (!empty($video['SOURCE']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $video['SOURCE'])) {
                $video['SOURCE_TYPE'] = 'youtube';
                $video['YOUTUBE_ID'] = $video['SOURCE'];
            } else {
                $video['SOURCE_TYPE'] = 'video';
            }
    
            return view('play',[
                'video' => $video,
                'episodes' =>  $episodes,
                'episodesBySeason' => $episodesBySeason,
                'selectedSeason' => $selectedSeason     
            ]);
        }
    
        return abort(500, 'Failed to load video details');
    }
    

    public function search(Request $request)
{
    $query = trim($request->input('query'));

    // Fetch content list from API
    $apiUrl = "http://15.184.102.5/SeePrime/APIS/SELECT.php?select_id=select_content&admin_portal=Y";
    $response = Http::get($apiUrl);

    $results = [];

    if ($response->successful()) {
        $data = $response->json();

        // Filter by title or description
        $results = collect($data)->filter(function ($item) use ($query) {
            return str_contains(strtolower($item['TITLE'] ?? ''), strtolower($query)) ||
                   str_contains(strtolower($item['DESCRIPTION'] ?? ''), strtolower($query));
        });
        
    }

    return view('search-results', [
        'results' => $results,
        'query'   => $query,
    ]);
}


}