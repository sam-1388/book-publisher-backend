<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public function casts(): array
    {
        return [
            'files' => 'array',
            'services'=>'array'
        ];
    }

    
}
