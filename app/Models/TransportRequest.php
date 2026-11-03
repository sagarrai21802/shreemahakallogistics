<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'reference',
    'customer_name',
    'customer_phone',
    'customer_email',
    'pickup_address',
    'pickup_city',
    'destination_address',
    'destination_city',
    'goods_category',
    'goods_description',
    'quantity',
    'weight_kg',
    'is_fragile',
    'needs_loading_help',
    'special_instructions',
    'status',
    'submitted_at',
])]
class TransportRequest extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'is_fragile' => 'boolean',
            'needs_loading_help' => 'boolean',
            'weight_kg' => 'integer',
        ];
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(DriverInvitation::class);
    }
}
