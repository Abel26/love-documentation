<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ImageGroup;
use Illuminate\Support\Collection;

class InteractiveMapComponent extends Component
{
    /**
     * List of image groups with location data
     */
    public Collection $imageGroups;

    /**
     * Selected image group for detail view
     */
    public ?ImageGroup $selectedImageGroup = null;

    /**
     * Map center coordinates
     */
    public float $mapCenterLat = -6.2088; // Default: Jakarta
    public float $mapCenterLng = 106.8456;

    /**
     * Map zoom level
     */
    public int $mapZoom = 12;

    /**
     * Show/hide detail modal
     */
    public bool $showDetailModal = false;

    /**
     * Filter by month
     */
    public ?string $selectedMonth = null;

    /**
     * Filter by year
     */
    public ?string $selectedYear = null;

    /**
     * Available months for filter
     */
    public array $availableMonths = [];

    /**
     * Available years for filter
     */
    public array $availableYears = [];

    /**
     * Initialize component
     */
    public function mount(): void
    {
        $this->loadImageGroups();
        $this->loadAvailableFilters();
        $this->calculateMapCenter();
    }

    /**
     * Load image groups with location data
     */
    public function loadImageGroups(): void
    {
        $query = ImageGroup::query()
            ->where('show_on_map', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['images' => function ($query) {
                $query->limit(1); // Get first image for thumbnail
            }])
            ->orderBy('event_date', 'desc');

        // Apply filters
        if ($this->selectedMonth) {
            $query->where('event_month', $this->selectedMonth);
        }

        if ($this->selectedYear) {
            $query->whereYear('event_date', $this->selectedYear);
        }

        $this->imageGroups = $query->get();
    }

    /**
     * Load available months and years for filter
     */
    public function loadAvailableFilters(): void
    {
        $months = ImageGroup::query()
            ->where('show_on_map', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('event_month')
            ->distinct()
            ->orderBy('event_month')
            ->pluck('event_month')
            ->toArray();

        $this->availableMonths = $months;

        $years = ImageGroup::query()
            ->where('show_on_map', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw('EXTRACT(YEAR FROM event_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $this->availableYears = $years;
    }

    /**
     * Calculate map center based on image groups
     */
    public function calculateMapCenter(): void
    {
        if ($this->imageGroups->isEmpty()) {
            return;
        }

        $totalLat = $this->imageGroups->sum('latitude');
        $totalLng = $this->imageGroups->sum('longitude');
        $count = $this->imageGroups->count();

        $this->mapCenterLat = $totalLat / $count;
        $this->mapCenterLng = $totalLng / $count;
    }

    /**
     * Filter by month
     */
    public function filterByMonth(string $month): void
    {
        $this->selectedMonth = $month;
        $this->loadImageGroups();
        $this->calculateMapCenter();
    }

    /**
     * Filter by year
     */
    public function filterByYear(string $year): void
    {
        $this->selectedYear = $year;
        $this->loadImageGroups();
        $this->calculateMapCenter();
    }

    /**
     * Reset all filters
     */
    public function resetFilters(): void
    {
        $this->selectedMonth = null;
        $this->selectedYear = null;
        $this->loadImageGroups();
        $this->calculateMapCenter();
    }

    /**
     * Show detail for selected image group
     */
    public function showDetail(string $uuid): void
    {
        $this->selectedImageGroup = ImageGroup::query()
            ->with('images')
            ->where('uuid', $uuid)
            ->first();

        if ($this->selectedImageGroup) {
            $this->showDetailModal = true;
        }
    }

    /**
     * Close detail modal
     */
    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedImageGroup = null;
    }

    /**
     * Navigate to image group detail page
     */
    public function goToImageGroup(string $uuid)
    {
        $this->closeDetail();
        return redirect()->route('image-groups.user-view', $uuid);
    }

    /**
     * Get formatted location name
     */
    public function getFormattedLocation(?string $locationName, ?string $locationAddress): string
    {
        if ($locationName && $locationAddress) {
            return $locationName . ' - ' . $locationAddress;
        }

        return $locationName ?? $locationAddress ?? 'Lokasi Tidak Diketahui';
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.interactive-map-component')
            ->layout('layouts.user');
    }
}
