<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'filename',
        'original_filename',
        'path',
        'thumbnail_path',
        'size',
        'mime_type',
        'upload_date',
        'upload_month',
        'image_group_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'upload_date' => 'date',
        'size' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the image.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the image group that owns the image.
     */
    public function imageGroup(): BelongsTo
    {
        return $this->belongsTo(ImageGroup::class, 'image_group_id');
    }

    /**
     * Scope a query to only include images from a specific month.
     */
    public function scopeByMonth($query, $month)
    {
        if ($month) {
            return $query->where('upload_month', $month);
        }
        return $query;
    }

    /**
     * Scope a query to only include images from a specific date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('upload_date', [$startDate, $endDate]);
        }
        return $query;
    }

    /**
     * Get the full URL of the image.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    /**
     * Get the full URL of the thumbnail.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return Storage::url($this->thumbnail_path);
    }

    /**
     * Get the display name of the file (original filename).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->original_filename;
    }

    /**
     * Get the file size in human-readable format.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
