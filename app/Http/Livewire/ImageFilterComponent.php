<?php

namespace App\Http\Livewire;

use App\Models\Image;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ImageFilterComponent extends Component
{
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
     * Available months for filter
     */
    public $availableMonths = [];

    /**
     * Mount component
     */
    public function mount(): void
    {
        $this->loadAvailableMonths();
    }

    /**
     * Load available months from database
     */
    protected function loadAvailableMonths(): void
    {
        $this->availableMonths = Image::select('upload_month')
            ->distinct()
            ->orderBy('upload_month', 'desc')
            ->pluck('upload_month')
            ->toArray();
    }

    /**
     * Apply month filter
     */
    public function applyMonthFilter(): void
    {
        $this->emit('filterChanged', [
            'filterMonth' => $this->filterMonth,
            'filterStartDate' => null,
            'filterEndDate' => null,
        ]);
    }

    /**
     * Apply date range filter
     */
    public function applyDateFilter(): void
    {
        $this->emit('filterChanged', [
            'filterMonth' => null,
            'filterStartDate' => $this->filterStartDate,
            'filterEndDate' => $this->filterEndDate,
        ]);
    }

    /**
     * Reset all filters
     */
    public function resetFilters(): void
    {
        $this->filterMonth = null;
        $this->filterStartDate = null;
        $this->filterEndDate = null;

        $this->emit('filterChanged', [
            'filterMonth' => null,
            'filterStartDate' => null,
            'filterEndDate' => null,
        ]);
    }

    /**
     * Get month name in Indonesian
     */
    public function getMonthName(string $month): string
    {
        $date = \DateTime::createFromFormat('Y-m', $month);
        $monthName = $date->format('F');
        
        $indonesianMonths = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];

        return $indonesianMonths[$monthName] ?? $monthName . ' ' . $date->format('Y');
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.image-filter-component');
    }
}
