<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Video;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Storage Calculation Service
 * 
 * Service untuk menghitung penggunaan storage untuk gambar dan video
 * dengan caching untuk optimasi performa
 */
class StorageCalculationService
{
    /**
     * Maximum storage limit for images (10GB in bytes)
     */
    const MAX_IMAGE_STORAGE = 10737418240;

    /**
     * Maximum storage limit for videos (10GB in bytes)
     */
    const MAX_VIDEO_STORAGE = 10737418240;

    /**
     * Cache duration in seconds (5 minutes)
     */
    const CACHE_DURATION = 300;

    /**
     * Get image storage statistics for a user
     *
     * @param int|null $userId User ID, null for all users
     * @return array
     */
    public function getImageStorageStats(?int $userId = null): array
    {
        $cacheKey = $userId 
            ? "image_storage_stats_{$userId}" 
            : "image_storage_stats_all";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userId) {
            $query = Image::query();
            
            if ($userId) {
                $query->where('user_id', $userId);
            }

            $totalSize = (int) $query->sum('size');
            $count = $query->count();
            $available = max(0, self::MAX_IMAGE_STORAGE - $totalSize);
            $percentage = self::calculatePercentage($totalSize, self::MAX_IMAGE_STORAGE);

            return [
                'total_size' => $totalSize,
                'total_size_formatted' => $this->formatBytes($totalSize),
                'count' => $count,
                'available' => $available,
                'available_formatted' => $this->formatBytes($available),
                'percentage' => $percentage,
                'max_storage' => self::MAX_IMAGE_STORAGE,
                'max_storage_formatted' => $this->formatBytes(self::MAX_IMAGE_STORAGE),
                'is_near_limit' => $percentage >= 90,
                'is_over_limit' => $percentage >= 100,
            ];
        });
    }

    /**
     * Get video storage statistics for a user
     *
     * @param int|null $userId User ID, null for all users
     * @return array
     */
    public function getVideoStorageStats(?int $userId = null): array
    {
        $cacheKey = $userId 
            ? "video_storage_stats_{$userId}" 
            : "video_storage_stats_all";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userId) {
            $query = Video::query();
            
            if ($userId) {
                $query->where('user_id', $userId);
            }

            $totalSize = (int) $query->sum('size');
            $count = $query->count();
            $available = max(0, self::MAX_VIDEO_STORAGE - $totalSize);
            $percentage = self::calculatePercentage($totalSize, self::MAX_VIDEO_STORAGE);

            return [
                'total_size' => $totalSize,
                'total_size_formatted' => $this->formatBytes($totalSize),
                'count' => $count,
                'available' => $available,
                'available_formatted' => $this->formatBytes($available),
                'percentage' => $percentage,
                'max_storage' => self::MAX_VIDEO_STORAGE,
                'max_storage_formatted' => $this->formatBytes(self::MAX_VIDEO_STORAGE),
                'is_near_limit' => $percentage >= 90,
                'is_over_limit' => $percentage >= 100,
            ];
        });
    }

    /**
     * Get combined storage statistics for a user
     *
     * @param int|null $userId User ID, null for all users
     * @return array
     */
    public function getCombinedStorageStats(?int $userId = null): array
    {
        $imageStats = $this->getImageStorageStats($userId);
        $videoStats = $this->getVideoStorageStats($userId);

        $totalSize = $imageStats['total_size'] + $videoStats['total_size'];
        $totalMaxStorage = self::MAX_IMAGE_STORAGE + self::MAX_VIDEO_STORAGE;
        $totalAvailable = $imageStats['available'] + $videoStats['available'];
        $totalPercentage = self::calculatePercentage($totalSize, $totalMaxStorage);

        return [
            'images' => $imageStats,
            'videos' => $videoStats,
            'total' => [
                'size' => $totalSize,
                'size_formatted' => $this->formatBytes($totalSize),
                'available' => $totalAvailable,
                'available_formatted' => $this->formatBytes($totalAvailable),
                'max_storage' => $totalMaxStorage,
                'max_storage_formatted' => $this->formatBytes($totalMaxStorage),
                'percentage' => $totalPercentage,
                'count' => $imageStats['count'] + $videoStats['count'],
            ],
        ];
    }

    /**
     * Check if user can upload image of given size
     *
     * @param int $userId
     * @param int $fileSize File size in bytes
     * @return array
     */
    public function canUploadImage(int $userId, int $fileSize): array
    {
        $stats = $this->getImageStorageStats($userId);
        $canUpload = ($stats['available'] >= $fileSize);

        return [
            'can_upload' => $canUpload,
            'available' => $stats['available'],
            'available_formatted' => $stats['available_formatted'],
            'required' => $fileSize,
            'required_formatted' => $this->formatBytes($fileSize),
            'shortage' => max(0, $fileSize - $stats['available']),
            'shortage_formatted' => $this->formatBytes(max(0, $fileSize - $stats['available'])),
        ];
    }

    /**
     * Check if user can upload video of given size
     *
     * @param int $userId
     * @param int $fileSize File size in bytes
     * @return array
     */
    public function canUploadVideo(int $userId, int $fileSize): array
    {
        $stats = $this->getVideoStorageStats($userId);
        $canUpload = ($stats['available'] >= $fileSize);

        return [
            'can_upload' => $canUpload,
            'available' => $stats['available'],
            'available_formatted' => $stats['available_formatted'],
            'required' => $fileSize,
            'required_formatted' => $this->formatBytes($fileSize),
            'shortage' => max(0, $fileSize - $stats['available']),
            'shortage_formatted' => $this->formatBytes(max(0, $fileSize - $stats['available'])),
        ];
    }

    /**
     * Clear storage cache for a user
     *
     * @param int|null $userId User ID, null for all users
     * @return void
     */
    public function clearCache(?int $userId = null): void
    {
        if ($userId) {
            Cache::forget("image_storage_stats_{$userId}");
            Cache::forget("video_storage_stats_{$userId}");
        } else {
            Cache::forget("image_storage_stats_all");
            Cache::forget("video_storage_stats_all");
        }

        Log::info('Storage cache cleared', ['user_id' => $userId]);
    }

    /**
     * Format bytes to human-readable format
     *
     * @param int $bytes Size in bytes
     * @param int $precision Decimal precision
     * @return string
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Calculate percentage
     *
     * @param int $value Current value
     * @param int $max Maximum value
     * @return float
     */
    protected function calculatePercentage(int $value, int $max): float
    {
        if ($max == 0) {
            return 0;
        }

        $percentage = ($value / $max) * 100;
        return min(100, max(0, $percentage));
    }

    /**
     * Get storage trends over time
     *
     * @param int|null $userId User ID, null for all users
     * @param int $days Number of days to look back
     * @return array
     */
    public function getStorageTrends(?int $userId = null, int $days = 30): array
    {
        $cacheKey = $userId 
            ? "storage_trends_{$userId}_{$days}" 
            : "storage_trends_all_{$days}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userId, $days) {
            $startDate = now()->subDays($days)->startOfDay();
            $endDate = now()->endOfDay();

            $imageQuery = Image::whereBetween('upload_date', [$startDate, $endDate]);
            $videoQuery = Video::whereBetween('upload_date', [$startDate, $endDate]);

            if ($userId) {
                $imageQuery->where('user_id', $userId);
                $videoQuery->where('user_id', $userId);
            }

            $imagesByDate = $imageQuery->selectRaw('DATE(upload_date) as date, SUM(size) as total_size, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $videosByDate = $videoQuery->selectRaw('DATE(upload_date) as date, SUM(size) as total_size, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $trends = [];
            $currentDate = clone $startDate;

            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $trends[] = [
                    'date' => $dateStr,
                    'images' => [
                        'size' => $imagesByDate[$dateStr]->total_size ?? 0,
                        'size_formatted' => $this->formatBytes($imagesByDate[$dateStr]->total_size ?? 0),
                        'count' => $imagesByDate[$dateStr]->count ?? 0,
                    ],
                    'videos' => [
                        'size' => $videosByDate[$dateStr]->total_size ?? 0,
                        'size_formatted' => $this->formatBytes($videosByDate[$dateStr]->total_size ?? 0),
                        'count' => $videosByDate[$dateStr]->count ?? 0,
                    ],
                ];

                $currentDate->addDay();
            }

            return $trends;
        });
    }
}
