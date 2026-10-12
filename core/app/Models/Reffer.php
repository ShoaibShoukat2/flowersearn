<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reffer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'level' => 'array',
        'commision' => 'array'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
