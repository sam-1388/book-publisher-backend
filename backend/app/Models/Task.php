<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Unguarded]
class Task extends Model
{
    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
