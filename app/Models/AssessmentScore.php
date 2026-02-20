<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'criterion_id',
        'criterion_name',
        'criterion_description',
        'weight',
        'score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    /**
     * Relationships
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function criterion()
    {
        return $this->belongsTo(AssessmentCriterion::class, 'criterion_id');
    }
}
