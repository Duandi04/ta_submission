<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Faculty extends Model
{
    use LogsActivity;
    protected $fillable = ['name', 'code'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('faculties');
    }

    public function programStudis()
    {
        return $this->hasMany(ProgramStudi::class);
    }
}
