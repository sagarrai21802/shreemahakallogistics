<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'partner_ref',
    'name',
    'phone',
    'email',
    'vehicle_type',
    'vehicle_model',
    'registration_number',
    'capacity_tons',
    'approval_status',
    'availability_status',
    'last_location_at',
    'lat',
    'lng',
])]
class Driver extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'last_location_at' => 'datetime',
            'capacity_tons' => 'decimal:2',
        ];
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(DriverInvitation::class);
    }
}
