<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'driver_name',
    'partner_ref',
    'vehicle_type',
    'documents_received',
    'documents_total',
    'status',
    'submitted_at',
])]
class DriverApplication extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'documents_received' => 'integer',
            'documents_total' => 'integer',
        ];
    }
}
