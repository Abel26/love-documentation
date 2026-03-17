<?php
  
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ImageGroupController;
use App\Http\Controllers\CloudDashboardController;
use App\Http\Livewire\VideoIndexComponent;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Super Admin Routes
Route::middleware(['auth', 'super_admin'])->group(function () {
    // Cloud Dashboard Routes
    Route::get('/cloud-dashboard', [CloudDashboardController::class, 'index'])->name('cloud-dashboard.index');
    Route::get('/cloud-dashboard/stats', [CloudDashboardController::class, 'getStorageStats'])->name('cloud-dashboard.stats');
    Route::post('/cloud-dashboard/check-capacity', [CloudDashboardController::class, 'checkUploadCapacity'])->name('cloud-dashboard.check-capacity');
    Route::post('/cloud-dashboard/refresh-cache', [CloudDashboardController::class, 'refreshCache'])->name('cloud-dashboard.refresh-cache');
    
    // Image Gallery Routes
    Route::get('/images', [ImageController::class, 'index'])->name('images.index');
    // DataTables AJAX endpoint
    Route::get('/livewire/images-table', [App\Http\Livewire\ImageIndexComponent::class, 'getImagesData'])->name('livewire.images-table');
    // Download group images as ZIP
    Route::get('/image-groups/{uuid}/download', [ImageGroupController::class, 'downloadAsZip'])->name('image-groups.download');
    
    // Video Gallery Routes
    Route::get('/videos', VideoIndexComponent::class)->name('videos.index');
    // DataTables AJAX endpoint for videos
    Route::get('/livewire/videos-table', [VideoIndexComponent::class, 'getVideosData'])->name('livewire.videos-table');
});

// Public view for shared groups
Route::get('/image-groups/{uuid}', [ImageGroupController::class, 'show'])->name('image-groups.show');

require __DIR__.'/auth.php';
