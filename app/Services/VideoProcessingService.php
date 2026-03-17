<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VideoProcessingService
{
    /**
     * Maximum width for video
     */
    const MAX_WIDTH = 1920;

    /**
     * Maximum height for video
     */
    const MAX_HEIGHT = 1080;

    /**
     * Thumbnail size (square)
     */
    const THUMBNAIL_SIZE = 300;

    /**
     * CRF (Constant Rate Factor) for compression
     * Lower = better quality, larger file
     * Higher = lower quality, smaller file
     * Range: 0-51, recommended: 18-28
     */
    const CRF = 26;

    /**
     * Audio bitrate (kbps)
     */
    const AUDIO_BITRATE = 128;

    /**
     * FFmpeg binary path
     */
    protected $ffmpegPath;

    /**
     * FFprobe binary path
     */
    protected $ffprobePath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ffmpegPath = config('ffmpeg.ffmpeg.binaries', '/usr/bin/ffmpeg');
        $this->ffprobePath = config('ffmpeg.ffprobe.binaries', '/usr/bin/ffprobe');
    }

    /**
     * Get compression settings from config
     */
    protected function getCompressionSettings(): array
    {
        return [
            'crf' => config('ffmpeg.compression.crf', self::CRF),
            'preset' => config('ffmpeg.compression.preset', 'medium'),
            'max_width' => config('ffmpeg.compression.max_width', self::MAX_WIDTH),
            'max_height' => config('ffmpeg.compression.max_height', self::MAX_HEIGHT),
            'audio_bitrate' => config('ffmpeg.compression.audio_bitrate', self::AUDIO_BITRATE),
        ];
    }

    /**
     * Get thumbnail settings from config
     */
    protected function getThumbnailSettings(): array
    {
        return [
            'size' => config('ffmpeg.thumbnail.size', self::THUMBNAIL_SIZE),
            'time' => config('ffmpeg.thumbnail.time', '00:00:01'),
            'format' => config('ffmpeg.thumbnail.format', 'jpg'),
        ];
    }

    /**
     * Process uploaded video
     *
     * @param UploadedFile $file
     * @param int $userId
     * @return array
     */
    public function process(UploadedFile $file, int $userId): array
    {
        // Generate UUID and filename
        $uuid = $this->generateUuid();
        $filename = $uuid . '.mp4';
        
        // Create folder structure based on date
        $date = Carbon::now();
        $folderPath = "uploads/{$date->format('Y')}/{$date->format('m')}";
        
        // Process main video (compress)
        $mainVideoData = $this->processMainVideo($file, $folderPath, $filename);
        
        // Process thumbnail
        $thumbnailData = $this->processThumbnail($file, $folderPath, $filename);
        
        // Get file info
        $fileInfo = $this->getFileInfo($file);
        
        return [
            'uuid' => $uuid,
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'path' => $mainVideoData['path'],
            'thumbnail_path' => $thumbnailData['path'],
            'size' => $mainVideoData['size'],
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
     * Process main video (compress, convert to MP4)
     *
     * @param UploadedFile $file
     * @param string $folderPath
     * @param string $filename
     * @return array
     */
    protected function processMainVideo(UploadedFile $file, string $folderPath, string $filename): array
    {
        $tempInputPath = $file->getRealPath();
        $tempOutputPath = tempnam(sys_get_temp_dir(), 'video_') . '.mp4';

        try {
            // Get compression settings
            $settings = $this->getCompressionSettings();

            // Get video dimensions using ffprobe
            $videoInfo = $this->getVideoInfo($tempInputPath);
            $width = $videoInfo['width'] ?? 0;
            $height = $videoInfo['height'] ?? 0;

            // Calculate new dimensions if needed
            $newWidth = $width;
            $newHeight = $height;
            $scaleFilter = '';

            if ($width > $settings['max_width'] || $height > $settings['max_height']) {
                $ratio = min($settings['max_width'] / $width, $settings['max_height'] / $height);
                $newWidth = (int) round($width * $ratio);
                $newHeight = (int) round($height * $ratio);
                $scaleFilter = "-vf scale={$newWidth}:{$newHeight}";
            }

            // Build FFmpeg command for compression
            $command = escapeshellcmd($this->ffmpegPath) . ' ' .
                '-i ' . escapeshellarg($tempInputPath) . ' ' .
                '-c:v libx264 ' .
                '-crf ' . $settings['crf'] . ' ' .
                '-preset ' . $settings['preset'] . ' ' .
                '-c:a aac ' .
                '-b:a ' . $settings['audio_bitrate'] . 'k ' .
                '-movflags +faststart ' .
                $scaleFilter . ' ' .
                '-y ' .
                escapeshellarg($tempOutputPath) . ' ' .
                '2>&1';

            // Execute FFmpeg command
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('FFmpeg compression failed: ' . implode("\n", $output));
            }

            // Get compressed file size
            $compressedSize = filesize($tempOutputPath);

            // Save to storage
            $path = "{$folderPath}/{$filename}";
            Storage::disk('public')->put($path, file_get_contents($tempOutputPath));

            return [
                'path' => $path,
                'size' => $compressedSize,
            ];
        } finally {
            // Clean up temp file
            if (file_exists($tempOutputPath)) {
                unlink($tempOutputPath);
            }
        }
    }

    /**
     * Process thumbnail
     *
     * @param UploadedFile $file
     * @param string $folderPath
     * @param string $filename
     * @return array
     */
    protected function processThumbnail(UploadedFile $file, string $folderPath, string $filename): array
    {
        $tempInputPath = $file->getRealPath();
        $tempThumbnailPath = tempnam(sys_get_temp_dir(), 'thumb_') . '.' . $this->getThumbnailSettings()['format'];

        try {
            // Get thumbnail settings
            $settings = $this->getThumbnailSettings();

            // Build FFmpeg command for thumbnail extraction
            $command = escapeshellcmd($this->ffmpegPath) . ' ' .
                '-i ' . escapeshellarg($tempInputPath) . ' ' .
                '-ss ' . $settings['time'] . ' ' .
                '-vframes 1 ' .
                '-vf scale=' . $settings['size'] . ':-1 ' .
                '-y ' .
                escapeshellarg($tempThumbnailPath) . ' ' .
                '2>&1';

            // Execute FFmpeg command
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('FFmpeg thumbnail extraction failed: ' . implode("\n", $output));
            }

            // Save to storage
            $thumbnailPath = "{$folderPath}/thumb_{$filename}." . $settings['format'];
            Storage::disk('public')->put($thumbnailPath, file_get_contents($tempThumbnailPath));

            return [
                'path' => $thumbnailPath,
            ];
        } finally {
            // Clean up temp file
            if (file_exists($tempThumbnailPath)) {
                unlink($tempThumbnailPath);
            }
        }
    }

    /**
     * Get video information using ffprobe
     *
     * @param string $filePath
     * @return array
     */
    protected function getVideoInfo(string $filePath): array
    {
        $command = escapeshellcmd($this->ffprobePath) . ' ' .
            '-v quiet ' .
            '-print_format json ' .
            '-show_format ' .
            '-show_streams ' .
            escapeshellarg($filePath) . ' ' .
            '2>&1';

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            return [
                'width' => 0,
                'height' => 0,
                'duration' => 0,
            ];
        }

        $jsonOutput = implode("\n", $output);
        $data = json_decode($jsonOutput, true);

        if (!isset($data['streams'])) {
            return [
                'width' => 0,
                'height' => 0,
                'duration' => 0,
            ];
        }

        // Find video stream
        $videoStream = null;
        foreach ($data['streams'] as $stream) {
            if (isset($stream['codec_type']) && $stream['codec_type'] === 'video') {
                $videoStream = $stream;
                break;
            }
        }

        return [
            'width' => $videoStream['width'] ?? 0,
            'height' => $videoStream['height'] ?? 0,
            'duration' => $data['format']['duration'] ?? 0,
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
            $tempInputPath = $file->getRealPath();
            $videoInfo = $this->getVideoInfo($tempInputPath);
            
            return [
                'mime_type' => $file->getMimeType(),
                'duration' => $videoInfo['duration'],
                'width' => $videoInfo['width'],
                'height' => $videoInfo['height'],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get video info', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
            
            // Return default values if error occurs
            return [
                'mime_type' => $file->getMimeType(),
                'duration' => 0,
                'width' => 0,
                'height' => 0,
            ];
        }
    }

    /**
     * Delete video and thumbnail from storage
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
     * Validate video file
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function validate(UploadedFile $file): bool
    {
        $allowedMimeTypes = [
            'video/mp4',
            'video/webm',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-matroska',
        ];

        return in_array($file->getMimeType(), $allowedMimeTypes);
    }
}
