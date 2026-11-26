<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;

class ImageService
{
    public function storeCover(UploadedFile $file, string $directory): string
    {
        $extension = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => throw new RuntimeException('Only JPG and PNG images can be stored.'),
        };

        $filename = Str::uuid().'.'.$extension;
        $path = trim($directory, '/').'/'.$filename;

        if (! extension_loaded('gd')) {
            $storedPath = $file->storePubliclyAs(trim($directory, '/'), $filename, 'public');

            if ($storedPath === false) {
                throw new RuntimeException('The uploaded image could not be stored.');
            }

            return $storedPath;
        }

        $image = (new ImageManager(new Driver))
            ->read($file->getRealPath())
            ->cover(500, 500);
        $encodedImage = $image->encodeByExtension($extension)->toString();

        if (! Storage::disk('public')->put($path, $encodedImage)) {
            throw new RuntimeException('The processed image could not be stored.');
        }

        return $path;
    }
}
