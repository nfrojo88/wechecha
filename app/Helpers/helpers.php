<?php

use App\Services\FileUploadService;

if (!function_exists('uploaded_asset')) {
    /**
     * Get the accessible public URL for any uploaded file or image from the centralized upload folder.
     *
     * @param string|null $path
     * @param string|null $fallback
     * @return string|null
     */
    function uploaded_asset(?string $path, ?string $fallback = null): ?string
    {
        return FileUploadService::url($path, $fallback);
    }
}
