<?php

namespace App\Jobs;

use BaseApi\Queue\Job;
use BaseApi\App;

class ProcessImageJob extends Job
{
    protected int $maxRetries = 5;
    protected int $retryDelay = 60; // seconds
    
    public function __construct(
        private string $imagePath,
        private array $transformations
    ) {
        // Store the image path and transformations to process
    }
    
    public function handle(): void
    {
        // Verify image exists
        $fullPath = App::storagePath($this->imagePath);
        if (!file_exists($fullPath)) {
            throw new \Exception("Image file not found: {$this->imagePath}");
        }
        
        foreach ($this->transformations as $transformation) {
            $this->applyTransformation($fullPath, $transformation);
        }
        
        error_log("Image processing completed for: {$this->imagePath}");
    }
    
    private function applyTransformation(string $path, array $transformation): void
    {
        // Image processing logic - this would typically use a library like GD or ImageMagick
        switch ($transformation['type']) {
            case 'resize':
                $this->resizeImage($path, $transformation['width'], $transformation['height']);
                break;
            case 'crop':
                $this->cropImage(
                    $path, 
                    $transformation['x'], 
                    $transformation['y'], 
                    $transformation['width'], 
                    $transformation['height']
                );
                break;
            case 'thumbnail':
                $this->createThumbnail($path, $transformation['size'] ?? 150);
                break;
            default:
                throw new \Exception("Unknown transformation type: {$transformation['type']}");
        }
    }
    
    private function resizeImage(string $path, int $width, int $height): void
    {
        // Placeholder for resize logic
        error_log("Resizing image {$path} to {$width}x{$height}");
        
        // In a real implementation, you would use GD or ImageMagick:
        // $image = imagecreatefromjpeg($path);
        // $resized = imagescale($image, $width, $height);
        // imagejpeg($resized, $path);
    }
    
    private function cropImage(string $path, int $x, int $y, int $width, int $height): void
    {
        // Placeholder for crop logic
        error_log("Cropping image {$path} to {$width}x{$height} at ({$x}, {$y})");
    }
    
    private function createThumbnail(string $path, int $size): void
    {
        // Placeholder for thumbnail creation
        $thumbnailPath = str_replace('.', '_thumb.', $path);
        error_log("Creating thumbnail {$thumbnailPath} with size {$size}x{$size}");
    }
    
    public function failed(\Throwable $exception): void
    {
        error_log("Image processing failed for {$this->imagePath}: " . $exception->getMessage());
        parent::failed($exception);
    }
}
