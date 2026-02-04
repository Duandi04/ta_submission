<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProgramStudi extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = ['name', 'code', 'faculty_id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'faculty_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('program_studis');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
