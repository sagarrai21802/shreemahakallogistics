<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'type',
    'title',
    'description',
    'occurred_at',
])]
class ActivityEvent extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
        ];
    }

    public function tone(): string
    {
        return match ($this->type) {
            'request_received', 'follow_up' => 'brand',
            'driver_accepted', 'application_approved' => 'success',
            'driver_rejected' => 'danger',
            'application_submitted' => 'warning',
            default => 'neutral',
        };
    }
}
