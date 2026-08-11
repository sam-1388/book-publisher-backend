<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable('name','color')]
class Occupation extends Model
{
    /** @use HasFactory<\Database\Factories\OccupationFactory> */
    use HasFactory;

    public function employees(){
        return $this->belongsToMany(Employee::class);
    }
}
