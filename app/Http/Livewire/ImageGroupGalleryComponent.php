<?php

namespace App\Http\Livewire;

use App\Models\Image;
use App\Models\ImageGroup;
use App\Services\ImageProcessingService;
use Livewire\Component;

class ImageGroupGalleryComponent extends Component
{
    /**
     * Image group UUID
     */
    public $groupUuid;

    /**
     * Image group
     */
    public $group;

    /**
     * Selected image index
     */
    public $selectedImageIndex = 0;

    /**
     * Show gallery modal
     */
    public $showGalleryModal = false;

    /**
     * Auto open gallery when mounted
     */
    public $autoOpen = false;

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
     * Show delete confirmation for current image
     */
    public $showDeleteConfirm = false;

    /**
     * Show delete group confirmation
     */
    public $showDeleteGroupConfirm = false;

    /**
     * For deleting thumbnail image
     */
    public $imageToDelete = null;
    public $showThumbnailDeleteConfirm = false;

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
     * Image to move
     */
    public $imageToMove = null;

    /**
     * Image processing service
     */
    protected $imageProcessingService;

    /**
     * Event listeners
     */
    protected $listeners = [
        'openGalleryModal' => 'openGalleryModal',
        'displayGallery' => 'displayGallery',
    ];

    /**
     * Watchers
     */
    protected $watchers = [
        'autoOpen' => 'handleAutoOpen',
    ];

    /**
     * Mount component
     */
    public function mount($groupUuid): void
    {
        $this->groupUuid = $groupUuid;
        $this->group = ImageGroup::with('images')
            ->where('uuid', $groupUuid)
            ->firstOrFail();
        
        // Auto open modal ketika component di-mount
        // Karena component hanya di-render ketika parent sudah set showGalleryModal = true
        $this->showGalleryModal = true;
        $this->selectedImageIndex = 0;
        
        // Initialize edit values
        $this->caption = $this->group->caption ?? '';
        $this->editEventDate = $this->group->event_date?->format('Y-m-d') ?? '';
        
        // Initialize image processing service
        $this->imageProcessingService = new ImageProcessingService();
    }

    /**
     * Handle auto open watcher
     */
    public function handleAutoOpen(): void
    {
        if ($this->autoOpen) {
            $this->showGalleryModal = true;
            $this->selectedImageIndex = 0;
        }
    }

    /**
     * Open gallery modal (called from parent via event)
     */
    public function openGalleryModal(): void
    {
        $this->showGalleryModal = true;
        $this->selectedImageIndex = 0;
    }

    /**
     * Display gallery - alternative method for more reliable triggering
     */
    public function displayGallery(): void
    {
        $this->showGalleryModal = true;
        $this->selectedImageIndex = 0;
    }

    /**
     * Close gallery modal
     */
    public function closeGallery(): void
    {
        $this->showGalleryModal = false;
        
        // PENTING: Emit event ke parent untuk close container juga
        // Ini memastikan parent reset $showGalleryModal dan $selectedGroupUuid
        $this->emit('galleryModalClosed');
    }

    /**
     * Select image
     */
    public function selectImage($index): void
    {
        $this->selectedImageIndex = $index;
    }

    /**
     * Next image
     */
    public function nextImage(): void
    {
        if ($this->selectedImageIndex < $this->group->images->count() - 1) {
            $this->selectedImageIndex++;
        }
    }

    /**
     * Previous image
     */
    public function previousImage(): void
    {
        if ($this->selectedImageIndex > 0) {
            $this->selectedImageIndex--;
        }
    }

    /**
     * Get current image
     */
    public function getCurrentImageProperty()
    {
        return $this->group->images[$this->selectedImageIndex] ?? null;
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
        $this->caption = $this->group->caption ?? '';
        $this->editEventDate = $this->group->event_date?->format('Y-m-d') ?? '';
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

        $this->group->update([
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
     * Show delete confirmation for current image
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
     * Delete current image
     */
    public function deleteImage(): void
    {
        if (!$this->currentImage) {
            return;
        }

        try {
            // Delete files from storage
            $this->imageProcessingService->delete(
                $this->currentImage->path,
                $this->currentImage->thumbnail_path
            );

            // Delete record from database
            $this->currentImage->delete();

            // Update image count
            $this->group->update([
                'image_count' => $this->group->images()->count(),
            ]);

            // Refresh group data
            $this->group->refresh();

            // Adjust selected index if needed
            if ($this->selectedImageIndex >= $this->group->images->count()) {
                $this->selectedImageIndex = max(0, $this->group->images->count() - 1);
            }

            // Emit events
            $this->emit('refreshGrid');

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
     * Confirm delete thumbnail image
     */
    public function confirmDeleteThumbnail($index): void
    {
        $this->imageToDelete = $index;
        $this->showThumbnailDeleteConfirm = true;
    }

    /**
     * Hide thumbnail delete confirmation
     */
    public function hideThumbnailDeleteConfirmation(): void
    {
        $this->showThumbnailDeleteConfirm = false;
        $this->imageToDelete = null;
    }

    /**
     * Delete thumbnail image
     */
    public function deleteThumbnailImage(): void
    {
        if ($this->imageToDelete === null || !isset($this->group->images[$this->imageToDelete])) {
            return;
        }

        try {
            $image = $this->group->images[$this->imageToDelete];

            // Delete files from storage
            $this->imageProcessingService->delete(
                $image->path,
                $image->thumbnail_path
            );

            // Delete record from database
            $image->delete();

            // Update image count
            $this->group->update([
                'image_count' => $this->group->images()->count(),
            ]);

            // Refresh group data
            $this->group->refresh();

            // Adjust selected index if needed
            if ($this->selectedImageIndex >= $this->group->images->count()) {
                $this->selectedImageIndex = max(0, $this->group->images->count() - 1);
            }

            // Reset
            $this->showThumbnailDeleteConfirm = false;
            $this->imageToDelete = null;

            // Emit events
            $this->emit('refreshGrid');

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
     * Show delete group confirmation
     */
    public function showDeleteGroupConfirmation(): void
    {
        $this->showDeleteGroupConfirm = true;
    }

    /**
     * Hide delete group confirmation
     */
    public function hideDeleteGroupConfirmation(): void
    {
        $this->showDeleteGroupConfirm = false;
    }

    /**
     * Confirm delete group
     */
    public function confirmDeleteGroup(): void
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => 'Hapus Grup',
            'text' => 'Apakah Anda yakin ingin menghapus grup ini beserta semua gambar di dalamnya? Tindakan ini tidak dapat dibatalkan.',
            'icon' => 'warning',
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, Hapus',
            'cancelButtonText' => 'Batal',
            'confirmMethod' => 'deleteGroup',
            'componentId' => $this->id
        ]);
    }

    /**
     * Delete group and all images
     */
    public function deleteGroup(): void
    {
        try {
            // Delete all physical files first
            foreach ($this->group->images as $image) {
                \Illuminate\Support\Facades\Storage::delete($image->path);
                \Illuminate\Support\Facades\Storage::delete($image->thumbnail_path);
            }

            // Delete the group (cascade will delete image records)
            $this->group->delete();

            // Close modal and emit events
            $this->closeGallery();
            $this->emit('refreshGrid');

            $this->dispatchBrowserEvent('swal:success', [
                'title' => 'Berhasil',
                'text' => 'Grup dan semua gambar berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Gagal',
                'text' => 'Gagal menghapus grup: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Open share modal
     */
    public function openShareModal(): void
    {
        // Generate share URL
        $this->shareUrl = route('image-groups.show', $this->group->uuid);
        
        // Generate share text
        $caption = $this->group->caption ?? 'Lihat foto-foto ini';
        $date = $this->group->event_date->format('d M Y');
        $count = $this->group->image_count;
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
     * Open move modal
     */
    public function openMoveModal($index): void
    {
        $this->imageToMove = $index;
        
        // Get all groups except current group
        $this->availableGroups = ImageGroup::where('user_id', auth()->id())
            ->where('uuid', '!=', $this->group->uuid)
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
        $this->imageToMove = null;
    }

    /**
     * Move image to another group
     */
    public function moveImage(): void
    {
        $this->validate([
            'targetGroupId' => 'required|exists:image_groups,uuid',
        ]);

        if ($this->imageToMove === null || !isset($this->group->images[$this->imageToMove])) {
            return;
        }

        $targetGroup = ImageGroup::where('uuid', $this->targetGroupId)->first();
        
        if (!$targetGroup) {
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Error',
                'text' => 'Grup tujuan tidak ditemukan'
            ]);
            return;
        }

        $image = $this->group->images[$this->imageToMove];
        
        // Move the image
        $image->update([
            'image_group_id' => $targetGroup->uuid,
        ]);

        // Update image counts
        $this->updateImageCount($this->group);
        $this->updateImageCount($targetGroup);

        // Refresh group data
        $this->group->refresh();

        // Adjust selected index if needed
        if ($this->selectedImageIndex >= $this->group->images->count()) {
            $this->selectedImageIndex = max(0, $this->group->images->count() - 1);
        }

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
     * Render component
     */
    public function render()
    {
        return view('livewire.image-group-gallery-component');
    }
}
