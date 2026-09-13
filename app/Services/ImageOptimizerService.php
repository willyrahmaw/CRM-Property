<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerService
{
    /**
     * Compress and convert an uploaded image to WebP format, then store it.
     *
     * @param UploadedFile $file The uploaded image file
     * @param string $directory Target directory on the disk (e.g. 'properties/clusters')
     * @param string $disk Storage disk name (default 'public')
     * @param int $quality WebP compression quality (0-100, default 80)
     * @param int $maxDimension Maximum width or height in pixels (default 1920)
     * @return string Relative file path on the disk (e.g. 'properties/clusters/uuid.webp')
     */
    public function convertToWebp(
        UploadedFile $file,
        string $directory = 'properties/clusters',
        string $disk = 'public',
        int $quality = 80,
        int $maxDimension = 1920
    ): string {
        // Fallback jika GD atau imagewebp tidak tersedia di environment server
        if (! extension_loaded('gd') || ! function_exists('imagewebp') || ! function_exists('imagecreatefromstring')) {
            return $file->store($directory, $disk);
        }

        try {
            $realPath = $file->getRealPath();
            if (! $realPath || ! file_exists($realPath)) {
                return $file->store($directory, $disk);
            }

            $rawContents = file_get_contents($realPath);
            if ($rawContents === false) {
                return $file->store($directory, $disk);
            }

            $sourceImage = @imagecreatefromstring($rawContents);
            if (! $sourceImage) {
                return $file->store($directory, $disk);
            }

            $origWidth = imagesx($sourceImage);
            $origHeight = imagesy($sourceImage);

            if ($origWidth <= 0 || $origHeight <= 0) {
                return $file->store($directory, $disk);
            }

            // Hitung skala jika dimensi melebihi batas maksimal (pertahankan aspect ratio)
            if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
                $ratio = min($maxDimension / $origWidth, $maxDimension / $origHeight);
                $targetWidth = (int) max(1, round($origWidth * $ratio));
                $targetHeight = (int) max(1, round($origHeight * $ratio));

                $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

                // Pertahankan alpha transparency untuk PNG/WebP transparan
                imagealphablending($targetImage, false);
                imagesavealpha($targetImage, true);

                imagecopyresampled(
                    $targetImage,
                    $sourceImage,
                    0, 0, 0, 0,
                    $targetWidth,
                    $targetHeight,
                    $origWidth,
                    $origHeight
                );
            } else {
                $targetImage = $sourceImage;
                imagealphablending($targetImage, false);
                imagesavealpha($targetImage, true);
            }

            // Kompresi ke format WebP dalam memory buffer
            ob_start();
            $success = imagewebp($targetImage, null, $quality);
            $webpData = ob_get_clean();

            if (! $success || empty($webpData)) {
                return $file->store($directory, $disk);
            }

            // Simpan file WebP dengan nama UUID acak yang aman
            $filename = Str::uuid() . '.webp';
            $relativePath = trim($directory, '/') . '/' . $filename;

            Storage::disk($disk)->put($relativePath, $webpData);

            return $relativePath;
        } catch (\Throwable $e) {
            Log::warning('Gagal mengompresi gambar ke WebP, menggunakan fallback: ' . $e->getMessage());
            return $file->store($directory, $disk);
        }
    }
}
