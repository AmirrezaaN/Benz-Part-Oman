<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Re-encodes every uploaded image with GD. This strips EXIF/metadata and any
 * payload hidden inside the file (polyglot uploads), and resizes large photos.
 */
class ImageService
{
    public function store(UploadedFile $file, string $directory, int $maxSize = 1600, int $quality = 82): string
    {
        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));
        if (! $source) {
            throw new RuntimeException('Invalid image.');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, $maxSize / max($width, $height));
        $newW = (int) round($width * $scale);
        $newH = (int) round($height * $scale);

        $canvas = imagecreatetruecolor($newW, $newH);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);

        ob_start();
        imagewebp($canvas, null, $quality);
        $binary = ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        $path = trim($directory, '/').'/'.now()->format('Y/m').'/'.Str::random(32).'.webp';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
