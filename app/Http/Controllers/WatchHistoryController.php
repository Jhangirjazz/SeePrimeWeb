<?php

namespace App\Http\Controllers;

use App\Models\WatchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WatchHistoryController extends Controller
{
        public function index(): JsonResponse
    {
        $userId = session('user_id');          // or Auth::id()

        $rows = WatchHistory::where('user_id', $userId)
                 ->whereColumn('watched','<',DB::raw('duration * 0.95'))
                 ->orderByDesc('updated_at')
                 ->limit(20)
                 ->get([
                     'video_id',
                     'watched',
                     'duration',
                     'updated_at'
                 ]);

        return response()->json($rows);
    }

public function save(Request $request)
{
    Log::info('⏺ Save Progress Payload', $request->all());

    $request->validate([
        'video_id' => 'required|numeric',
        'watched' => 'required|numeric|min:0',
        'duration' => 'required|numeric|min:1',
    ]);

    $userId = session('user_id');

    if (!$userId) {
        return response()->json(['error' => 'User not logged in'], 401);
    }

    WatchHistory::updateOrCreate(
        ['user_id' => $userId, 'video_id' => $request->video_id],
        ['watched' => $request->watched, 'duration' => $request->duration]
    );

    return response()->json(['status' => 'saved']);
}

}
