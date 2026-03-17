<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FFmpeg Binaries
    |--------------------------------------------------------------------------
    |
    | Path to FFmpeg and FFprobe binaries on your system.
    | You can find these by running: which ffmpeg and which ffprobe
    |
    */

    'ffmpeg' => [
        'binaries' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
        'threads' => env('FFMPEG_THREADS', 12),
        'timeout' => env('FFMPEG_TIMEOUT', 3600), // 1 hour timeout for large videos
    ],

    'ffprobe' => [
        'binaries' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),
        'timeout' => env('FFPROBE_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Video Compression Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for video compression
    |
    */

    'compression' => [
        'crf' => env('FFMPEG_CRF', 26), // Constant Rate Factor (0-51, lower = better quality)
        'preset' => env('FFMPEG_PRESET', 'medium'), // ultrafast, superfast, veryfast, faster, fast, medium, slow, slower, veryslow
        'max_width' => env('FFMPEG_MAX_WIDTH', 1920),
        'max_height' => env('FFMPEG_MAX_HEIGHT', 1080),
        'audio_bitrate' => env('FFMPEG_AUDIO_BITRATE', 128), // kbps
    ],

    /*
    |--------------------------------------------------------------------------
    | Thumbnail Settings
    |--------------------------------------------------------------------------
    |
    | Settings for video thumbnail generation
    |
    */

    'thumbnail' => [
        'size' => env('FFMPEG_THUMBNAIL_SIZE', 300),
        'time' => env('FFMPEG_THUMBNAIL_TIME', '00:00:01'), // Time to extract frame
        'format' => env('FFMPEG_THUMBNAIL_FORMAT', 'jpg'),
    ],
];
