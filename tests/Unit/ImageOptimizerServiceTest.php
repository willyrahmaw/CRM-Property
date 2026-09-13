<?php

namespace Tests\Unit;

use App\Services\ImageOptimizerService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizerServiceTest extends TestCase
{
    public function test_converts_jpeg_to_webp(): void
    {
        Storage::fake('public');

        $service = new ImageOptimizerService();
        $file = UploadedFile::fake()->image('test_house.jpg', 640, 480);

        $path = $service->convertToWebp($file, 'test/images', 'public', 80, 1920);

        $this->assertStringEndsWith('.webp', $path);
        $this->assertTrue(Storage::disk('public')->exists($path));

        $contents = Storage::disk('public')->get($path);
        // Cek header magic bytes WebP: 'RIFF....WEBP'
        $this->assertStringStartsWith('RIFF', $contents);
        $this->assertStringContainsString('WEBP', substr($contents, 8, 4));
    }

    public function test_resizes_large_image_exceeding_max_dimension(): void
    {
        Storage::fake('public');

        $service = new ImageOptimizerService();
        // Buat gambar berukuran besar 3000x2000
        $file = UploadedFile::fake()->image('giant_view.jpg', 3000, 2000);

        $path = $service->convertToWebp($file, 'test/images', 'public', 80, 1200);

        $this->assertStringEndsWith('.webp', $path);
        $this->assertTrue(Storage::disk('public')->exists($path));

        $contents = Storage::disk('public')->get($path);
        $image = imagecreatefromstring($contents);
        $this->assertNotFalse($image);

        // Lebar maksimal harus di-scale down menjadi 1200
        $this->assertEquals(1200, imagesx($image));
        $this->assertEquals(800, imagesy($image));
    }
}
