<?php

namespace App\Http\Livewire;

use App\Models\ImageGroup;
use App\Models\Video;
use Livewire\Component;

class UserGalleryComponent extends Component
{
    protected $layout = 'layouts.user';

    // Filter Properties
    public $selectedDay = null;
    public $selectedMonth = null;
    public $selectedYear = null;
    public $availableMonths = [];
    public $availableYears = [];

    // Data Properties
    // public $imageGroups; // Removed to avoid hydration issues
    // public $videos;      // Removed to avoid hydration issues
    public $featuredImage;
    public $statistics;

    /**
     * Mount the component
     */
    public function mount()
    {
        $this->loadAvailableFilters();
        $this->loadStatistics();
        $this->loadFeaturedImage();
    }

    /**
     * Load available months and years for filters
     */
    public function loadAvailableFilters()
    {
        $this->availableMonths = ImageGroup::select('event_month')
            ->distinct()
            ->orderBy('event_month')
            ->pluck('event_month')
            ->toArray();

        $this->availableYears = ImageGroup::selectRaw('EXTRACT(YEAR FROM event_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    /**
     * Load statistics
     */
    public function loadStatistics()
    {
        $this->statistics = [
            'total_photos' => \App\Models\Image::count(),
            'total_videos' => Video::count(),
            'total_events' => ImageGroup::count(),
        ];
    }

    /**
     * Load featured image
     */
    public function loadFeaturedImage()
    {
        $this->featuredImage = \App\Models\Image::inRandomOrder()->first();
    }

    /**
     * Get image groups based on filters
     */
    protected function getImageGroupsProperty()
    {
        $query = ImageGroup::with('images');

        // Apply day filter
        if ($this->selectedDay) {
            $query->whereDay('event_date', $this->selectedDay);
        }

        // Apply month filter
        if ($this->selectedMonth) {
            $query->where('event_month', $this->selectedMonth);
        }

        // Apply year filter
        if ($this->selectedYear) {
            $query->whereYear('event_date', $this->selectedYear);
        }

        return $query->orderBy('event_date', 'desc')->get();
    }

    /**
     * Get videos
     */
    protected function getVideosProperty()
    {
        $query = Video::query();

        if ($this->selectedDay) {
            $query->whereDay('upload_date', $this->selectedDay);
        }
        
        if ($this->selectedMonth) {
            // Wait, does upload_date match the event_month string ("10") or integer? event_month is an integer/string
            $query->whereMonth('upload_date', $this->selectedMonth);
        }
        
        if ($this->selectedYear) {
            $query->whereYear('upload_date', $this->selectedYear);
        }

        return $query->orderBy('upload_date', 'desc')->limit(6)->get();
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->selectedDay = null;
        $this->selectedMonth = null;
        $this->selectedYear = null;
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.user-gallery-component', [
            'imageGroups' => $this->getImageGroupsProperty(),
            'videos' => $this->getVideosProperty(),
        ])->layout('layouts.user');
    }
}
