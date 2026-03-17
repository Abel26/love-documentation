<?php
namespace App\Http\Livewire;
use App\Models\Image;
use App\Models\ImageGroup;
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
     * Show gallery modal
     */
    public $showGalleryModal = false;

    /**
     * Selected group UUID for gallery
     */
    public $selectedGroupUuid = null;

    /**
     * Show delete modal
     */
    public $showDeleteModal = false;

    /**
     * Selected group for deletion
     */
    public $selectedGroup = null;

    /**
     * Event listeners
     */
    protected $listeners = [
        'refreshGrid' => 'refreshGrid',
        'openEditModal' => 'openEditModal',
        'openDeleteModal' => 'openDeleteModal',
        'openDeleteGroupModal' => 'openDeleteGroupModal',
        'openGallery' => 'openGallery',
        'galleryModalClosed' => 'closeGalleryModal',
        'imageMoved' => 'refreshGrid',
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
        Log::info('ImageIndexComponent: refreshGrid called');
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
     * Open delete group modal
     */
    public function openDeleteGroupModal($groupUuid): void
    {
        $this->selectedGroup = ImageGroup::where('uuid', $groupUuid)->first();
        if ($this->selectedGroup) {
            $this->showDeleteModal = true;
        }
    }

    /**
     * Close delete modal
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->selectedGroup = null;
    }

    /**
     * Delete group and all images
     */
    public function deleteGroup(): void
    {
        if (!$this->selectedGroup) {
            return;
        }

        // Delete all physical files first
        foreach ($this->selectedGroup->images as $image) {
            \Illuminate\Support\Facades\Storage::delete($image->path);
            \Illuminate\Support\Facades\Storage::delete($image->thumbnail_path);
        }

        // Delete the group (cascade will delete image records)
        $this->selectedGroup->delete();

        $this->selectedGroup = null;
        $this->showDeleteModal = false;

        $this->emit('refreshGrid');

        $this->dispatchBrowserEvent('swal:success', [
            'title' => 'Berhasil',
            'text' => 'Grup dan semua gambar berhasil dihapus'
        ]);
    }

    /**
     * Open gallery modal
     */
    public function openGallery($groupUuid): void
    {
        $this->selectedGroupUuid = $groupUuid;
        $this->showGalleryModal = true;
    }

    /**
     * Close gallery modal
     */
    public function closeGalleryModal(): void
    {
        $this->showGalleryModal = false;
        $this->selectedGroupUuid = null;
    }

    /**
     * Get images with caching
     */
    public function getImagesProperty()
    {
        $cacheKey = "images_{$this->filterMonth}_{$this->filterStartDate}_{$this->filterEndDate}_{$this->search}_{$this->page}";
        Log::info('ImageIndexComponent: fetching images', ['cacheKey' => $cacheKey]);

        return Cache::remember($cacheKey, 300, function () {
            Log::info('ImageIndexComponent: Cache miss, fetching from DB');
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
        return view('livewire.image-index-component', [
            'availableMonths' => $this->getAvailableMonths(),
            'filterMonth' => $this->filterMonth,
            'filterStartDate' => $this->filterStartDate,
            'filterEndDate' => $this->filterEndDate,
            'showUploadModal' => $this->showUploadModal,
            'selectedImage' => $this->selectedImage,
            'showDeleteModal' => $this->showDeleteModal,
            'selectedGroup' => $this->selectedGroup,
            'showGalleryModal' => $this->showGalleryModal,
            'selectedGroupUuid' => $this->selectedGroupUuid,
        ]);
    }

    /**
     * Get available months from database
     */
    protected function getAvailableMonths(): array
    {
        return ImageGroup::selectRaw('DISTINCT event_month')
            ->orderBy('event_month', 'desc')
            ->pluck('event_month')
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

        // Build query for image groups
        $query = ImageGroup::query()->with(['images', 'user']);

        // Apply month filter
        if ($filterMonth) {
            $query->byMonth($filterMonth);
        }

        // Apply date range filter
        if ($filterStartDate && $filterEndDate) {
            $query->byDateRange($filterStartDate, $filterEndDate);
        } elseif ($filterStartDate) {
            $query->whereDate('event_date', '>=', $filterStartDate);
        } elseif ($filterEndDate) {
            $query->whereDate('event_date', '<=', $filterEndDate);
        }

        // Get total records before search
        $totalRecords = $query->count();

        // Apply search
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('caption', 'like', "%{$searchValue}%")
                    ->orWhere('name', 'like', "%{$searchValue}%");
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
                'thumbnails' => 'event_date',
                'caption' => 'caption',
                'event_date' => 'event_date',
                'image_count' => 'image_count',
                'actions' => 'event_date',
            ];

            $dbColumn = $columnMap[$columnName] ?? 'event_date';
            $query->orderBy($dbColumn, $columnSortOrder);
        } else {
            // Default order by event_date desc
            $query->orderBy('event_date', 'desc');
        }

        // Apply pagination
        $groups = $query->offset($start)
            ->limit($length)
            ->get();

        // Format data for DataTables
        $data = $groups->map(function ($group) {
            // Generate thumbnail grid HTML
            $thumbnails = $group->images->take(4)->map(function ($img) {
                return '<img src="' . $img->thumbnail_url . '" class="w-12 h-12 object-cover rounded">';
            })->join('');

            $moreCount = max(0, $group->image_count - 4);
            if ($moreCount > 0) {
                $thumbnails .= '<div class="w-12 h-12 bg-love-200 rounded flex items-center justify-center text-xs text-love-600">+' . $moreCount . '</div>';
            }

            return [
                'thumbnails' => '<div class="flex gap-2">' . $thumbnails . '</div>',
                'caption' => '<span class="text-love-800">' . htmlspecialchars($group->caption ?? '-') . '</span>',
                'event_date' => '<span class="text-love-800">' . $group->event_date->format('d M Y') . '</span>',
                'image_count' => '<span class="text-love-800">' . $group->image_count . ' gambar</span>',
                'actions' => $this->renderGroupActions($group),
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
    protected function renderGroupActions($group): string
    {
        $uuid = $group->uuid;
        // Using inline styles for colors to bypass Tailwind purging and ensure visibility
        // Added pointer-events: auto to ensure buttons are clickable
        return '<div class="flex gap-2 justify-center items-center" style="display: flex; gap: 0.5rem; justify-content: center; align-items: center; pointer-events: auto;">
            <button
                onclick="window.dispatchEvent(new CustomEvent(\'openGallery\', {detail: \'' . $uuid . '\'}))"
                style="padding: 0.5rem; background-color: #6366f1; color: white; border-radius: 0.75rem; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;"
                onmouseover="this.style.transform=\'scale(1.1)\'"
                onmouseout="this.style.transform=\'scale(1)\'"
                title="Lihat Semua Gambar">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </button>
            <a href="' . route('image-groups.download', $uuid) . '"
               style="padding: 0.5rem; background-color: #3b82f6; color: white; border-radius: 0.75rem; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s; text-decoration: none;"
               onmouseover="this.style.transform=\'scale(1.1)\'"
               onmouseout="this.style.transform=\'scale(1)\'"
               title="Download ZIP">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </a>
            <button
                onclick="window.dispatchEvent(new CustomEvent(\'openDeleteGroupModal\', {detail: \'' . $uuid . '\'}))"
                style="padding: 0.5rem; background-color: #ef4444; color: white; border-radius: 0.75rem; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;"
                onmouseover="this.style.transform=\'scale(1.1)\'"
                onmouseout="this.style.transform=\'scale(1)\'"
                title="Hapus Grup">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>';
    }
}
