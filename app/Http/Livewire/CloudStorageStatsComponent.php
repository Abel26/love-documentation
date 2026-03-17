<?php

namespace App\Http\Livewire;

use App\Services\StorageCalculationService;
use Livewire\Component;

/**
 * Cloud Storage Stats Component
 * 
 * Component untuk menampilkan statistik storage
 * dengan progress bar animasi
 */
class CloudStorageStatsComponent extends Component
{
    /**
     * Storage statistics
     */
    public $imageStats = [];
    public $videoStats = [];
    public $combinedStats = [];

    /**
     * Auto-refresh enabled
     */
    public $autoRefresh = true;

    /**
     * Refresh interval (in seconds)
     */
    public $refreshInterval = 30;

    /**
     * Storage calculation service
     */
    protected StorageCalculationService $storageService;

    /**
     * Mount component
     */
    public function mount(StorageCalculationService $storageService): void
    {
        $this->storageService = $storageService;
        $this->refreshStats();
    }

    /**
     * Refresh storage statistics
     */
    public function refreshStats(): void
    {
        $userId = auth()->id();

        $this->imageStats = $this->storageService->getImageStorageStats($userId);
        $this->videoStats = $this->storageService->getVideoStorageStats($userId);
        $this->combinedStats = $this->storageService->getCombinedStorageStats($userId);
    }

    /**
     * Toggle auto-refresh
     */
    public function toggleAutoRefresh(): void
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    /**
     * Get progress bar color based on percentage
     */
    public function getProgressColor(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'bg-red-500';
        } elseif ($percentage >= 70) {
            return 'bg-yellow-500';
        } elseif ($percentage >= 50) {
            return 'bg-orange-500';
        } else {
            return 'bg-love-500';
        }
    }

    /**
     * Get storage status text
     */
    public function getStatusText(float $percentage): string
    {
        if ($percentage >= 100) {
            return 'Penuh';
        } elseif ($percentage >= 90) {
            return 'Kritis';
        } elseif ($percentage >= 70) {
            return 'Peringatan';
        } elseif ($percentage >= 50) {
            return 'Sedang';
        } else {
            return 'Aman';
        }
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.cloud-storage-stats-component');
    }
}
