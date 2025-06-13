<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',         // or any other fields you have
        'thumbnail',     // for displaying thumbnail
        'description',
        'duration',
        // add any other columns from your `videos` table
    ];

    public function watchHistories()
    {
        return $this->hasMany(WatchHistory::class);
    }
}
