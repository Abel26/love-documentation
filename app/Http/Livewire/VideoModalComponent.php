<?php

namespace App\Http\Livewire;

use App\Models\Video;
use App\Services\VideoProcessingService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class VideoModalComponent extends Component
{
    /**
     * Show modal
     */
    public $showModal = false;

    /**
     * Video to display
     */
    public $video = null;

    /**
     * Edit mode
     */
    public $isEditing = false;

    /**
     * Caption for edit
     */
    public $caption = '';

    /**
     * Show delete confirmation
     */
    public $showDeleteConfirm = false;

    /**
     * Video processing service
     */
    protected $videoProcessingService;

    /**
     * Mount component
     */
    public function mount(): void
    {
        $this->videoProcessingService = new VideoProcessingService();
    }

    /**
     * Listen for open video modal event
     */
    protected $listeners = [
        'openVideoModal' => 'openModal',
        'closeVideoModal' => 'closeModal',
        'enableEditMode' => 'enableEditMode',
        'enableDeleteMode' => 'enableDeleteMode',
    ];

    /**
     * Open modal with video
     */
    public function openModal($videoUuid): void
    {
        $this->video = Video::with('user')->where('uuid', $videoUuid)->first();
        $this->caption = $this->video->caption ?? '';
        $this->isEditing = false;
        $this->showDeleteConfirm = false;
        $this->showModal = true;
    }

    /**
     * Close modal
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->video = null;
        $this->caption = '';
        $this->isEditing = false;
        $this->showDeleteConfirm = false;
    }

    /**
     * Enable edit mode
     */
    public function enableEditMode(): void
    {
        $this->isEditing = true;
    }

    /**
     * Disable edit mode
     */
    public function disableEditMode(): void
    {
        $this->isEditing = false;
        $this->caption = $this->video->caption ?? '';
    }

    /**
     * Enable delete mode
     */
    public function enableDeleteMode(): void
    {
        $this->showDeleteConfirm = true;
    }

    /**
     * Confirm update caption
     */
    public function confirmUpdateCaption(): void
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => 'Update Caption',
            'text' => 'Apakah Anda yakin ingin mengubah caption video ini?',
            'icon' => 'question',
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, Update',
            'cancelButtonText' => 'Batal',
            'confirmMethod' => 'updateCaption',
            'componentId' => $this->id
        ]);
    }

    /**
     * Update caption
     */
    public function updateCaption(): void
    {
        $this->validate([
            'caption' => 'nullable|string|max:500',
        ]);

        $this->video->update([
            'caption' => $this->caption,
        ]);

        $this->isEditing = false;
        
        $this->dispatchBrowserEvent('swal:success', [
            'title' => 'Berhasil',
            'text' => 'Caption berhasil diupdate'
        ]);
        
        $this->emit('refreshGrid');
    }

    /**
     * Show delete confirmation
     */
    public function showDeleteConfirmation(): void
    {
        $this->showDeleteConfirm = true;
    }

    /**
     * Hide delete confirmation
     */
    public function hideDeleteConfirmation(): void
    {
        $this->showDeleteConfirm = false;
    }

    /**
     * Confirm delete video
     */
    public function confirmDeleteVideo(): void
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => 'Hapus Video',
            'text' => 'Apakah Anda yakin ingin menghapus video ini? Tindakan ini tidak dapat dibatalkan.',
            'icon' => 'warning',
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, Hapus',
            'cancelButtonText' => 'Batal',
            'confirmMethod' => 'deleteVideo',
            'componentId' => $this->id
        ]);
    }

    /**
     * Delete video
     */
    public function deleteVideo(): void
    {
        try {
            // Delete files from storage
            $this->videoProcessingService->delete(
                $this->video->path,
                $this->video->thumbnail_path
            );

            // Delete record from database
            $this->video->delete();

            // Emit events
            $this->emit('videoDeleted');
            $this->emit('refreshGrid');

            // Close modal
            $this->closeModal();

            $this->dispatchBrowserEvent('swal:success', [
                'title' => 'Berhasil',
                'text' => 'Video berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Gagal',
                'text' => 'Gagal menghapus video: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download video
     */
    public function downloadVideo()
    {
        return response()->download(
            storage_path('app/public/' . $this->video->path),
            $this->video->original_filename
        );
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.video-modal-component');
    }
}
