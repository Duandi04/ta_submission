<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SubmissionFile extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['file_name', 'file_type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('files');
    }

    protected $fillable = [
        'thesis_submission_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    /**
     * Relationships
     */
    public function thesisSubmission()
    {
        return $this->belongsTo(ThesisSubmission::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Helper methods
     */
    public function getFormattedFileSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, 2) . ' ' . $units[$index];
    }

    public function getFileTypeLabel(): string
    {
        return match ($this->file_type) {
            'proposal' => 'Proposal',
            'final_document' => 'Dokumen Akhir',
            'presentation' => 'Presentasi',
            default => 'Lainnya',
        };
    }
}
