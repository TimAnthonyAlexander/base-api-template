<?php

namespace App\Jobs;

use Override;
use Exception;
use Throwable;
use BaseApi\Queue\Job;
use BaseApi\App;

class ProcessImageJob extends Job
{
    protected int $maxRetries = 5;

    protected int $retryDelay = 60; // seconds

    /**
     * @param array<int, array<string, mixed>> $transformations
     */
    public function __construct(
        private readonly string $imagePath,
        private readonly array $transformations
    ) {
        // Store the image path and transformations to process
    }

    #[Override]
    public function handle(): void
    {
        // Verify image exists
        $fullPath = App::storagePath($this->imagePath);
        if (!file_exists($fullPath)) {
            throw new Exception('Image file not found: ' . $this->imagePath);
        }

        foreach ($this->transformations as $transformation) {
            $this->applyTransformation($fullPath, $transformation);
        }

        error_log('Image processing completed for: ' . $this->imagePath);
    }

    /**
     * @param array<string, mixed> $transformation
     */
    private function applyTransformation(string $path, array $transformation): void
    {
        // Image processing logic - this would typically use a library like GD or ImageMagick
        match ($transformation['type']) {
            'resize' => $this->resizeImage($path, $transformation['width'], $transformation['height']),
            'crop' => $this->cropImage(
                $path, 
                $transformation['x'], 
                $transformation['y'], 
                $transformation['width'], 
                $transformation['height']
            ),
            'thumbnail' => $this->createThumbnail($path, $transformation['size'] ?? 150),
            default => throw new Exception('Unknown transformation type: ' . $transformation['type']),
        };
    }

    private function resizeImage(string $path, int $width, int $height): void
    {
        // Placeholder for resize logic
        error_log(sprintf('Resizing image %s to %dx%d', $path, $width, $height));

        // In a real implementation, you would use GD or ImageMagick:
        // $image = imagecreatefromjpeg($path);
        // $resized = imagescale($image, $width, $height);
        // imagejpeg($resized, $path);
    }

    private function cropImage(string $path, int $x, int $y, int $width, int $height): void
    {
        // Placeholder for crop logic
        error_log(sprintf('Cropping image %s to %dx%d at (%d, %d)', $path, $width, $height, $x, $y));
    }

    private function createThumbnail(string $path, int $size): void
    {
        // Placeholder for thumbnail creation
        $thumbnailPath = str_replace('.', '_thumb.', $path);
        error_log(sprintf('Creating thumbnail %s with size %dx%d', $thumbnailPath, $size, $size));
    }

    #[Override]
    public function failed(Throwable $throwable): void
    {
        error_log(sprintf('Image processing failed for %s: ', $this->imagePath) . $throwable->getMessage());
        parent::failed($throwable);
    }
}
