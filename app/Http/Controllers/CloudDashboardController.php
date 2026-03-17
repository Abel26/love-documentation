<?php

namespace App\Http\Controllers;

use App\Services\StorageCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Cloud Dashboard Controller
 * 
 * Controller untuk menampilkan dashboard cloud server
 * dengan monitoring storage dan statistik
 */
class CloudDashboardController extends Controller
{
    /**
     * Storage calculation service instance
     */
    protected StorageCalculationService $storageService;

    /**
     * Constructor
     */
    public function __construct(StorageCalculationService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * Display the cloud dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get storage statistics
        $imageStats = $this->storageService->getImageStorageStats($userId);
        $videoStats = $this->storageService->getVideoStorageStats($userId);
        $combinedStats = $this->storageService->getCombinedStorageStats($userId);
        
        // Get storage trends
        $storageTrends = $this->storageService->getStorageTrends($userId, 30);
        
        return view('cloud-dashboard.index', compact(
            'imageStats',
            'videoStats',
            'combinedStats',
            'storageTrends'
        ));
    }

    /**
     * Get storage statistics via AJAX
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStorageStats(Request $request)
    {
        $userId = Auth::id();
        $type = $request->input('type', 'combined');
        
        switch ($type) {
            case 'images':
                $stats = $this->storageService->getImageStorageStats($userId);
                break;
            case 'videos':
                $stats = $this->storageService->getVideoStorageStats($userId);
                break;
            case 'combined':
            default:
                $stats = $this->storageService->getCombinedStorageStats($userId);
                break;
        }
        
        return response()->json($stats);
    }

    /**
     * Check if user can upload file
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUploadCapacity(Request $request)
    {
        $userId = Auth::id();
        $fileType = $request->input('file_type', 'image');
        $fileSize = (int) $request->input('file_size', 0);
        
        if ($fileType === 'image') {
            $result = $this->storageService->canUploadImage($userId, $fileSize);
        } else {
            $result = $this->storageService->canUploadVideo($userId, $fileSize);
        }
        
        return response()->json($result);
    }

    /**
     * Refresh storage cache
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshCache()
    {
        $userId = Auth::id();
        $this->storageService->clearCache($userId);
        
        return response()->json([
            'success' => true,
            'message' => 'Cache refreshed successfully',
        ]);
    }
}
