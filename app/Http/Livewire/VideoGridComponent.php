<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class VideoGridComponent extends Component
{
    /**
     * Videos to display
     */
    public $videos;

    /**
     * Mount component
     */
    public function mount($videos): void
    {
        $this->videos = $videos;
    }

    /**
     * Open video modal
     */
    public function openVideoModal($videoUuid): void
    {
        $this->emit('openVideoModal', $videoUuid);
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.video-grid-component');
    }
}
