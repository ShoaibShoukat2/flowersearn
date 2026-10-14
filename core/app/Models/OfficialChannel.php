<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficialChannel extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
    ];
}
