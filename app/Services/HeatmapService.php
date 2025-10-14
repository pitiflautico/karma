<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class HeatmapService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Generate a thermal heatmap from an image
     *
     * @param string $imagePath Path to the original image in storage
     * @return string Path to the generated heatmap image
     */
    public function generateThermalHeatmap(string $imagePath): string
    {
        try {
            // Get the full path to the image
            $fullPath = Storage::disk('public')->path($imagePath);

            \Log::info('Generating heatmap', ['path' => $fullPath]);

            // Load the image
            $image = $this->manager->read($fullPath);

            // Convert to grayscale first (based on luminosity)
            $image->greyscale();

            // Apply contrast to enhance thermal effect
            $image->contrast(30);

            // Apply colorize effect to create thermal appearance
            // This creates a base thermal look
            $image->colorize(50, -20, -50);

            // Generate output path
            $pathInfo = pathinfo($imagePath);
            $heatmapPath = $pathInfo['dirname'] . '/heatmap_' . $pathInfo['basename'];

            // Save the heatmap
            $heatmapFullPath = Storage::disk('public')->path($heatmapPath);
            $image->save($heatmapFullPath);

            \Log::info('Heatmap generated successfully', ['heatmap_path' => $heatmapPath]);

            return $heatmapPath;
        } catch (\Exception $e) {
            \Log::error('Error generating heatmap', [
                'error' => $e->getMessage(),
                'path' => $imagePath
            ]);
            throw $e;
        }
    }

    /**
     * Convert grayscale value (0-255) to thermal color
     * Uses a color gradient: blue -> cyan -> green -> yellow -> red
     *
     * @param int $gray Grayscale value (0-255)
     * @return string Hex color code
     */
    protected function grayscaleToThermal(int $gray): string
    {
        // Normalize to 0-1
        $value = $gray / 255;

        $r = 0;
        $g = 0;
        $b = 0;

        if ($value < 0.2) {
            // Blue to Cyan (0.0 - 0.2)
            $t = $value / 0.2;
            $r = 0;
            $g = (int)($t * 255);
            $b = 255;
        } elseif ($value < 0.4) {
            // Cyan to Green (0.2 - 0.4)
            $t = ($value - 0.2) / 0.2;
            $r = 0;
            $g = 255;
            $b = (int)(255 * (1 - $t));
        } elseif ($value < 0.6) {
            // Green to Yellow (0.4 - 0.6)
            $t = ($value - 0.4) / 0.2;
            $r = (int)($t * 255);
            $g = 255;
            $b = 0;
        } elseif ($value < 0.8) {
            // Yellow to Orange (0.6 - 0.8)
            $t = ($value - 0.6) / 0.2;
            $r = 255;
            $g = (int)(255 * (1 - $t * 0.5));
            $b = 0;
        } else {
            // Orange to Red (0.8 - 1.0)
            $t = ($value - 0.8) / 0.2;
            $r = 255;
            $g = (int)(127 * (1 - $t));
            $b = 0;
        }

        // Convert RGB to hex
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
