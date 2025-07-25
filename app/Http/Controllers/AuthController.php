<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\MyList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
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


        $payload['stripe_token'] = 'dummy-token-for-now';
        // if($request->has('stripeToken')){
        //     $payload['stripe_token'] = $request->input('stripeToken');
        // }
        // else{
        //      return back()->withInput()->withErrors(['register_error' => 'Stripe token is missing for PRIME account.']);
        // }    
    }


    try {
        $registerApiUrl = seeprime_url('insert.php?action=register_user', 'insert');
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

    public function manageProfiles()
    {
        $userId = session('user_id');

        $profiles = seeprime_api([
            'select_id' => 'select_users_profile_by_user_id',
            'admin_portal' => 'N',
            'user_id' => $userId           
        ]);

        if(!is_array($profiles)){
            $profiles = [];
        }

        // $profiles = is_array($profiles) ? $profiles : [];

        return view('profiles',compact('profiles'));
    }

public function storeProfile(Request $request)
{
    if ($request->filled('profile_id')) {
        return $this->updateProfile($request, $request->profile_id);
    }

    $request->validate([
        'profile_name' => 'required|string|max:255',
        'profile_pin' => 'required|digits:4',
        'profile_photo' => 'required|image|max:2048',
    ]);

    // ✅ Use full, working API URL (like in Postman/React Native)
    $apiUrl = seeprime_url('?action=add_profiles', 'insert');

    $response = Http::asMultipart()->post($apiUrl, [
        ['name' => 'user_id', 'contents' => session('user_id')],
        ['name' => 'profile_name', 'contents' => $request->profile_name],
        ['name' => 'profile_pin', 'contents' => $request->profile_pin],
        [
            'name' => 'profile_photo',
            'contents' => fopen($request->file('profile_photo')->getPathname(), 'r'),
            'filename' => $request->file('profile_photo')->getClientOriginalName(),
        ],
    ]);

    Log::info('Profile API response', ['status' => $response->status(), 'body' => $response->body()]);

    if ($response->successful() && str_contains(strtolower($response->body()), 'success')) {
        return redirect()->route('profiles.index')->with('message', 'Profile Created Successfully.');
    }

    return back()->with('error', 'Failed to create profile. API responded with: ' . $response->body());
}


public function deleteProfile($id)
{
    $profiles = seeprime_api([
        'select_id' => 'select_users_profile_by_user_id',
        'admin_portal' => 'N',
        'user_id' => session('user_id')
    ]);

    if(!is_array($profiles)){
        $profiles = [];
    }

    $profileToDelete = collect($profiles)->firstWhere('ID',$id);

    if ($profileToDelete && 
        in_array($profileToDelete['PROFILE_TYPE'] ?? '', ['Adult', 'Kid']) && 
        ($profileToDelete['DEFAULT_PROFILE'] ?? '') === 'Y') {
        return redirect()->route('profiles.index')
               ->with('error', 'Default '.$profileToDelete['PROFILE_TYPE'].' profile cannot be deleted.');
    }

    $url = seeprime_url("delete.php?action=remove_profile&id={$id}", 'delete');
    $response =  Http::get($url);

    Log::info('Delete Profile API',[
        'url' => $url,
        'status' => $response->status(),
        'body' => $response->body()
    ]);

      if ($response->successful() && str_contains(strtolower($response->body()), 'success')) {
        return redirect()->route('profiles.index')->with('message', 'Profile deleted successfully.');
    }

     return redirect()->route('profiles.index')->with('error', 'Failed to delete profile.');
    
}

public function updateProfile(Request $request, $id)
{
    $request->validate([
        'profile_name' => 'required|string|max:255',
        'profile_pin' => 'required|digits:4',
        'profile_photo' => 'nullable|image|max:2048',
    ]);

    $url = seeprime_url('update.php?action=update_profile', 'update');

    $multipart = [
        ['name' => 'id', 'contents' => $id],
        ['name' => 'profile_name', 'contents' => $request->profile_name],
        ['name' => 'profile_pin', 'contents' => $request->profile_pin],
    ];

    if ($request->hasFile('profile_photo')) {
        $multipart[] = [
            'name' => 'profile_photo',
            'contents' => fopen($request->file('profile_photo')->getPathname(), 'r'),
            'filename' => $request->file('profile_photo')->getClientOriginalName(),
        ];
    }

    $response = Http::asMultipart()->post($url, $multipart);

    Log::info('Update Profile Response', ['status' => $response->status(), 'body' => $response->body()]);

    if ($response->successful() && str_contains($response->body(), 'success')) {
        return redirect()->route('profiles.index')->with('message', 'Profile updated successfully.');
    }

    return back()->with('error', 'Failed to update profile.');
}
public function handleProfileLogin(Request $request)
{
    $profileId = $request->input('profile_id');

    $profiles = seeprime_api([
        'select_id' => 'select_users_profile_by_user_id',
        'admin_portal' => 'N',
        'user_id' => session('user_id')
    ]);

    $profile = collect($profiles)->firstWhere('ID',$profileId);

    if(!$profile){
        return redirect()->route('profiles.index')->withErrors('Profile not found.');
    }

    session([
        'active_profile_id' => $profile['ID'],
        'active_profile_name' => $profile['PROFILE_NAME'],
    ]);

   return redirect()->route('welcome');
}

public function getProfiePhoto($id)
{
    if (!session()->has('user_id')) {
        abort(403);
    }

    $profiles = seeprime_api([
        'select_id' => 'select_users_profile_by_user_id',
        'admin_portal' => 'N',
        'user_id' => session('user_id')
    ]);

    Log::info('Profiles fetched in getProfiePhoto', $profiles); // Debug log

    $profile = collect($profiles)->firstWhere('ID', $id);

    if (!$profile || empty($profile['PROFILE_PHOTO'])) {
        Log::info("Profile photo not found or invalid for ID: $id");
        return response()->file(public_path('images/default-avatar.png')); // Ensure this file exists
    }

    // $binary = base64_decode($profile['PROFILE_PHOTO']);
    $binary = base64_decode($profile['PROFILE_PHOTO']);
    if ($binary === false) {
        Log::info("Base64 decode failed for ID: $id, Photo: " . $profile['PROFILE_PHOTO']);
        return response()->file(public_path('images/de.png'));
    }

    return response($binary)->header('Content-Type', 'image/jpeg');
}

// public function getProfiePhoto($id)
// {
//     if(!session()->has('user_id')){
//         abort(403);
//     }

// $profiles = seeprime_api([
//     'select_id' => 'select_users_profile_by_user_id',
//     'admin_portal' => 'N',
//     'user_id' => session('user_id')
// ]);
// $profile = collect($profiles)->firstWhere('ID',$id);

// if(!$profile || empty($profile['PROFILE_PHOTO'])){
//     return response()->file(public_path('images/default-avatar.png'));
// }

// $binary = base64_decode($profile['PROFILE_PHOTO']);

// return response($binary)->header('Content-Type', 'image/jpeg');
// }
}
