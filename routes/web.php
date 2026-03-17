<?php

use App\Http\Controllers\ProfileController;
use App\Http\Livewire\ImageIndexComponent;
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
    // Image Gallery Routes
    Route::get('/images', ImageIndexComponent::class)->name('images.index');
    // DataTables AJAX endpoint
    Route::get('/livewire/images-table', [ImageIndexComponent::class, 'getImagesData'])->name('livewire.images-table');
    
    // Video Gallery Routes
    Route::get('/videos', VideoIndexComponent::class)->name('videos.index');
    // DataTables AJAX endpoint for videos
    Route::get('/livewire/videos-table', [VideoIndexComponent::class, 'getVideosData'])->name('livewire.videos-table');
});

require __DIR__.'/auth.php';
