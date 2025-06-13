<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MyList;
use App\Models\WatchHistory;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function handleLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = $request->username;
        $password = $request->password;

        // ✅ Use global helper for login API call
        $data = seeprime_api([
            'select_id' => 'select_users_by_name_and_pass',
            'admin_portal' => 'N',
            'username' => $username,
            'password' => $password
        ]);

        // ✅ Check API response
        if (isset($data['state']) && $data['state'] === 'Success') {

            // ✅ Store session
            session([
                'user_id' => $data['user_id'],
                'user_name' => $data['user_name'],
                'subscription_state' => $data['subscription_state']
            ]);

            // ✅ Redirect based on subscription
            if ($data['subscription_state'] === 'PRIME MEMBER') {
                return redirect('/profiles');
            } else {
                return redirect('/welcome');
            }
        }

        return back()->with('error', $data['message'] ?? 'Invalid Username or Password');
    }

    public function register()
    {
        return view('register');
    }

    public function login()
    {
        return view('login');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/');
    }

    public function addToMyList(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->back()->with('toast', 'You must be logged in.');
        }

        $userId = session('user_id');
        $contentId = $request->input('content_id');

        $exists = MyList::where('user_id', $userId)
            ->where('content_id', $contentId)
            ->exists();

        if (!$exists) {
            MyList::create([
                'user_id' => $userId,
                'content_id' => $contentId
            ]);
        }

        return redirect()->back()->with('toast', 'Added to My List!');
    }

    public function removeFromMyList(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->back()->with('toast', 'You must be logged in.');
        }

        $userId = session('user_id');
        $contentId = $request->input('content_id');

        MyList::where('user_id', $userId)
            ->where('content_id', $contentId)
            ->delete();

        return redirect()->back()->with('toast', 'Removed from My List!');
    }

}
