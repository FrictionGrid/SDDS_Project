<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'customer_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'mime_type',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get icon class based on file type
     */
    public function getIconClassAttribute(): string
    {
        return match($this->file_type) {
            'pdf' => 'document-icon--pdf',
            'doc', 'docx' => 'document-icon--doc',
            'xls', 'xlsx' => 'document-icon--excel',
            'jpg', 'jpeg', 'png', 'gif' => 'document-icon--img',
            default => 'document-icon--default',
        };
    }
}
