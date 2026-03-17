<?php

namespace App\Http\Livewire;

use App\Models\Image;
use App\Services\ImageProcessingService;
use App\Services\StorageCalculationService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Cloud Image Upload Component
 * 
 * Component untuk upload gambar dengan validasi storage
 */
class CloudImageUploadComponent extends Component
{
    use WithFileUploads;

    /**
     * Uploaded images
     */
    public $images = [];

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
     * Image processing service
     */
    protected ImageProcessingService $imageService;

    /**
     * Storage calculation service
     */
    protected StorageCalculationService $storageService;

    /**
     * Mount component
     */
    public function mount(
        ImageProcessingService $imageService,
        StorageCalculationService $storageService
    ): void {
        $this->imageService = $imageService;
        $this->storageService = $storageService;
    }

    /**
     * Updated images property
     */
    public function updatedImages(): void
    {
        $this->validateImages();
        $this->checkStorageCapacity();
    }

    /**
     * Validate images
     */
    protected function validateImages(): void
    {
        $this->uploadErrors = [];

        foreach ($this->images as $index => $image) {
            // Validate file type
            if (!$image->isValid()) {
                $this->uploadErrors[] = "File ke-" . ($index + 1) . " tidak valid";
                continue;
            }

            // Validate MIME type
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($image->getMimeType(), $allowedMimes)) {
                $this->uploadErrors[] = "File ke-" . ($index + 1) . " harus berupa gambar (JPEG, PNG, GIF, WebP)";
            }

            // Validate file size (50MB max)
            if ($image->getSize() > 52428800) {
                $this->uploadErrors[] = "File ke-" . ($index + 1) . " terlalu besar (maksimal 50MB)";
            }
        }

        if (!empty($this->uploadErrors)) {
            $this->images = [];
        }
    }

    /**
     * Check storage capacity
     */
    protected function checkStorageCapacity(): void
    {
        $userId = Auth::id();
        $totalSize = 0;

        foreach ($this->images as $image) {
            $totalSize += $image->getSize();
        }

        $this->storageCheck = $this->storageService->canUploadImage($userId, $totalSize);

        if (!$this->storageCheck['can_upload']) {
            $this->uploadErrors[] = "Storage tidak mencukupi. Kekurangan: {$this->storageCheck['shortage_formatted']}";
            $this->images = [];
        }
    }

    /**
     * Upload images
     */
    public function upload(): void
    {
        if ($this->isUploading || empty($this->images)) {
            return;
        }

        $this->isUploading = true;
        $this->uploadProgress = 0;

        try {
            $userId = Auth::id();
            $totalImages = count($this->images);
            $uploadedCount = 0;

            foreach ($this->images as $image) {
                // Process image
                $imageData = $this->imageService->process($image, $userId);

                // Create image record
                Image::create([
                    'uuid' => $imageData['uuid'],
                    'user_id' => $userId,
                    'filename' => $imageData['filename'],
                    'original_filename' => $imageData['original_filename'],
                    'path' => $imageData['path'],
                    'thumbnail_path' => $imageData['thumbnail_path'],
                    'size' => $imageData['size'],
                    'mime_type' => $imageData['mime_type'],
                    'upload_date' => $imageData['upload_date'],
                    'upload_month' => $imageData['upload_month'],
                ]);

                $uploadedCount++;
                $this->uploadProgress = ($uploadedCount / $totalImages) * 100;
            }

            // Clear storage cache
            $this->storageService->clearCache($userId);

            // Reset form
            $this->images = [];
            $this->uploadProgress = 0;
            $this->uploadErrors = [];
            $this->storageCheck = null;

            // Emit event
            $this->emit('imageUploaded');
            $this->emit('refreshDashboard');

            $this->dispatchBrowserEvent('swal:success', [
                'title' => 'Berhasil',
                'text' => "{$totalImages} gambar berhasil diupload",
            ]);

        } catch (\Exception $e) {
            Log::error('Image upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->uploadErrors[] = 'Terjadi kesalahan saat upload gambar';
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Error',
                'text' => 'Terjadi kesalahan saat upload gambar',
            ]);
        } finally {
            $this->isUploading = false;
        }
    }

    /**
     * Remove image from upload list
     */
    public function removeImage($index): void
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
        $this->checkStorageCapacity();
    }

    /**
     * Cancel upload
     */
    public function cancel(): void
    {
        $this->images = [];
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
        return view('livewire.cloud-image-upload-component');
    }
}
