<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
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

    public function handleRegister(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'username' => 'required|alpha_num|min:3|max:20',
        'password' => 'required|min:5',
        'confirm_password' => 'required|same:password',
        'account_type' => 'required|in:free,prime,trial',
        'cnic' => ['required', 'regex:/^\d{5}-\d{7}-\d{1}$/']
    ]);

    $accountType = strtoupper($request->input('account_type'));
    $payload = [
        'email' => trim($request->email),
        'username' => trim($request->username),
        'password' => $request->password,
        'user_type' => $accountType,
        'cnic' => trim($request->cnic)
    ];

    // For PRIME users, add dummy card info for now (or use real input later)
    if ($accountType === 'PRIME') {
        if($request->has('stripeToken')){
            $payload['stripe_token'] = $request->input('stripeToken');
        }
        else{
             return back()->withInput()->withErrors(['register_error' => 'Stripe token is missing for PRIME account.']);
        }    
    }


    try {
        $registerApiUrl = str_replace('SELECT.php', 'insert.php?action=register_user',env('SEEPRIME_API_BASE'));
        $response = HTTP::post($registerApiUrl,$payload);
        $result = $response->json();
        $rawBody = $response->body();

        Log::info('API response', is_array($result) ? $result : ['raw' => $response->body()]);
        Log::info('sent payload', $payload);

        if (!empty($result['success']) || ($result['status'] ?? '') === 'success') {
    return redirect()->route('login')->with('message', 'Registration successful. Please confirm your email before logging in.');
    }
    
        if (str_contains($rawBody, 'VERIFICATION EMAIL SENDED')) {
         return redirect()->route('login')->with('message', 'Registration successful. Please confirm your email before logging in.');
    }

    return back()->withInput()->withErrors([
        'register_error' => $result['message'] ?? 'Unknown error from API'
    ]);

    } catch (\Exception $e) {
        return back()->withInput()->withErrors(['register_error' => 'API connection failed: ' . $e->getMessage()]);
    }
}

public function getAccountType()
{
    $response = HTTP::get('http://15.184.102.5/apis/SeePrime/apis/select.php',[
        'select_id' => 'subscription_status',
        'admin_portal' => 'n'
    ]);

return response()->json($response->json());

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
