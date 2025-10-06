<?php

namespace App\Support;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class DynamicUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        $baseUrl = request()->getSchemeAndHttpHost();
        $path = $this->getPathRelativeToRoot();
        
        // Ensure the path starts with 'storage/' for public disk
        if (!str_starts_with($path, 'storage/')) {
            $path = 'storage/' . $path;
        }
        
        return $baseUrl . '/' . $path;
    }
}
