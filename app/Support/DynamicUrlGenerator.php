<?php

namespace App\Support;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class DynamicUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        $baseUrl = request()->getSchemeAndHttpHost();
        
        // Force HTTPS for ngrok URLs
        if (str_contains($baseUrl, 'ngrok')) {
            $baseUrl = str_replace('http://', 'https://', $baseUrl);
        }
        
        $path = $this->getPathRelativeToRoot();
        
        // Ensure the path starts with 'storage/' for public disk
        if (!str_starts_with($path, 'storage/')) {
            $path = 'storage/' . $path;
        }
        
        return $baseUrl . '/' . $path;
    }
}
