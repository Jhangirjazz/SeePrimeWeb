<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Video;
use App\Models\User;

class WatchHistory extends Model
{  protected $fillable = [
        'user_id', 'video_id', 'duration', 'watched'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}