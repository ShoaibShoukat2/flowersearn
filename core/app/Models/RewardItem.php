<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class RewardItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public static function activeOrdered(?int $limit = null): Collection
    {
        if (!Schema::hasTable((new static)->getTable())) {
            return new Collection();
        }

        $query = static::active()->ordered();

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
