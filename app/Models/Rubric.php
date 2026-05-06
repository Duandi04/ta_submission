<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    protected $fillable = ['name', 'description', 'criteria', 'is_active', 'program_studi_id'];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

}
