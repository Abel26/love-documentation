<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Image;
use App\Models\Video;
use App\Models\ImageGroup;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get images for user (backward compatibility)
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Get videos for user (backward compatibility)
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    /**
     * Get image groups for user (backward compatibility)
     */
    public function imageGroups(): HasMany
    {
        return $this->hasMany(ImageGroup::class);
    }

    /**
     * Get images for user (backward compatibility)
     */
    public function getImages(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Get videos for user (backward compatibility)
     */
    public function getVideos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    /**
     * Get image groups for user (backward compatibility)
     */
    public function getImageGroups(): HasMany
    {
        return $this->hasMany(ImageGroup::class);
    }
}
