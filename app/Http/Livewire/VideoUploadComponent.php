<?php

namespace App\Http\Livewire;

use App\Models\Video;
use App\Services\VideoProcessingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class VideoUploadComponent extends Component
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
     * Mount component
     */
    public function mount(): void
    {
        // Mount method tidak perlu melakukan apa-apa
    }

    /**
     * Get video processing service
     */
    protected function getVideoProcessingService(): VideoProcessingService
    {
        return app(VideoProcessingService::class);
    }

    /**
     * Validate videos
     */
    protected function rules(): array
    {
        return [
            'videos.*' => [
                'required',
                'mimes:mp4,webm,mov,avi,mkv',
                'max:1048576', // 1GB in KB
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'videos.*.required' => 'File video harus dipilih.',
            'videos.*.mimes' => 'Format file harus berupa MP4, WebM, MOV, AVI, atau MKV.',
            'videos.*.max' => 'Ukuran file video maksimal 1GB.',
        ];
    }

    /**
     * Confirm upload
     */
    public function confirmUpload(): void
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => 'Upload Video',
            'text' => 'Apakah Anda yakin ingin mengupload ' . count($this->videos) . ' video?',
            'icon' => 'question',
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, Upload',
            'cancelButtonText' => 'Batal',
            'confirmMethod' => 'upload',
            'componentId' => $this->id
        ]);
    }

    /**
     * Upload videos
     */
    public function upload(): void
    {
        Log::info('Starting video upload process', ['videos_count' => count($this->videos)]);
        
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
        $totalVideos = count($this->videos);
        $uploadedVideos = [];

        foreach ($this->videos as $index => $video) {
            try {
                Log::info('Processing video', [
                    'index' => $index,
                    'filename' => $video->getClientOriginalName(),
                    'size' => $video->getSize(),
                    'mime_type' => $video->getMimeType(),
                ]);
                
                // Validate video
                $videoProcessingService = $this->getVideoProcessingService();
                if (!$videoProcessingService->validate($video)) {
                    $this->uploadErrors[] = "File {$video->getClientOriginalName()} tidak valid. Format yang didukung: MP4, WebM, MOV, AVI, MKV";
                    Log::warning('Video validation failed', ['filename' => $video->getClientOriginalName()]);
                    continue;
                }

                // Process video (compress + thumbnail)
                $videoData = $videoProcessingService->process($video, $userId);
                
                Log::info('Video processed successfully', [
                    'filename' => $videoData['filename'],
                    'size' => $videoData['size'],
                ]);

                // Create video record
                $uploadedVideo = Video::create([
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

                $uploadedVideos[] = $uploadedVideo;

                // Update progress
                $this->uploadProgress = round((($index + 1) / $totalVideos) * 100);
            } catch (\Exception $e) {
                Log::error('Failed to upload video', [
                    'filename' => $video->getClientOriginalName(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $this->uploadErrors[] = "Gagal upload {$video->getClientOriginalName()}: {$e->getMessage()}";
            }
        }

        // Clear uploaded videos
        $this->videos = [];
        $this->captions = [];

        // Reset upload state
        $this->isUploading = false;
        $this->uploadProgress = 100;

        // Emit event to parent
        $this->emit('uploadComplete', count($uploadedVideos));
        $this->emit('refreshGrid');

        // Show success/error message
        if (count($uploadedVideos) > 0 && count($this->uploadErrors) === 0) {
            $this->dispatchBrowserEvent('swal:success', [
                'title' => 'Berhasil',
                'text' => count($uploadedVideos) . ' video berhasil diupload'
            ]);
        }

        if (count($this->uploadErrors) > 0) {
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Gagal',
                'text' => 'Gagal mengupload ' . count($this->uploadErrors) . ' video. Silakan cek kembali file yang gagal.'
            ]);
        }
    }

    /**
     * Remove video from queue
     */
    public function removeVideo(int $index): void
    {
        unset($this->videos[$index]);
        unset($this->captions[$index]);
        $this->videos = array_values($this->videos);
        $this->captions = array_values($this->captions);
    }

    /**
     * Reset upload form
     */
    public function resetForm(): void
    {
        $this->videos = [];
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
        return view('livewire.video-upload-component');
    }
}
