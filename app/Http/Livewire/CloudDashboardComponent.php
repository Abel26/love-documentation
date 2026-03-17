<?php

namespace App\Http\Livewire;

use App\Models\Image;
use App\Models\Video;
use App\Services\StorageCalculationService;
use Livewire\Component;

/**
 * Cloud Dashboard Component
 *
 * Main dashboard component untuk menampilkan storage statistics,
 * recent uploads, dan system information
 */
class CloudDashboardComponent extends Component
{
    /**
     * Storage statistics
     */
    public $imageStats = [];
    public $videoStats = [];
    public $combinedStats = [];

    /**
     * Recent uploads
     */
    public $recentImages = [];
    public $recentVideos = [];

    /**
     * System information
     */
    public $systemInfo = [];

    /**
     * Storage trends
     */
    public $storageTrends = [];

    /**
     * Show upload modal
     */
    public $showImageUploadModal = false;
    public $showVideoUploadModal = false;

    /**
     * Auto-refresh interval (in seconds)
     */
    public $refreshInterval = 30;

    /**
     * Storage calculation service
     */
    protected $storageService;

    /**
     * Boot component
     */
    public function boot(StorageCalculationService $storageService): void
    {
        $this->storageService = $storageService;
    }

    /**
     * Mount component
     */
    public function mount(): void
    {
        $this->refreshData();
    }

    /**
     * Refresh all data
     */
    public function refreshData(): void
    {
        $userId = auth()->id();

        // Get storage statistics
        $this->imageStats = $this->storageService->getImageStorageStats($userId);
        $this->videoStats = $this->storageService->getVideoStorageStats($userId);
        $this->combinedStats = $this->storageService->getCombinedStorageStats($userId);

        // Get recent uploads
        $this->recentImages = Image::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $this->recentVideos = Video::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Get system information
        $this->systemInfo = $this->getSystemInfo();

        // Get storage trends
        $this->storageTrends = $this->storageService->getStorageTrends($userId, 7);
    }

    /**
     * Open image upload modal
     */
    public function openImageUploadModal(): void
    {
        $this->showImageUploadModal = true;
    }

    /**
     * Close image upload modal
     */
    public function closeImageUploadModal(): void
    {
        $this->showImageUploadModal = false;
    }

    /**
     * Open video upload modal
     */
    public function openVideoUploadModal(): void
    {
        $this->showVideoUploadModal = true;
    }

    /**
     * Close video upload modal
     */
    public function closeVideoUploadModal(): void
    {
        $this->showVideoUploadModal = false;
    }

    /**
     * Handle image uploaded event
     */
    public function onImageUploaded(): void
    {
        $this->closeImageUploadModal();
        $this->storageService->clearCache(auth()->id());
        $this->refreshData();
        $this->dispatchBrowserEvent('swal:success', [
            'title' => 'Berhasil',
            'text' => 'Gambar berhasil diupload',
        ]);
    }

    /**
     * Handle video uploaded event
     */
    public function onVideoUploaded(): void
    {
        $this->closeVideoUploadModal();
        $this->storageService->clearCache(auth()->id());
        $this->refreshData();
        $this->dispatchBrowserEvent('swal:success', [
            'title' => 'Berhasil',
            'text' => 'Video berhasil diupload',
        ]);
    }

    /**
     * Get system information
     */
    protected function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
            'disk_free' => disk_free_space('/'),
            'disk_total' => disk_total_space('/'),
            'disk_usage_percentage' => $this->calculateDiskUsage(),
        ];
    }

    /**
     * Calculate disk usage percentage
     */
    protected function calculateDiskUsage(): float
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;

        if ($total == 0) {
            return 0;
        }

        return round(($used / $total) * 100, 2);
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.cloud-dashboard-component');
    }
}
