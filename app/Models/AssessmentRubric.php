<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentRubric extends Model
{
    protected $fillable = [
        'thesis_submission_id',
        'name',
        'description',
        'criteria',
    ];

    protected $casts = [
        'criteria' => 'array',
    ];

    public function thesisSubmission()
    {
        return $this->belongsTo(ThesisSubmission::class);
    }
}
