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

        $featured = $data[0] ?? null;
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
    
            $isMultiPart = is_array($data) && isset($data[0]) && isset($data[0]['CONTENT_ID']);
            $episodes = [];
            $video = [];
    
            if ($isMultiPart) {
                $episodes = $data;
    
                $meta = $episodes[0];
                $selected = $partId
                    ? collect($episodes)->firstWhere('CONTENT_DETAIL_ID', $partId)
                    : $meta;
    
                $video = array_merge($meta, $selected);
    
                // Always assign source
                $video['SOURCE'] = $selected['SOURCE_PATH'] ?? $selected['SOURCE'] ?? $meta['SOURCE_PATH'] ?? $meta['SOURCE'] ?? '';
    
                // Always assign thumbnail
                $video['THUMBNAIL_PATH'] = $selected['THUMBNAIL_PATH'] ?? $meta['THUMBNAIL_PATH'] ?? '';
    
            } else {
                $video = is_array($data) ? (array)$data : [];
                $video['SOURCE'] = $video['SOURCE_PATH'] ?? $video['SOURCE'] ?? '';
            }
    
            // Final safety net
            $video['SOURCE'] = (string) $video['SOURCE'];
    
            return view('play', compact('video', 'episodes'));
        }
    
        return abort(500, 'Failed to load video details');
    }
    

}