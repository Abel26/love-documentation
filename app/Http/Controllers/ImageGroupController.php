<?php

namespace App\Http\Controllers;

use App\Models\ImageGroup;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageGroupController extends Controller
{
    /**
     * Download all images in a group as ZIP
     */
    public function downloadAsZip(string $uuid): StreamedResponse
    {
        $group = ImageGroup::where('uuid', $uuid)->firstOrFail();

        if ($group->images->isEmpty()) {
            abort(404, 'Tidak ada gambar dalam grup ini');
        }

        // Generate ZIP filename
        $zipFilename = 'images_' . Str::slug($group->caption ?? $group->uuid) . '_' . $group->event_date->format('Y-m-d') . '.zip';

        return response()->stream(function () use ($group) {
            $zip = new \ZipArchive();
            $tempFile = tempnam(sys_get_temp_dir(), 'zip');

            if ($zip->open($tempFile, \ZipArchive::CREATE) !== true) {
                abort(500, 'Gagal membuat file ZIP');
            }

            foreach ($group->images as $image) {
                // Use public disk to get the file content
                $fileContent = Storage::disk('public')->get($image->path);
                if ($fileContent !== false) {
                    $zip->addFromString($image->original_filename, $fileContent);
                }
            }

            $zip->close();

            // Output ZIP file
            readfile($tempFile);

            // Delete temp file
            unlink($tempFile);
        }, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $zipFilename . '"',
        ]);
    }

    /**
     * Show image group details (public view)
     */
    public function show(string $uuid)
    {
        $group = ImageGroup::with(['images', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();
        
        return view('image-groups.show', compact('group'));
    }
}
