<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyList extends Model
{
    protected $table = 'my_lists'; // this is correct (table name)

    protected $fillable = [       // not $table again!
        'user_id',
        'content_id',
    ];
}