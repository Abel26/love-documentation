<?php

namespace App\Http\Livewire;

use App\Models\Image;
use App\Models\MemoryQuote;
use Livewire\Component;

class MemoryOfTheDayComponent extends Component
{
    // Properties
    public $todayMemory;
    public $memoryDate;
    public $memoryCaption;
    public $memoryImage;
    public $memoryQuote;
    public $showPrevious = false;
    public $showNext = false;
    public $previousDate;
    public $nextDate;

    /**
     * Mount the component
     */
    public function mount()
    {
        $this->loadTodayMemory();
    }

    /**
     * Load today's memory
     */
    public function loadTodayMemory()
    {
        // Get today's date
        $today = now();
        $this->memoryDate = $today;

        // Try to find an image from the same date in previous years
        $sameDateImage = Image::where('user_id', auth()->id())
            ->whereDay('upload_date', $today->day)
            ->whereMonth('upload_date', $today->month)
            ->inRandomOrder()
            ->first();

        if ($sameDateImage) {
            $this->memoryImage = $sameDateImage;
            $this->memoryCaption = $sameDateImage->imageGroup->caption ?? 'Kenangan spesial di tanggal ini';
        } else {
            // Get a random image if no image from same date
            $this->memoryImage = Image::where('user_id', auth()->id())
                ->inRandomOrder()
                ->first();
            
            if ($this->memoryImage) {
                $this->memoryCaption = $this->memoryImage->imageGroup->caption ?? 'Kenangan random hari ini';
            } else {
                $this->memoryCaption = 'Belum ada kenangan. Upload foto pertamamu sekarang!';
            }
        }

        // Get daily quote
        $this->memoryQuote = MemoryQuote::getDailyQuote();

        // Check if there are previous/next memories
        $this->checkNavigation();
    }

    /**
     * Check if there are previous/next memories
     */
    public function checkNavigation()
    {
        $allImages = Image::where('user_id', auth()->id())
            ->orderBy('upload_date', 'asc')
            ->get();

        if ($this->memoryImage) {
            $currentIndex = $allImages->search(function ($item) {
                return $item->id === $this->memoryImage->id;
            });

            $this->showPrevious = $currentIndex > 0;
            $this->showNext = $currentIndex < $allImages->count() - 1;

            if ($this->showPrevious) {
                $this->previousDate = $allImages[$currentIndex - 1]->upload_date;
            }

            if ($this->showNext) {
                $this->nextDate = $allImages[$currentIndex + 1]->upload_date;
            }
        }
    }

    /**
     * Load previous memory
     */
    public function loadPreviousMemory()
    {
        if ($this->previousDate) {
            $previousImage = Image::where('user_id', auth()->id())
                ->whereDate('upload_date', $this->previousDate)
                ->first();

            if ($previousImage) {
                $this->memoryImage = $previousImage;
                $this->memoryCaption = $previousImage->imageGroup->caption ?? 'Kenangan sebelumnya';
                $this->memoryDate = $previousImage->upload_date;
                $this->checkNavigation();
            }
        }
    }

    /**
     * Load next memory
     */
    public function loadNextMemory()
    {
        if ($this->nextDate) {
            $nextImage = Image::where('user_id', auth()->id())
                ->whereDate('upload_date', $this->nextDate)
                ->first();

            if ($nextImage) {
                $this->memoryImage = $nextImage;
                $this->memoryCaption = $nextImage->imageGroup->caption ?? 'Kenangan selanjutnya';
                $this->memoryDate = $nextImage->upload_date;
                $this->checkNavigation();
            }
        }
    }

    /**
     * Load random memory
     */
    public function loadRandomMemory()
    {
        $randomImage = Image::where('user_id', auth()->id())
            ->inRandomOrder()
            ->first();

        if ($randomImage) {
            $this->memoryImage = $randomImage;
            $this->memoryCaption = $randomImage->imageGroup->caption ?? 'Kenangan random';
            $this->memoryDate = $randomImage->upload_date;
            $this->checkNavigation();
        }
    }

    /**
     * Share memory to WhatsApp
     */
    public function shareToWhatsApp()
    {
        if ($this->memoryImage) {
            $message = urlencode("💕 {$this->memoryCaption}\n\nKenangan hari ini: {$this->memoryDate->format('d M Y')}");
            $imageUrl = asset('storage/' . $this->memoryImage->path);
            $whatsappUrl = "https://wa.me/?text={$message}&url={$imageUrl}";
            
            return redirect()->away($whatsappUrl);
        }
    }

    /**
     * Copy link to clipboard
     */
    public function copyLink()
    {
        if ($this->memoryImage) {
            $url = asset('storage/' . $this->memoryImage->path);
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Link berhasil disalin!'
            ]);
        }
    }

    /**
     * Download memory
     */
    public function downloadMemory()
    {
        if ($this->memoryImage) {
            return response()->download(storage_path('app/public/' . $this->memoryImage->path));
        }
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.memory-of-the-day-component');
    }
}
