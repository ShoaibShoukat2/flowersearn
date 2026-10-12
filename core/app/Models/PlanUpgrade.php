<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanUpgrade extends Model
{
    const APPROVED = 1;
    const PENDING = 2;
    const REJECTED = 3;

    protected $guarded = ['id'];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromPlan()
    {
        return $this->belongsTo(Plan::class, 'from_plan_id');
    }

    public function toPlan()
    {
        return $this->belongsTo(Plan::class, 'to_plan_id');
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::REJECTED);
    }

    public function statusText(): string
    {
        if ((int) $this->status === self::APPROVED) {
            return 'Approved';
        }
        if ((int) $this->status === self::REJECTED) {
            return 'Rejected';
        }

        return 'Pending';
    }
}
