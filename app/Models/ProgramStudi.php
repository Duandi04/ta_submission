<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    protected $fillable = ['name', 'code', 'faculty_id'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
