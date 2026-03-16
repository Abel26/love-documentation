<?php

namespace App\Http\Livewire;

use App\Models\Image;
use App\Services\ImageProcessingService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ImageModalComponent extends Component
{
    /**
     * Show modal
     */
    public $showModal = false;

    /**
     * Image to display
     */
    public $image = null;

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
     * Image processing service
     */
    protected $imageProcessingService;

    /**
     * Mount component
     */
    public function mount(): void
    {
        $this->imageProcessingService = new ImageProcessingService();
    }

    /**
     * Listen for open image modal event
     */
    protected $listeners = [
        'openImageModal' => 'openModal',
        'closeImageModal' => 'closeModal',
        'enableEditMode' => 'enableEditMode',
        'enableDeleteMode' => 'enableDeleteMode',
    ];

    /**
     * Open modal with image
     */
    public function openModal($imageUuid): void
    {
        $this->image = Image::with('user')->where('uuid', $imageUuid)->first();
        $this->caption = $this->image->caption ?? '';
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
        $this->image = null;
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
        $this->caption = $this->image->caption ?? '';
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
            'text' => 'Apakah Anda yakin ingin mengubah caption foto ini?',
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

        $this->image->update([
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
     * Confirm delete image
     */
    public function confirmDeleteImage(): void
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => 'Hapus Foto',
            'text' => 'Apakah Anda yakin ingin menghapus foto ini? Tindakan ini tidak dapat dibatalkan.',
            'icon' => 'warning',
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, Hapus',
            'cancelButtonText' => 'Batal',
            'confirmMethod' => 'deleteImage',
            'componentId' => $this->id
        ]);
    }

    /**
     * Delete image
     */
    public function deleteImage(): void
    {
        try {
            // Delete files from storage
            $this->imageProcessingService->delete(
                $this->image->path,
                $this->image->thumbnail_path
            );

            // Delete record from database
            $this->image->delete();

            // Emit events
            $this->emit('imageDeleted');
            $this->emit('refreshGrid');

            // Close modal
            $this->closeModal();

            $this->dispatchBrowserEvent('swal:success', [
                'title' => 'Berhasil',
                'text' => 'Gambar berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Gagal',
                'text' => 'Gagal menghapus gambar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download image
     */
    public function downloadImage()
    {
        return response()->download(
            storage_path('app/public/' . $this->image->path),
            $this->image->original_filename
        );
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.image-modal-component');
    }
}
