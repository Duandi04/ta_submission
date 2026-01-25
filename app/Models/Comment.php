<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'thesis_submission_id',
        'user_id',
        'content',
        'parent_id',
    ];

    /**
     * Relationships
     */
    public function thesisSubmission()
    {
        return $this->belongsTo(ThesisSubmission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Scopes
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
