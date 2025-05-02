<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class PageController extends Controller
{

    public function welcome()
{
    $apiUrl = "http://3.109.176.31/SeePrime/APIS/SELECT.php?select_id=select_content&admin_portal=Y";
    $response = Http::get($apiUrl);

    if ($response->successful()) {
        $data = $response->json();

        $featured = $data[0] ?? null;

        // Group by CONTENT_TYPE
        $grouped = collect($data)->groupBy('CONTENT_TYPE');

        return view('welcome', [
            'featured' => $featured,
            'grouped' => $grouped
        ]);
    }

    return view('welcome', [
        'featured' => null,
        'grouped' => collect()
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

    $apiUrl = "http://3.109.176.31/SeePrime/APIS/SELECT.php?select_id=select_users_by_name_and_pass&admin_portal=N&username={$username}&password={$password}";

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
            return back()->with('error','Invalid Username or Password');
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

    public function playVideo($id)
    {
        $apiUrl = "http://3.109.176.31/SeePrime/APIS/SELECT.php?select_id=content_detail&content_id={$id}&admin_portal=N";
        $response = Http::get($apiUrl);
    
        if ($response->successful()) {
            $data = $response->json();
            $video = is_array($data) && isset($data[0]) ? $data[0] : $data;
    
            if (!empty($video)) {
                // Normalize SOURCE
                if (empty($video['SOURCE']) && !empty($video['SOURCE_PATH'])) {
                    $video['SOURCE'] = $video['SOURCE_PATH'];
                }
    
                return view('play', compact('video'));
            } else {
                return abort(404, 'Video not found');
            }
        }
    
        return abort(500, 'Failed to load video details');
    }
    

    


}
