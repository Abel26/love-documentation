<?php

namespace App\Http\Livewire;

use App\Models\Image;
use App\Models\ImageGroup;
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
     * Event date for edit
     */
    public $editEventDate = '';

    /**
     * Show delete confirmation
     */
    public $showDeleteConfirm = false;

    /**
     * Show move modal
     */
    public $showMoveModal = false;

    /**
     * Available groups for moving
     */
    public $availableGroups = [];

    /**
     * Selected target group for moving
     */
    public $targetGroupId = null;

    /**
     * Show share modal
     */
    public $showShareModal = false;

    /**
     * Share URL
     */
    public $shareUrl = '';

    /**
     * Share text
     */
    public $shareText = '';

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
        $this->image = Image::with(['user', 'imageGroup'])->where('uuid', $imageUuid)->first();
        $this->caption = $this->image->imageGroup->caption ?? '';
        $this->editEventDate = $this->image->imageGroup->event_date?->format('Y-m-d') ?? '';
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
        $this->editEventDate = '';
        $this->isEditing = false;
        $this->showDeleteConfirm = false;
        $this->showMoveModal = false;
        $this->targetGroupId = null;
        $this->availableGroups = [];
        $this->showShareModal = false;
        $this->shareUrl = '';
        $this->shareText = '';
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
        $this->caption = $this->image->imageGroup->caption ?? '';
        $this->editEventDate = $this->image->imageGroup->event_date?->format('Y-m-d') ?? '';
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
            'title' => 'Update Grup',
            'text' => 'Apakah Anda yakin ingin mengubah caption dan tanggal kejadian grup ini?',
            'icon' => 'question',
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, Update',
            'cancelButtonText' => 'Batal',
            'confirmMethod' => 'updateGroup',
            'componentId' => $this->id
        ]);
    }

    /**
     * Update group
     */
    public function updateGroup(): void
    {
        $this->validate([
            'caption' => 'nullable|string|max:1000',
            'editEventDate' => 'required|date',
        ]);

        $this->image->imageGroup->update([
            'caption' => $this->caption,
            'event_date' => $this->editEventDate,
            'event_month' => \Carbon\Carbon::parse($this->editEventDate)->format('Y-m'),
        ]);

        $this->isEditing = false;
        
        $this->dispatchBrowserEvent('swal:success', [
            'title' => 'Berhasil',
            'text' => 'Grup berhasil diupdate'
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
     * Open move modal
     */
    public function openMoveModal(): void
    {
        // Get all groups except current group
        $currentGroupId = $this->image->imageGroup->uuid;
        $this->availableGroups = ImageGroup::where('user_id', auth()->id())
            ->where('uuid', '!=', $currentGroupId)
            ->orderBy('event_date', 'desc')
            ->get();
        
        $this->showMoveModal = true;
    }

    /**
     * Close move modal
     */
    public function closeMoveModal(): void
    {
        $this->showMoveModal = false;
        $this->targetGroupId = null;
        $this->availableGroups = [];
    }

    /**
     * Move image to another group
     */
    public function moveImage(): void
    {
        $this->validate([
            'targetGroupId' => 'required|exists:image_groups,uuid',
        ]);

        $targetGroup = ImageGroup::where('uuid', $this->targetGroupId)->first();
        
        if (!$targetGroup) {
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Error',
                'text' => 'Grup tujuan tidak ditemukan'
            ]);
            return;
        }

        // Move the image
        $this->image->update([
            'image_group_id' => $targetGroup->uuid,
        ]);

        // Update image counts
        $this->updateImageCount($this->image->imageGroup);
        $this->updateImageCount($targetGroup);

        $this->closeMoveModal();
        $this->emit('refreshGrid');

        $this->dispatchBrowserEvent('swal:success', [
            'title' => 'Berhasil',
            'text' => 'Gambar berhasil dipindahkan'
        ]);
    }

    /**
     * Update image count for a group
     */
    protected function updateImageCount(ImageGroup $group): void
    {
        $group->update([
            'image_count' => $group->images()->count(),
        ]);
    }

    /**
     * Open share modal
     */
    public function openShareModal(): void
    {
        $group = $this->image->imageGroup;
        
        // Generate share URL (gunakan URL publik jika ada, atau URL internal)
        $this->shareUrl = route('image-groups.show', $group->uuid);
        
        // Generate share text
        $caption = $group->caption ?? 'Lihat foto-foto ini';
        $date = $group->event_date->format('d M Y');
        $count = $group->image_count;
        $this->shareText = "{$caption} ({$date}) - {$count} foto";
        
        $this->showShareModal = true;
    }

    /**
     * Close share modal
     */
    public function closeShareModal(): void
    {
        $this->showShareModal = false;
        $this->shareUrl = '';
        $this->shareText = '';
    }

    /**
     * Copy share URL to clipboard
     */
    public function copyShareUrl(): void
    {
        $this->dispatchBrowserEvent('copyToClipboard', [
            'text' => $this->shareUrl
        ]);
        
        $this->dispatchBrowserEvent('swal:success', [
            'title' => 'Berhasil',
            'text' => 'URL berhasil disalin ke clipboard'
        ]);
    }

    /**
     * Share to WhatsApp
     */
    public function shareToWhatsApp(): void
    {
        $encodedText = urlencode($this->shareText);
        $encodedUrl = urlencode($this->shareUrl);
        $whatsappUrl = "https://wa.me/?text={$encodedText}%20{$encodedUrl}";
        
        $this->dispatchBrowserEvent('openInNewTab', ['url' => $whatsappUrl]);
    }

    /**
     * Share to Facebook
     */
    public function shareToFacebook(): void
    {
        $encodedUrl = urlencode($this->shareUrl);
        $facebookUrl = "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}";
        
        $this->dispatchBrowserEvent('openInNewTab', ['url' => $facebookUrl]);
    }

    /**
     * Share to Twitter
     */
    public function shareToTwitter(): void
    {
        $encodedText = urlencode($this->shareText);
        $encodedUrl = urlencode($this->shareUrl);
        $twitterUrl = "https://twitter.com/intent/tweet?text={$encodedText}&url={$encodedUrl}";
        
        $this->dispatchBrowserEvent('openInNewTab', ['url' => $twitterUrl]);
    }

    /**
     * Share to Telegram
     */
    public function shareToTelegram(): void
    {
        $encodedText = urlencode($this->shareText);
        $encodedUrl = urlencode($this->shareUrl);
        $telegramUrl = "https://t.me/share/url?url={$encodedUrl}&text={$encodedText}";
        
        $this->dispatchBrowserEvent('openInNewTab', ['url' => $telegramUrl]);
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.image-modal-component');
    }
}
