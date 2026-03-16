<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ImageGridComponent extends Component
{
    /**
     * Images to display
     */
    public $images;

    /**
     * Mount component
     */
    public function mount($images): void
    {
        $this->images = $images;
    }

    /**
     * Open image modal
     */
    public function openImageModal($imageUuid): void
    {
        $this->emit('openImageModal', $imageUuid);
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.image-grid-component');
    }
}
