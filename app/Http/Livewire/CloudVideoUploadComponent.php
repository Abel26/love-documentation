<?php

namespace App\Http\Livewire;

use App\Models\Video;
use App\Services\StorageCalculationService;
use App\Services\VideoProcessingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Cloud Video Upload Component
 * 
 * Component untuk upload video dengan validasi storage
 */
class CloudVideoUploadComponent extends Component
{
    use WithFileUploads;

    /**
     * Uploaded videos
     */
    public $videos = [];

    /**
     * Video captions
     */
    public $captions = [];

    /**
     * Upload progress
     */
    public $uploadProgress = 0;

    /**
     * Is uploading
     */
    public $isUploading = false;

    /**
     * Upload errors
     */
    public $uploadErrors = [];

    /**
     * Storage check result
     */
    public $storageCheck = null;

    /**
     * Video processing service
     */
    protected VideoProcessingService $videoService;

    /**
     * Storage calculation service
     */
    protected StorageCalculationService $storageService;

    /**
     * Mount component
     */
    public function mount(
        VideoProcessingService $videoService,
        StorageCalculationService $storageService
    ): void {
        $this->videoService = $videoService;
        $this->storageService = $storageService;
    }

    /**
     * Updated videos property
     */
    public function updatedVideos(): void
    {
        $this->validateVideos();
        $this->checkStorageCapacity();
        $this->initCaptions();
    }

    /**
     * Initialize captions array
     */
    protected function initCaptions(): void
    {
        $this->captions = array_fill(0, count($this->videos), '');
    }

    /**
     * Validate videos
     */
    protected function validateVideos(): void
    {
        $this->uploadErrors = [];

        foreach ($this->videos as $index => $video) {
            // Validate file type
            if (!$video->isValid()) {
                $this->uploadErrors[] = "File ke-" . ($index + 1) . " tidak valid";
                continue;
            }

            // Validate MIME type
            $allowedMimes = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska'];
            if (!in_array($video->getMimeType(), $allowedMimes)) {
                $this->uploadErrors[] = "File ke-" . ($index + 1) . " harus berupa video (MP4, WebM, MOV, AVI, MKV)";
            }

            // Validate file size (1GB max)
            if ($video->getSize() > 1073741824) {
                $this->uploadErrors[] = "File ke-" . ($index + 1) . " terlalu besar (maksimal 1GB)";
            }
        }

        if (!empty($this->uploadErrors)) {
            $this->videos = [];
            $this->captions = [];
        }
    }

    /**
     * Check storage capacity
     */
    protected function checkStorageCapacity(): void
    {
        $userId = Auth::id();
        $totalSize = 0;

        foreach ($this->videos as $video) {
            $totalSize += $video->getSize();
        }

        $this->storageCheck = $this->storageService->canUploadVideo($userId, $totalSize);

        if (!$this->storageCheck['can_upload']) {
            $this->uploadErrors[] = "Storage tidak mencukupi. Kekurangan: " . $this->storageCheck['shortage_formatted'];
            $this->videos = [];
            $this->captions = [];
        }
    }

    /**
     * Upload videos
     */
    public function upload(): void
    {
        if ($this->isUploading || empty($this->videos)) {
            return;
        }

        $this->isUploading = true;
        $this->uploadProgress = 0;

        try {
            $userId = Auth::id();
            $totalVideos = count($this->videos);
            $uploadedCount = 0;

            foreach ($this->videos as $index => $video) {
                // Process video
                $videoData = $this->videoService->process($video, $userId);

                // Create video record
                Video::create([
                    'uuid' => $videoData['uuid'],
                    'user_id' => $userId,
                    'filename' => $videoData['filename'],
                    'original_filename' => $videoData['original_filename'],
                    'path' => $videoData['path'],
                    'thumbnail_path' => $videoData['thumbnail_path'],
                    'size' => $videoData['size'],
                    'mime_type' => $videoData['mime_type'],
                    'upload_date' => $videoData['upload_date'],
                    'upload_month' => $videoData['upload_month'],
                    'caption' => $this->captions[$index] ?? null,
                ]);

                $uploadedCount++;
                $this->uploadProgress = ($uploadedCount / $totalVideos) * 100;
            }

            // Clear storage cache
            $this->storageService->clearCache($userId);

            // Reset form
            $this->videos = [];
            $this->captions = [];
            $this->uploadProgress = 0;
            $this->uploadErrors = [];
            $this->storageCheck = null;

            // Emit event
            $this->emit('videoUploaded');
            $this->emit('refreshDashboard');

            $this->dispatchBrowserEvent('swal:success', [
                'title' => 'Berhasil',
                'text' => "{$totalVideos} video berhasil diupload",
            ]);

        } catch (\Exception $e) {
            Log::error('Video upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->uploadErrors[] = 'Terjadi kesalahan saat upload video';
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Error',
                'text' => 'Terjadi kesalahan saat upload video',
            ]);
        } finally {
            $this->isUploading = false;
        }
    }

    /**
     * Remove video from upload list
     */
    public function removeVideo($index): void
    {
        unset($this->videos[$index]);
        unset($this->captions[$index]);
        $this->videos = array_values($this->videos);
        $this->captions = array_values($this->captions);
        $this->checkStorageCapacity();
    }

    /**
     * Cancel upload
     */
    public function cancel(): void
    {
        $this->videos = [];
        $this->captions = [];
        $this->uploadProgress = 0;
        $this->uploadErrors = [];
        $this->storageCheck = null;
        $this->isUploading = false;
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.cloud-video-upload-component');
    }
}
