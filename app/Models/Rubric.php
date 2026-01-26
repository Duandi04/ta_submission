<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    protected $fillable = ['name', 'description', 'criteria', 'is_active'];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];
}
