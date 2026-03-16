<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Carbon\Carbon;

class ImageProcessingService
{
    /**
     * Maximum width for main image
     */
    const MAX_WIDTH = 1920;

    /**
     * Thumbnail size (square)
     */
    const THUMBNAIL_SIZE = 300;

    /**
     * Image quality (0-100)
     */
    const QUALITY = 80;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Use Imagick if available, otherwise fallback to GD
        $driver = extension_loaded('imagick') ? 'imagick' : 'gd';
        Image::configure(['driver' => $driver]);
    }

    /**
     * Process uploaded image
     *
     * @param UploadedFile $file
     * @param int $userId
     * @return array
     */
    public function process(UploadedFile $file, int $userId): array
    {
        // Generate UUID and hash filename using SHA-256
        $uuid = $this->generateUuid();
        $hashFilename = $uuid . '.webp';
        
        // Create folder structure based on date
        $date = Carbon::now();
        $folderPath = "uploads/{$date->format('Y')}/{$date->format('m')}";
        
        // Process main image
        $mainImageData = $this->processMainImage($file, $folderPath, $hashFilename);
        
        // Process thumbnail
        $thumbnailData = $this->processThumbnail($file, $folderPath, $hashFilename);
        
        // Get file info
        $fileInfo = $this->getFileInfo($file);
        
        return [
            'uuid' => $uuid,
            'filename' => $hashFilename,
            'original_filename' => $file->getClientOriginalName(),
            'path' => $mainImageData['path'],
            'thumbnail_path' => $thumbnailData['path'],
            'size' => $mainImageData['size'],
            'mime_type' => $fileInfo['mime_type'],
            'upload_date' => $date->toDateString(),
            'upload_month' => $date->format('Y-m'),
        ];
    }

    /**
     * Generate UUID v4
     *
     * @return string
     */
    protected function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant 1

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Generate hash filename using SHA-256 (deprecated, kept for reference)
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateHashFilename(UploadedFile $file): string
    {
        $originalName = $file->getClientOriginalName();
        $timestamp = time();
        $randomString = Str::random(10);
        
        return hash('sha256', $originalName . $timestamp . $randomString) . '.webp';
    }

    /**
     * Process main image (compress, resize, convert to WebP)
     *
     * @param UploadedFile $file
     * @param string $folderPath
     * @param string $hashFilename
     * @return array
     */
    protected function processMainImage(UploadedFile $file, string $folderPath, string $hashFilename): array
    {
        $image = Image::make($file->getRealPath());
        
        // Resize if necessary
        if ($image->width() > self::MAX_WIDTH) {
            $image->resize(self::MAX_WIDTH, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        // Convert to WebP with compression
        $encodedImage = $image->encode('webp', self::QUALITY)->getEncoded();
        
        // Save to storage
        $path = "{$folderPath}/{$hashFilename}";
        Storage::disk('public')->put($path, $encodedImage);
        
        return [
            'path' => $path,
            'size' => strlen($encodedImage),
        ];
    }

    /**
     * Process thumbnail
     *
     * @param UploadedFile $file
     * @param string $folderPath
     * @param string $hashFilename
     * @return array
     */
    protected function processThumbnail(UploadedFile $file, string $folderPath, string $hashFilename): array
    {
        $image = Image::make($file->getRealPath());
        
        // Create square thumbnail
        $image->fit(self::THUMBNAIL_SIZE, self::THUMBNAIL_SIZE, function ($constraint) {
            $constraint->upsize();
        });
        
        // Convert to WebP with compression
        $encodedImage = $image->encode('webp', self::QUALITY)->getEncoded();
        
        // Save to storage
        $thumbnailPath = "{$folderPath}/thumb_{$hashFilename}";
        Storage::disk('public')->put($thumbnailPath, $encodedImage);
        
        return [
            'path' => $thumbnailPath,
        ];
    }

    /**
     * Get file information
     *
     * @param UploadedFile $file
     * @return array
     */
    protected function getFileInfo(UploadedFile $file): array
    {
        try {
            $imageInfo = getimagesize($file->getRealPath());
            
            if ($imageInfo === false) {
                throw new \Exception('Unable to get image information');
            }
            
            return [
                'mime_type' => $imageInfo['mime'] ?? 'image/webp',
                'width' => $imageInfo[0] ?? 0,
                'height' => $imageInfo[1] ?? 0,
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to get file info', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
            
            // Return default values if error occurs
            return [
                'mime_type' => $file->getMimeType(),
                'width' => 0,
                'height' => 0,
            ];
        }
    }

    /**
     * Delete image and thumbnail from storage
     *
     * @param string $path
     * @param string $thumbnailPath
     * @return void
     */
    public function delete(string $path, string $thumbnailPath): void
    {
        Storage::disk('public')->delete($path);
        Storage::disk('public')->delete($thumbnailPath);
    }

    /**
     * Validate image file
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function validate(UploadedFile $file): bool
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
        ];

        // Tidak ada batasan ukuran file di sini, biarkan validasi di Livewire yang menghandle
        // Ukuran file akan dikompresi saat processing

        return in_array($file->getMimeType(), $allowedMimeTypes);
    }
}
