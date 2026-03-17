<?php

if (!function_exists('formatBytes')) {
    /**
     * Format bytes to human-readable format
     *
     * @param int $bytes Size in bytes
     * @param int $precision Decimal precision
     * @return string
     */
    function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('calculatePercentage')) {
    /**
     * Calculate percentage
     *
     * @param int $value Current value
     * @param int $max Maximum value
     * @param int $precision Decimal precision
     * @return float
     */
    function calculatePercentage(int $value, int $max, int $precision = 2): float
    {
        if ($max == 0) {
            return 0;
        }

        $percentage = ($value / $max) * 100;
        return min(100, max(0, round($percentage, $precision)));
    }
}

if (!function_exists('getStorageStatusColor')) {
    /**
     * Get storage status color based on percentage
     *
     * @param float $percentage Storage percentage
     * @return string
     */
    function getStorageStatusColor(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'red'; // Near limit
        } elseif ($percentage >= 70) {
            return 'yellow'; // Warning
        } elseif ($percentage >= 50) {
            return 'orange'; // Moderate
        } else {
            return 'green'; // Good
        }
    }
}

if (!function_exists('getStorageStatusText')) {
    /**
     * Get storage status text based on percentage
     *
     * @param float $percentage Storage percentage
     * @return string
     */
    function getStorageStatusText(float $percentage): string
    {
        if ($percentage >= 100) {
            return 'Storage Penuh';
        } elseif ($percentage >= 90) {
            return 'Hampir Penuh';
        } elseif ($percentage >= 70) {
            return 'Peringatan';
        } elseif ($percentage >= 50) {
            return 'Sedang';
        } else {
            return 'Aman';
        }
    }
}

if (!function_exists('formatFileSize')) {
    /**
     * Format file size with icon
     *
     * @param int $bytes Size in bytes
     * @return string
     */
    function formatFileSize(int $bytes): string
    {
        $formatted = formatBytes($bytes);
        
        if ($bytes < 1024) {
            return "📄 {$formatted}";
        } elseif ($bytes < 1048576) {
            return "📋 {$formatted}";
        } elseif ($bytes < 1073741824) {
            return "📁 {$formatted}";
        } else {
            return "💾 {$formatted}";
        }
    }
}

if (!function_exists('convertToBytes')) {
    /**
     * Convert human-readable size to bytes
     *
     * @param string $size Human-readable size (e.g., "10MB", "1GB")
     * @return int
     */
    function convertToBytes(string $size): int
    {
        $size = strtoupper(trim($size));
        $units = ['B' => 1, 'KB' => 1024, 'MB' => 1048576, 'GB' => 1073741824, 'TB' => 1099511627776];
        
        $value = (float) preg_replace('/[^0-9.]/', '', $size);
        $unit = preg_replace('/[^A-Z]/', '', $size);
        
        return isset($units[$unit]) ? (int) ($value * $units[$unit]) : (int) $value;
    }
}

if (!function_exists('getMaxImageStorage')) {
    /**
     * Get maximum image storage limit
     *
     * @return int
     */
    function getMaxImageStorage(): int
    {
        return 10737418240; // 10GB
    }
}

if (!function_exists('getMaxVideoStorage')) {
    /**
     * Get maximum video storage limit
     *
     * @return int
     */
    function getMaxVideoStorage(): int
    {
        return 10737418240; // 10GB
    }
}

if (!function_exists('getMaxImageStorageFormatted')) {
    /**
     * Get maximum image storage limit formatted
     *
     * @return string
     */
    function getMaxImageStorageFormatted(): string
    {
        return formatBytes(getMaxImageStorage());
    }
}

if (!function_exists('getMaxVideoStorageFormatted')) {
    /**
     * Get maximum video storage limit formatted
     *
     * @return string
     */
    function getMaxVideoStorageFormatted(): string
    {
        return formatBytes(getMaxVideoStorage());
    }
}
