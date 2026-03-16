<?php

namespace App\Http\Livewire;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ImageIndexComponent extends Component
{
    use WithPagination;

    /**
     * Filter by month
     */
    public $filterMonth = null;

    /**
     * Filter by date range
     */
    public $filterStartDate = null;
    public $filterEndDate = null;

    /**
     * Search query
     */
    public $search = '';

    /**
     * Show upload modal
     */
    public $showUploadModal = false;

    /**
     * Selected image for modal
     */
    public $selectedImage = null;

    /**
     * Show image modal
     */
    public $showImageModal = false;

    /**
     * Event listeners
     */
    protected $listeners = [
        'refreshGrid' => 'refreshGrid',
        'openEditModal' => 'openEditModal',
        'openDeleteModal' => 'openDeleteModal',
    ];

    /**
     * Mount component
     */
    public function mount(): void
    {
        $this->resetFilters();
    }

    /**
     * Reset filters
     */
    public function resetFilters(): void
    {
        $this->filterMonth = null;
        $this->filterStartDate = null;
        $this->filterEndDate = null;
        $this->search = '';
    }

    /**
     * Apply filters
     */
    public function applyFilters(): void
    {
        $this->resetPage();
    }

    /**
     * Refresh grid after upload/delete
     */
    public function refreshGrid(): void
    {
        \Log::info('ImageIndexComponent: refreshGrid called');
        $this->clearCache();
        $this->resetPage();
    }

    /**
     * Open upload modal
     */
    public function openUploadModal(): void
    {
        $this->showUploadModal = true;
    }

    /**
     * Close upload modal
     */
    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
    }

    /**
     * Open image modal
     */
    public function openImageModal($imageUuid): void
    {
        $this->selectedImage = Image::where('uuid', $imageUuid)->first();
        if ($this->selectedImage) {
            $this->showImageModal = true;
            $this->emitTo('image-modal-component', 'openImageModal', $imageUuid);
        }
    }

    /**
     * Close image modal
     */
    public function closeImageModal(): void
    {
        $this->selectedImage = null;
        $this->showImageModal = false;
    }

    /**
     * Open edit modal
     */
    public function openEditModal($imageUuid): void
    {
        $this->selectedImage = Image::where('uuid', $imageUuid)->first();
        if ($this->selectedImage) {
            $this->showImageModal = true;
            // Buka modal terlebih dahulu, lalu aktifkan mode edit
            $this->emitTo('image-modal-component', 'openImageModal', $imageUuid);
            $this->emitTo('image-modal-component', 'enableEditMode');
        }
    }

    /**
     * Open delete modal
     */
    public function openDeleteModal($imageUuid): void
    {
        $this->selectedImage = Image::where('uuid', $imageUuid)->first();
        if ($this->selectedImage) {
            $this->showImageModal = true;
            // Buka modal terlebih dahulu, lalu aktifkan mode hapus
            $this->emitTo('image-modal-component', 'openImageModal', $imageUuid);
            $this->emitTo('image-modal-component', 'enableDeleteMode');
        }
    }

    /**
     * Get images with caching
     */
    public function getImagesProperty()
    {
        $cacheKey = "images_{$this->filterMonth}_{$this->filterStartDate}_{$this->filterEndDate}_{$this->search}_{$this->page}";
        \Log::info('ImageIndexComponent: fetching images', ['cacheKey' => $cacheKey]);

        return Cache::remember($cacheKey, 300, function () {
            \Log::info('ImageIndexComponent: Cache miss, fetching from DB');
            $query = Image::query()
                ->with('user')
                ->orderBy('created_at', 'desc');

            // Apply month filter
            if ($this->filterMonth) {
                $query->byMonth($this->filterMonth);
            }

            // Apply date range filter
            if ($this->filterStartDate && $this->filterEndDate) {
                $query->byDateRange($this->filterStartDate, $this->filterEndDate);
            } elseif ($this->filterStartDate) {
                $query->whereDate('upload_date', '>=', $this->filterStartDate);
            } elseif ($this->filterEndDate) {
                $query->whereDate('upload_date', '<=', $this->filterEndDate);
            }

            // Apply search
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('original_filename', 'like', "%{$this->search}%")
                        ->orWhere('caption', 'like', "%{$this->search}%");
                });
            }

            return $query->paginate(12);
        });
    }

    /**
     * Clear cache when filters change
     */
    public function updatedFilterMonth(): void
    {
        $this->applyFilters();
        $this->clearCache();
    }

    public function updatedFilterStartDate(): void
    {
        $this->applyFilters();
        $this->clearCache();
    }

    public function updatedFilterEndDate(): void
    {
        $this->applyFilters();
        $this->clearCache();
    }

    public function updatedSearch(): void
    {
        $this->applyFilters();
        $this->clearCache();
    }

    /**
     * Clear cache
     */
    protected function clearCache(): void
    {
        // Clear all image cache by flushing the entire cache
        // This is simpler than trying to match wildcard patterns
        Cache::flush();
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('images.index', [
            'images' => $this->images,
            'availableMonths' => $this->getAvailableMonths(),
            'filterMonth' => $this->filterMonth,
            'filterStartDate' => $this->filterStartDate,
            'filterEndDate' => $this->filterEndDate,
            'search' => $this->search,
            'showUploadModal' => $this->showUploadModal,
            'selectedImage' => $this->selectedImage,
        ]);
    }

    /**
     * Get available months from database
     */
    protected function getAvailableMonths(): array
    {
        return Image::selectRaw('DISTINCT upload_month')
            ->orderBy('upload_month', 'desc')
            ->pluck('upload_month')
            ->toArray();
    }

    /**
     * Get images data for DataTables server-side processing
     */
    public function getImagesData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $searchValue = $request->get('search', ['value' => ''])['value'];
        $order = $request->get('order', [0, 'desc']);
        $columns = $request->get('columns', []);

        // Get filter parameters from request
        $filterMonth = $request->get('filterMonth');
        $filterStartDate = $request->get('filterStartDate');
        $filterEndDate = $request->get('filterEndDate');

        // Build query
        $query = Image::query()->with('user');

        // Apply month filter
        if ($filterMonth) {
            $query->byMonth($filterMonth);
        }

        // Apply date range filter
        if ($filterStartDate && $filterEndDate) {
            $query->byDateRange($filterStartDate, $filterEndDate);
        } elseif ($filterStartDate) {
            $query->whereDate('upload_date', '>=', $filterStartDate);
        } elseif ($filterEndDate) {
            $query->whereDate('upload_date', '<=', $filterEndDate);
        }

        // Get total records before search
        $totalRecords = $query->count();

        // Apply search
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('original_filename', 'like', "%{$searchValue}%")
                    ->orWhere('caption', 'like', "%{$searchValue}%");
            });
        }

        // Get total records after search
        $totalFiltered = $query->count();

        // Apply ordering
        if (!empty($order)) {
            $columnIndex = $order[0]['column'];
            $columnSortOrder = $order[0]['dir'];
            $columnName = $columns[$columnIndex]['data'];

            // Map column names to database columns
            $columnMap = [
                'thumbnail' => 'created_at',
                'original_filename' => 'original_filename',
                'caption' => 'caption',
                'upload_date' => 'upload_date',
                'formatted_size' => 'size',
                'actions' => 'created_at',
            ];

            $dbColumn = $columnMap[$columnName] ?? 'created_at';
            $query->orderBy($dbColumn, $columnSortOrder);
        } else {
            // Default order by upload_date desc
            $query->orderBy('upload_date', 'desc');
        }

        // Apply pagination
        $images = $query->offset($start)
            ->limit($length)
            ->get();

        // Format data for DataTables
        $data = $images->map(function ($image) {
            return [
                'thumbnail' => '<img src="' . $image->thumbnail_url . '" alt="' . htmlspecialchars($image->caption ?? '') . '" class="w-16 h-16 object-cover rounded-lg cursor-pointer" onclick="window.dispatchEvent(new CustomEvent(\'openImageModal\', {detail: \'' . $image->uuid . '\'}))">',
                'original_filename' => '<span class="text-love-800">' . htmlspecialchars($image->original_filename) . '</span>',
                'caption' => '<span class="text-love-800">' . htmlspecialchars($image->caption ?? '-') . '</span>',
                'upload_date' => '<span class="text-love-800">' . $image->upload_date->format('d M Y') . '</span>',
                'formatted_size' => '<span class="text-love-800">' . htmlspecialchars($image->formatted_size) . '</span>',
                'actions' => $this->renderActions($image),
            ];
        })->toArray();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Render action buttons for DataTables
     */
    protected function renderActions($image): string
    {
        $uuid = $image->uuid;
        // Using inline styles for colors to bypass Tailwind purging and ensure visibility
        return '<div class="flex gap-2 justify-center items-center" style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
            <button
                onclick="window.dispatchEvent(new CustomEvent(\'openImageModal\', {detail: \'' . $uuid . '\'}))"
                style="padding: 0.5rem; background-color: #f43f5e; color: white; border-radius: 0.75rem; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;"
                onmouseover="this.style.transform=\'scale(1.1)\'"
                onmouseout="this.style.transform=\'scale(1)\'"
                title="Lihat"
            >
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
            <button
                onclick="window.dispatchEvent(new CustomEvent(\'openEditModal\', {detail: \'' . $uuid . '\'}))"
                style="padding: 0.5rem; background-color: #3b82f6; color: white; border-radius: 0.75rem; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;"
                onmouseover="this.style.transform=\'scale(1.1)\'"
                onmouseout="this.style.transform=\'scale(1)\'"
                title="Edit"
            >
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
            <button
                onclick="window.dispatchEvent(new CustomEvent(\'openDeleteModal\', {detail: \'' . $uuid . '\'}))"
                style="padding: 0.5rem; background-color: #ef4444; color: white; border-radius: 0.75rem; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;"
                onmouseover="this.style.transform=\'scale(1.1)\'"
                onmouseout="this.style.transform=\'scale(1)\'"
                title="Hapus"
            >
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>';
    }
}
