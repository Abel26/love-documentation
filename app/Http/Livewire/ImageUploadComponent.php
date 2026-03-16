<?php

namespace App\Http\Livewire;

use App\Models\Image;
use App\Services\ImageProcessingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageUploadComponent extends Component
{
    use WithFileUploads;

    /**
     * Uploaded images
     */
    public $images = [];

    /**
     * Image captions
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
     * Mount component
     */
    public function mount(): void
    {
        // Mount method tidak perlu melakukan apa-apa
    }

    /**
     * Get image processing service
     */
    protected function getImageProcessingService(): ImageProcessingService
    {
        return app(ImageProcessingService::class);
    }

    /**
     * Validate images
     */
    protected function rules(): array
    {
        return [
            'images.*' => [
                'required',
                'image',
                'max:51200', // 50MB
                'mimes:jpeg,jpg,png,gif,webp',
            ],
        ];
    }

    /**
     * Confirm upload
     */
    public function confirmUpload(): void
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => 'Upload Foto',
            'text' => 'Apakah Anda yakin ingin mengupload ' . count($this->images) . ' foto?',
            'icon' => 'question',
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, Upload',
            'cancelButtonText' => 'Batal',
            'confirmMethod' => 'upload',
            'componentId' => $this->id
        ]);
    }

    /**
     * Upload images
     */
    public function upload(): void
    {
        Log::info('Starting upload process', ['images_count' => count($this->images)]);
        
        $this->isUploading = true;
        $this->uploadProgress = 0;
        $this->uploadErrors = [];

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            $this->isUploading = false;
            throw $e;
        }

        $userId = Auth::id();
        $totalImages = count($this->images);
        $uploadedImages = [];

        foreach ($this->images as $index => $image) {
            try {
                Log::info('Processing image', [
                    'index' => $index,
                    'filename' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                ]);
                
                // Validate image
                $imageProcessingService = $this->getImageProcessingService();
                if (!$imageProcessingService->validate($image)) {
                    $this->uploadErrors[] = "File {$image->getClientOriginalName()} tidak valid. Format yang didukung: JPEG, PNG, GIF, WebP";
                    Log::warning('Image validation failed', ['filename' => $image->getClientOriginalName()]);
                    continue;
                }

                // Process image
                $imageData = $imageProcessingService->process($image, $userId);
                
                Log::info('Image processed successfully', [
                    'filename' => $imageData['filename'],
                    'size' => $imageData['size'],
                ]);

                // Create image record
                $uploadedImage = Image::create([
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
                    'caption' => $this->captions[$index] ?? null,
                ]);

                $uploadedImages[] = $uploadedImage;

                // Update progress
                $this->uploadProgress = round((($index + 1) / $totalImages) * 100);
            } catch (\Exception $e) {
                Log::error('Failed to upload image', [
                    'filename' => $image->getClientOriginalName(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $this->uploadErrors[] = "Gagal upload {$image->getClientOriginalName()}: {$e->getMessage()}";
            }
        }

        // Clear uploaded images
        $this->images = [];
        $this->captions = [];

        // Reset upload state
        $this->isUploading = false;
        $this->uploadProgress = 100;

        // Emit event to parent
        $this->emit('uploadComplete', count($uploadedImages));
        $this->emit('refreshGrid');

        // Show success/error message
        if (count($uploadedImages) > 0 && count($this->uploadErrors) === 0) {
            $this->dispatchBrowserEvent('swal:success', [
                'title' => 'Berhasil',
                'text' => count($uploadedImages) . ' gambar berhasil diupload'
            ]);
        }

        if (count($this->uploadErrors) > 0) {
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Gagal',
                'text' => 'Gagal mengupload ' . count($this->uploadErrors) . ' gambar. Silakan cek kembali file yang gagal.'
            ]);
        }
    }

    /**
     * Remove image from queue
     */
    public function removeImage(int $index): void
    {
        unset($this->images[$index]);
        unset($this->captions[$index]);
        $this->images = array_values($this->images);
        $this->captions = array_values($this->captions);
    }

    /**
     * Reset upload form
     */
    public function resetForm(): void
    {
        $this->images = [];
        $this->captions = [];
        $this->uploadProgress = 0;
        $this->uploadErrors = [];
    }

    /**
     * Get temporary URL for preview
     */
    public function getTemporaryUrl(UploadedFile $file): string
    {
        return $file->temporaryUrl();
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.image-upload-component');
    }
}
