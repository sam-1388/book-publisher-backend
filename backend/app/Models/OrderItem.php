<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Unguarded]
class OrderItem extends Model
{
    protected function casts(): array
    {
        return [
            'files' => 'array',
            'purchase' => 'boolean',
            'print' => 'boolean',
            'publish' => 'boolean',
            'translate' => 'boolean',
            'other' => 'boolean',
            'quantity' => 'integer',
        ];
    }
}
