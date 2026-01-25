<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThesisStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_submission_id',
        'changed_by',
        'old_status',
        'new_status',
        'comment',
    ];

    /**
     * Relationships
     */
    public function thesisSubmission()
    {
        return $this->belongsTo(ThesisSubmission::class);
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
